<?php

namespace Shadcn\Integrations;

use Shadcn\Traits\SingletonTrait;

class WooCommerce {

	use SingletonTrait;

	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'shadcn-woocommerce', get_template_directory_uri() . '/assets/css/woocommerce.css', array(), '1.0.0' );
	}
}

WooCommerce::get_instance();
