<?php

namespace Shadcn;

use Shadcn\Traits\SingletonTrait;

class Core {
	use SingletonTrait;

	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'setup_theme' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_editor_styles' ) );
		add_action( 'after_setup_theme', array( $this, 'starter_content_setup' ) );

		require_once __DIR__ . '/Core/Blocks.php';
		require_once __DIR__ . '/Core/Patterns.php';
	}

	public function setup_theme() {
		// Add theme support for various features
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'custom-logo' );
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);
		// Add theme support
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'responsive-embeds' );
	}

	public function setup_editor_styles() {
		add_theme_support( 'editor-styles' );
		add_editor_style( get_template_directory_uri() . '/assets/css/editor-style.css' );
	}

	/**
	 * Add support for starter content
	 */
	public function starter_content_setup() {
		add_theme_support(
			'starter-content',
			array(
				'widgets'    => array(
					'sidebar-1' => array(
						'text_business_info',
						'search',
						'text_about',
					),
				),
				'posts'      => array(
					'home',
					'about'            => array(
						'thumbnail' => '{{image-sandwich}}',
					),
					'contact'          => array(
						'thumbnail' => '{{image-espresso}}',
					),
					'blog'             => array(
						'thumbnail' => '{{image-coffee}}',
					),
					'homepage-section' => array(
						'thumbnail' => '{{image-espresso}}',
					),
				),
				'options'    => array(
					'show_on_front'  => 'page',
					'page_on_front'  => '{{home}}',
					'page_for_posts' => '{{blog}}',
				),
				'theme_mods' => array(
					'panel_1' => '{{homepage-section}}',
					'panel_2' => '{{about}}',
					'panel_3' => '{{blog}}',
					'panel_4' => '{{contact}}',
				),
				'nav_menus'  => array(
					'top'    => array(
						'name'  => __( 'Top Menu', 'shadcn' ),
						'items' => array(
							'link_home',
							'page_about',
							'page_blog',
							'page_contact',
						),
					),
					'social' => array(
						'name'  => __( 'Social Links Menu', 'shadcn' ),
						'items' => array(
							'link_yelp',
							'link_facebook',
							'link_twitter',
							'link_instagram',
							'link_email',
						),
					),
				),
			)
		);
	}
}

Core::get_instance();
