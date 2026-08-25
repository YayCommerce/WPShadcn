<?php

namespace Shadcn\BlockSettings\LayoutPicker;

use Shadcn\Traits\SingletonTrait;

/**
 * Layout picker for WooCommerce blocks that cannot be rearranged by hand.
 *
 * WordPress has no pattern picker for these blocks. The native "Replace" flow
 * lives on the core/template-part block, and the two blocks handled here are
 * never placed as a template part: the Mini-Cart is rendered server side by
 * WooCommerce, and the Checkout sits inline in the page template. So the theme
 * supplies its own picker - a toolbar button on the block that opens a modal of
 * layouts.
 *
 * Layout markup comes from the theme's `patterns/` directory. Those patterns are
 * registered with `Inserter: no`, and the editor's `getBlockPatterns()` selector
 * omits non-inserter patterns, so the markup is handed to the script from PHP
 * instead of being fetched client side.
 *
 * A family is one block plus the pattern prefix that supplies its layouts. Both
 * families share the picker because the hard part - keeping the modal outside
 * the canvas iframe, never nesting dialogs, previewing blocks that render from
 * the Store API - is identical for both.
 */
class Caller {

	use SingletonTrait;

	protected function __construct() {
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
	}

	/**
	 * Describe every block that gets a layout picker.
	 *
	 * `preview` names the schematic the script draws on the layout cards; each
	 * family needs its own because the layout CSS targets a different DOM.
	 *
	 * @return array
	 */
	private function get_families() {
		return array(
			array(
				'key'           => 'mini-cart',
				'targetBlock'   => 'woocommerce/mini-cart-contents',
				'patternPrefix' => 'shadcn/mini-cart-',
				'preview'       => 'mini-cart',
				'modalTitle'    => __( 'Choose a mini cart layout', 'shadcn' ),
				'intro'         => __( 'Applying a layout replaces the contents of the Mini-Cart drawer.', 'shadcn' ),
				/* translators: %s: layout name. */
				'confirm'       => __( 'Replace the Mini-Cart contents with "%s"? Changes you made to this template part will be lost.', 'shadcn' ),
			),
			array(
				'key'           => 'checkout',
				'targetBlock'   => 'woocommerce/checkout',
				'patternPrefix' => 'shadcn/checkout-',
				'preview'       => 'checkout',
				'modalTitle'    => __( 'Choose a checkout layout', 'shadcn' ),
				'intro'         => __( 'Applying a layout rearranges the checkout columns and sections. Customer fields and payment options are unchanged.', 'shadcn' ),
				/* translators: %s: layout name. */
				'confirm'       => __( 'Apply the "%s" checkout layout? Changes you made to this template will be lost.', 'shadcn' ),
			),
		);
	}

	/**
	 * Read the attributes a layout pattern states on its root block.
	 *
	 * Deliberately taken from `parse_blocks()`, which reports only what the
	 * block delimiter actually contains. The editor's own parser would fill in
	 * every registered default as well, and the Checkout block registers a dozen
	 * store settings client side - company field, phone requirements, order
	 * notes. Copying that whole set onto the block would quietly reset the
	 * merchant's configuration every time they changed layout.
	 *
	 * `className` is dropped: the picker computes it from the pattern slug so
	 * that classes the user added by hand survive.
	 *
	 * @param string $content Pattern markup.
	 * @return array
	 */
	private static function get_root_attributes( $content ) {
		$blocks = parse_blocks( $content );

		if ( empty( $blocks[0]['attrs'] ) || ! is_array( $blocks[0]['attrs'] ) ) {
			return array();
		}

		$attributes = $blocks[0]['attrs'];

		unset( $attributes['className'] );

		return $attributes;
	}

	/**
	 * Collect a family's layouts from the registered block patterns.
	 *
	 * The layout class is derived from the pattern slug rather than parsed out
	 * of the markup, so the two can never drift: `shadcn/checkout-summary-left`
	 * always pairs with `is-checkout-summary-left`.
	 *
	 * @param string $prefix Pattern name prefix that marks the family.
	 * @return array List of layouts, each with key, title, className and content.
	 */
	private function get_layouts( $prefix ) {
		$layouts  = array();
		$patterns = \WP_Block_Patterns_Registry::get_instance()->get_all_registered();

		foreach ( $patterns as $pattern ) {
			if ( empty( $pattern['name'] ) || 0 !== strpos( $pattern['name'], $prefix ) ) {
				continue;
			}

			$key     = substr( $pattern['name'], strlen( 'shadcn/' ) );
			$content = isset( $pattern['content'] ) ? $pattern['content'] : '';

			$layouts[] = array(
				'key'        => $key,
				'title'      => isset( $pattern['title'] ) ? $pattern['title'] : $key,
				'className'  => 'is-' . $key,
				'content'    => $content,
				'attributes' => self::get_root_attributes( $content ),
			);
		}

		usort(
			$layouts,
			static function ( $a, $b ) {
				return strcmp( $a['key'], $b['key'] );
			}
		);

		return $layouts;
	}

	public function enqueue_editor_assets() {
		if ( ! class_exists( '\WP_Block_Patterns_Registry' ) ) {
			return;
		}

		$families = array();

		foreach ( $this->get_families() as $family ) {
			$family['layouts'] = $this->get_layouts( $family['patternPrefix'] );

			// A family with no patterns installed contributes no toolbar button.
			if ( ! empty( $family['layouts'] ) ) {
				$families[] = $family;
			}
		}

		if ( empty( $families ) ) {
			return;
		}

		$version = wp_get_theme()->get( 'Version' );

		wp_enqueue_style(
			'shadcn/layout-picker',
			get_template_directory_uri() . '/inc/BlockSettings/LayoutPicker/style.css',
			array( 'wp-components' ),
			$version
		);

		wp_enqueue_script(
			'shadcn/layout-picker',
			get_template_directory_uri() . '/inc/BlockSettings/LayoutPicker/script.js',
			array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-compose', 'wp-data', 'wp-element', 'wp-hooks', 'wp-i18n', 'wp-plugins' ),
			$version,
			true
		);

		wp_add_inline_script(
			'shadcn/layout-picker',
			'window.shadcnLayoutPickers = ' . wp_json_encode( array( 'families' => $families ) ) . ';',
			'before'
		);
	}
}

Caller::get_instance();
