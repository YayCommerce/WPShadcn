<?php

namespace Shadcn\BlockSettings\ButtonSize;

use Shadcn\Interfaces\BlockSettingsInterface;
use Shadcn\Traits\SingletonTrait;

class Caller implements BlockSettingsInterface {
	use SingletonTrait;

	protected function __construct() {
		add_filter( 'block_type_metadata_settings', array( $this, 'declare_attribute' ), 10 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_settings_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_frontend_scripts' ) );
	}

	private function is_button_block( $name ) {
		return in_array( $name, array( 'core/button' ), true );
	}

	public function declare_attribute( $settings ) {

		if ( empty( $settings['name'] ) ) {
			return $settings;
		}

		if ( ! $this->is_button_block( $settings['name'] ) ) {
			return $settings;
		}

		if ( ! empty( $settings['attributes'] ) ) {
			$settings['attributes']['size'] = array(
				'type'    => 'string',
				'default' => '',
			);
		}
		return $settings;
	}

	public function enqueue_settings_scripts() {
		wp_enqueue_script( 'shadcn/button-size', get_template_directory_uri() . '/inc/BlockSettings/ButtonSize/script.js', array( 'wp-edit-post' ), wp_get_theme()->get( 'Version' ), true );
	}

	public function enqueue_frontend_scripts() {
		wp_enqueue_style( 'shadcn/button-size', get_template_directory_uri() . '/inc/BlockSettings/ButtonSize/style.css', array(), wp_get_theme()->get( 'Version' ) );
	}

}

Caller::get_instance();
