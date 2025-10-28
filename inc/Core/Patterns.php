<?php

namespace Shadcn\Core;

use Shadcn\Traits\SingletonTrait;

class Patterns {
	use SingletonTrait;

	public function __construct() {
		add_action( 'init', array( $this, 'register_patterns' ) );
	}

	public function register_patterns() {
		$patterns = array(
			'shadcn/banner-heading' => array(
				'title'       => 'Banner Heading',
				'description' => 'A banner heading',
				'categories'  => array( 'shadcn', 'shadcn-banner' ),
				'content'     => '<!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontWeight":"700","lineHeight":"1.1","fontStyle":"normal"}},"fontSize":"fluid-7xl"} -->
				<h1 class="wp-block-heading has-text-align-center has-fluid-7-xl-font-size" style="font-style:normal;font-weight:700;line-height:1.1">Welcome to Our Website</h1>
				<!-- /wp:heading -->',
			),
		);

		foreach ( $patterns as $pattern => $pattern_data ) {
			register_block_pattern( $pattern, $pattern_data );
		}
	}
}

Patterns::get_instance();
