<?php

namespace Shadcn;

use Shadcn\Traits\SingletonTrait;

class Core {
	use SingletonTrait;

	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'setup_theme' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_editor_styles' ) );
		add_action( 'after_setup_theme', array( $this, 'starter_content_setup' ) );
		add_action( 'after_setup_theme', array( $this, 'create_demo_navigation' ), 20 );

		require_once __DIR__ . '/Core/Blocks.php';
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

	 /**
	* Create demo navigation post for footer
	*/
	public function create_demo_navigation() {
		// Check if navigation post already exists
		$existing_nav = get_posts(
			array(
				'post_type'      => 'wp_navigation',
				'title'          => 'Dummy Navigation',
				'posts_per_page' => 1,
			)
		);

		$dummy_content = '
	   <!-- wp:navigation-link {"label":"Shop","url":"#","kind":"custom"} /-->
	   <!-- wp:navigation-link {"label":"Blog","url":"#","kind":"custom"} /-->
	   <!-- wp:navigation-link {"label":"About","url":"#","kind":"custom"} /-->';

		if ( ! empty( $existing_nav ) ) {
			//Update the navigation post
			wp_update_post(
				array(
					'ID'           => $existing_nav[0]->ID,
					'post_content' => $dummy_content,
				)
			);
			return;
		}

		// Create the navigation post
		$nav_post_id = wp_insert_post(
			array(
				'post_type'    => 'wp_navigation',
				'post_title'   => 'Dummy Navigation',
				'post_content' => $dummy_content,
				'post_status'  => 'publish',
			)
		);

		if ( ! is_wp_error( $nav_post_id ) ) {
			error_log( 'Footer navigation post created with ID: ' . $nav_post_id );
		}
	}
}

Core::get_instance();
