<?php

namespace Shadcn;

use Shadcn\Traits\SingletonTrait;

class DarkMode {
	use SingletonTrait;

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'language_attributes', array( $this, 'html_classes' ) );
		add_action( 'wp_head', array( $this, 'add_dark_mode_sync' ), 1 );
	}

	public function enqueue_scripts() {

		$theme_version = wp_get_theme()->get( 'Version' );

		wp_enqueue_style(
			'shadcn-style',
			get_stylesheet_uri(),
			array(),
			$theme_version
		);

		// Enqueue dark mode toggle script
		wp_enqueue_script(
			'shadcn-dark-mode',
			get_template_directory_uri() . '/assets/js/dark-mode.js',
			array(),
			$theme_version,
			true
		);

		// Localize script for AJAX
		wp_localize_script(
			'shadcn-dark-mode',
			'shadcn',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'shadcn_nonce' ),
			)
		);
	}

	/**
	 * Add dark mode class to html element
	 */
	public function html_classes( $attributes ) {
		if ( is_admin() ) {
			return $attributes;
		}

		if ( isset( $_COOKIE['shadcn-theme-mode'] ) && 'dark' === $_COOKIE['shadcn-theme-mode'] ) {
			$attributes .= ' class="dark" style="color-scheme: dark;"';
		}

		return $attributes;
	}

	/**
	 * Add inline script to sync localStorage to cookie for server-side rendering
	 * This prevents FOUC (Flash of Unstyled Content)
	 */
	public function add_dark_mode_sync() { ?>
		<script>
			(function() {
				// Helper to set cookie with proper attributes for production
				function setCookieValue(name, value) {
					try {
						const expires = new Date();
						expires.setTime(expires.getTime() + (365 * 24 * 60 * 60 * 1000));
						const secureFlag = window.location.protocol === 'https:' ? ';Secure' : '';
						const cookieString = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/;SameSite=Lax' + secureFlag;
						document.cookie = cookieString;
					} catch (e) {
						console.error('Failed to set cookie:', e);
					}
				}
				
				// Check if we have localStorage preference
				try {
					const savedMode = localStorage.getItem('shadcn_dark_mode');
					if (savedMode !== null) {
						// Set cookie to match localStorage
						const cookieValue = savedMode === 'true' ? 'dark' : 'light';
						setCookieValue('shadcn-theme-mode', cookieValue);
					} else {
						// Check system preference and set cookie
						const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
						const cookieValue = prefersDark ? 'dark' : 'light';
						setCookieValue('shadcn-theme-mode', cookieValue);
					}
				} catch(e) {
					// Ignore localStorage errors
					console.error('Dark mode sync error:', e);
				}
			})();
		</script>
		<?php
	}

}

return DarkMode::get_instance();
