<?php

namespace Shadcn\Integrations;

use Shadcn\Traits\SingletonTrait;

class WooCommerce {

	use SingletonTrait;

	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		// enqueue_block_assets covers the editor as well as the front end. The
		// editor renders the same cart and checkout DOM, so without these the
		// canvas shows unstyled WooCommerce markup and a merchant cannot judge
		// what they are editing; the layout picker's schematics are laid out by
		// the same rules.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * The theme's WooCommerce styling.
	 *
	 * The two layout stylesheets only match once a layout class is applied, so
	 * loading them unconditionally costs a request and changes nothing else.
	 */
	public function enqueue_styles() {
		$version = wp_get_theme()->get( 'Version' );
		$base    = get_template_directory_uri() . '/assets/css/';

		wp_enqueue_style( 'shadcn-woocommerce', $base . 'woocommerce.css', array(), $version );
		wp_enqueue_style( 'shadcn-mini-cart-layouts', $base . 'mini-cart-layouts.css', array(), $version );
		wp_enqueue_style( 'shadcn-checkout-layouts', $base . 'checkout-layouts.css', array(), $version );
	}
}

WooCommerce::get_instance();
