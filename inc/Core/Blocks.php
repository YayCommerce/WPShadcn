<?php

namespace Shadcn\Core;

use Shadcn\Traits\SingletonTrait;

class Blocks {
	use SingletonTrait;

	public function __construct() {
		add_action( 'init', array( $this, 'register_block_styles' ) );
		add_action( 'init', array( $this, 'register_pattern_category' ), 9 );
	}

	/**
	 * Register block styles
	 */
	public function register_block_styles() {

		$button_styles = array(
			'outline'     => __( 'Outline', 'shadcn' ),
			'secondary'   => __( 'Secondary', 'shadcn' ),
			'destructive' => __( 'Destructive', 'shadcn' ),
			'ghost'       => __( 'Ghost', 'shadcn' ),
			'sm'          => __( 'Small', 'shadcn' ),
			'lg'          => __( 'Large', 'shadcn' ),
		);

		foreach ( $button_styles as $style => $label ) {
			register_block_style(
				'core/button',
				array(
					'name'  => $style,
					'label' => $label,
				)
			);
		}

		// Group block styles
		register_block_style(
			'core/group',
			array(
				'name'  => 'card',
				'label' => __( 'Card', 'shadcn' ),
			)
		);

		register_block_style(
			'core/navigation',
			array(
				'name'  => 'pill',
				'label' => __( 'Pill', 'shadcn' ),
			)
		);

	}

	public function register_pattern_category() {

		if ( ! class_exists( '\WP_Block_Pattern_Categories_Registry' ) ) {
			return;
		}

		$block_pattern_categories = array(
			'shadcn'        => array( 'label' => __( 'Shadcn Patterns', 'shadcn' ) ),
			'shadcn-banner' => array( 'label' => __( 'Shadcn Banner', 'shadcn' ) ),
		);

		$block_pattern_categories = apply_filters( 'fse_handyman_block_pattern_categories', $block_pattern_categories );

		foreach ( $block_pattern_categories as $name => $properties ) {
			if ( ! \WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( $name ) ) {
				register_block_pattern_category( $name, $properties ); // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_pattern_category
			}
		}
	}
}

Blocks::get_instance();
