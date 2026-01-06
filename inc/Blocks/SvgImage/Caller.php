<?php
/**
 * SVG Image Block Variation
 *
 * Registers an "SVG Image" variation of core/image that renders inline SVG.
 *
 * @package Shadcn
 * @since 1.0.5
 */

namespace Shadcn\Blocks\SvgImage;

use Shadcn\Traits\SingletonTrait;

class Caller {
	use SingletonTrait;

	protected function __construct() {
		add_filter( 'block_type_metadata_settings', array( $this, 'declare_attribute' ), 10 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_scripts' ) );
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_block_styles' ) );
		add_filter( 'render_block_core/image', array( $this, 'render_svg_image' ), 10, 2 );
	}

	/**
	 * Add svgCode attribute to core/image block.
	 * 
	 * SVG code should have fill or stroke as currentColor to make sure it works with the theme color palette.
	 *
	 * @param array $settings Block settings.
	 * @return array Modified settings.
	 */
	public function declare_attribute( $settings ) {
		if ( empty( $settings['name'] ) || 'core/image' !== $settings['name'] ) {
			return $settings;
		}

		if ( ! empty( $settings['attributes'] ) ) {
			$settings['attributes']['svgCode'] = array(
				'type'    => 'string',
				'default' => '',
			);
			$settings['attributes']['svgColor'] = array(
				'type'    => 'string',
				'default' => '',
			);
			$settings['attributes']['svgSize'] = array(
				'type'    => 'string',
				'default' => '',
			);
		}

		return $settings;
	}

	/**
	 * Enqueue editor scripts for SVG Image variation.
	 */
	public function enqueue_editor_scripts() {
		wp_enqueue_script(
			'shadcn/svg-image',
			get_template_directory_uri() . '/inc/Blocks/SvgImage/script.js',
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-compose', 'wp-hooks', 'wp-i18n' ),
			wp_get_theme()->get( 'Version' ),
			true
		);
	}

	/**
	 * Enqueue block styles for both editor and frontend.
	 */
	public function enqueue_block_styles() {
		$css = '
			.shadcn-svg-image { display: inline-flex; justify-content: center; align-items: center; }
		';

		// Editor-only preview styles.
		if ( is_admin() ) {
			$css .= '
				.shadcn-svg-preview__content { display: flex; justify-content: center; align-items: center; }
			';
		}

		wp_register_style( 'shadcn/svg-image', false );
		wp_enqueue_style( 'shadcn/svg-image' );
		wp_add_inline_style( 'shadcn/svg-image', $css );
	}

	/**
	 * Render SVG image on frontend.
	 *
	 * @param string $content Block content.
	 * @param array  $block   Block data.
	 * @return string Modified content.
	 */
	public function render_svg_image( $content, $block ) {
		$svg_code = $block['attrs']['svgCode'] ?? '';

		if ( empty( $svg_code ) ) {
			return $content;
		}

		$sanitized_svg = $this->sanitize_svg( $svg_code );

		if ( empty( $sanitized_svg ) ) {
			return $content;
		}

		// Get alignment class if set.
		$align       = $block['attrs']['align'] ?? '';
		$align_class = $align ? ' align' . $align : '';

		// Build inline styles.
		$styles = array();

		// Get size if set.
		$svg_size = $block['attrs']['svgSize'] ?? '';
		if ( $svg_size ) {
			$styles[] = 'width: ' . esc_attr( $svg_size );
		}

		// Get color if set.
		$svg_color = $block['attrs']['svgColor'] ?? '';
		if ( $svg_color ) {
			$styles[] = 'color: ' . esc_attr( $svg_color );
		}

		$style_attr = ! empty( $styles ) ? ' style="' . implode( '; ', $styles ) . ';"' : '';

		return sprintf(
			'<figure class="wp-block-image shadcn-svg-image%s"%s>%s</figure>',
			esc_attr( $align_class ),
			$style_attr,
			$sanitized_svg
		);
	}

	/**
	 * Sanitize SVG code using wp_kses with allowed SVG tags.
	 *
	 * @param string $svg Raw SVG code.
	 * @return string Sanitized SVG.
	 */
	public function sanitize_svg( $svg ) {
		$allowed_tags = $this->get_allowed_svg_tags();
		return wp_kses( $svg, $allowed_tags );
	}

	/**
	 * Get allowed SVG tags and attributes for wp_kses.
	 *
	 * @return array Allowed tags and attributes.
	 */
	private function get_allowed_svg_tags() {
		return array(
			'svg'      => array(
				'xmlns'       => true,
				'viewbox'     => true,
				'width'       => true,
				'height'      => true,
				'fill'        => true,
				'class'       => true,
				'aria-hidden' => true,
				'aria-label'  => true,
				'role'        => true,
				'focusable'   => true,
				'id'          => true,
				'stroke'       => true,
				'stroke-width' => true,
			),
			'path'     => array(
				'd'            => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
				'class'        => true,
				'opacity'      => true,
				'fill-rule'    => true,
				'clip-rule'    => true,
				'id'           => true,
			),
			'circle'   => array(
				'cx'           => true,
				'cy'           => true,
				'r'            => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
				'class'        => true,
				'opacity'      => true,
				'id'           => true,
			),
			'rect'     => array(
				'x'            => true,
				'y'            => true,
				'width'        => true,
				'height'       => true,
				'rx'           => true,
				'ry'           => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
				'class'        => true,
				'opacity'      => true,
				'id'           => true,
			),
			'line'     => array(
				'x1'           => true,
				'y1'           => true,
				'x2'           => true,
				'y2'           => true,
				'stroke'       => true,
				'stroke-width' => true,
				'class'        => true,
			),
			'polyline' => array(
				'points'       => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
				'class'        => true,
			),
			'polygon'  => array(
				'points'       => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
				'class'        => true,
			),
			'ellipse'  => array(
				'cx'           => true,
				'cy'           => true,
				'rx'           => true,
				'ry'           => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
				'class'        => true,
			),
			'g'        => array(
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
				'transform'    => true,
				'class'        => true,
				'opacity'      => true,
				'id'           => true,
			),
			'defs'     => array(),
			'clippath' => array(
				'id' => true,
			),
			'use'      => array(
				'href'       => true,
				'xlink:href' => true,
				'x'          => true,
				'y'          => true,
			),
			'text'     => array(
				'x'         => true,
				'y'         => true,
				'fill'      => true,
				'font-size' => true,
				'class'     => true,
			),
			'tspan'    => array(
				'x'  => true,
				'y'  => true,
				'dx' => true,
				'dy' => true,
			),
			'title'    => array(),
			'desc'     => array(),
			'mask'     => array(
				'id' => true,
			),
			'symbol'   => array(
				'id'      => true,
				'viewBox' => true,
			),
			'stop'     => array(
				'offset'     => true,
				'stop-color' => true,
			),
			'lineargradient' => array(
				'id'            => true,
				'x1'            => true,
				'y1'            => true,
				'x2'            => true,
				'y2'            => true,
				'gradientunits' => true,
			),
			'radialgradient' => array(
				'id'            => true,
				'cx'            => true,
				'cy'            => true,
				'r'             => true,
				'fx'            => true,
				'fy'            => true,
				'gradientunits' => true,
			),
		);
	}
}

Caller::get_instance();
