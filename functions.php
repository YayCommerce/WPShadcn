<?php
/**
 * Shadcn WP Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WP_Shadcn
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme setup
 */
function wpshadcn_setup() {
    // Add theme support for various features
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('custom-logo');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));
}
add_action('after_setup_theme', 'wpshadcn_setup');

/**
 * Enqueue scripts and styles
 */
function wpshadcn_scripts() {
    // Enqueue main stylesheet
    wp_enqueue_style(
        'wpshadcn-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get('Version')
    );
    
    // Enqueue dark mode toggle script
    wp_enqueue_script(
        'wpshadcn-dark-mode',
        get_template_directory_uri() . '/assets/js/dark-mode.js',
        array(),
        wp_get_theme()->get('Version'),
        true
    );
    
    // Localize script for AJAX
    wp_localize_script('wpshadcn-dark-mode', 'wpshadcn', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wpshadcn_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'wpshadcn_scripts');

/**
 * Register block styles
 */
function wpshadcn_register_block_styles() {
    // Button block styles
    register_block_style(
        'core/button',
        array(
            'name' => 'outline',
            'label' => __('Outline', 'wpshadcn'),
        )
    );
    
    register_block_style(
        'core/button',
        array(
            'name' => 'secondary',
            'label' => __('Secondary', 'wpshadcn'),
        )
    );
    
    register_block_style(
        'core/button',
        array(
            'name' => 'destructive',
            'label' => __('Destructive', 'wpshadcn'),
        )
    );
    
    register_block_style(
        'core/button',
        array(
            'name' => 'ghost',
            'label' => __('Ghost', 'wpshadcn'),
        )
    );
    
    register_block_style(
        'core/button',
        array(
            'name' => 'sm',
            'label' => __('Small', 'wpshadcn'),
        )
    );
    
    register_block_style(
        'core/button',
        array(
            'name' => 'lg',
            'label' => __('Large', 'wpshadcn'),
        )
    );
    
    // Group block styles
    register_block_style(
        'core/group',
        array(
            'name' => 'card',
            'label' => __('Card', 'wpshadcn'),
        )
    );

}
add_action('init', 'wpshadcn_register_block_styles');

/**
 * Add dark mode class to html element
 */
function wpshadcn_html_classes($attributes) {
    if (isset($_COOKIE['wpshadcn-theme-mode']) && $_COOKIE['wpshadcn-theme-mode'] === 'dark') {
        $attributes .= ' class="dark" style="color-scheme: dark;"';
    }
    
    return $attributes;
}
add_filter('language_attributes', 'wpshadcn_html_classes');

/**
 * Add inline script to sync localStorage to cookie for server-side rendering
 * This prevents FOUC (Flash of Unstyled Content)
 */
function wpshadcn_add_dark_mode_sync() {
    ?>
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
                const savedMode = localStorage.getItem('wpshadcn_dark_mode');
                if (savedMode !== null) {
                    // Set cookie to match localStorage
                    const cookieValue = savedMode === 'true' ? 'dark' : 'light';
                    setCookieValue('wpshadcn-theme-mode', cookieValue);
                } else {
                    // Check system preference and set cookie
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    const cookieValue = prefersDark ? 'dark' : 'light';
                    setCookieValue('wpshadcn-theme-mode', cookieValue);
                }
            } catch(e) {
                // Ignore localStorage errors
                console.error('Dark mode sync error:', e);
            }
        })();
    </script>
    <?php
}
add_action('wp_head', 'wpshadcn_add_dark_mode_sync', 1);

/**
 * Custom block editor styles
 */
function wpshadcn_editor_styles() {
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');
}
add_action('after_setup_theme', 'wpshadcn_editor_styles');

/**
 * Add support for starter content
 */
function wpshadcn_starter_content_setup() {
    add_theme_support('starter-content', array(
        'widgets' => array(
            'sidebar-1' => array(
                'text_business_info',
                'search',
                'text_about',
            ),
        ),
        'posts' => array(
            'home',
            'about' => array(
                'thumbnail' => '{{image-sandwich}}',
            ),
            'contact' => array(
                'thumbnail' => '{{image-espresso}}',
            ),
            'blog' => array(
                'thumbnail' => '{{image-coffee}}',
            ),
            'homepage-section' => array(
                'thumbnail' => '{{image-espresso}}',
            ),
        ),
        'options' => array(
            'show_on_front' => 'page',
            'page_on_front' => '{{home}}',
            'page_for_posts' => '{{blog}}',
        ),
        'theme_mods' => array(
            'panel_1' => '{{homepage-section}}',
            'panel_2' => '{{about}}',
            'panel_3' => '{{blog}}',
            'panel_4' => '{{contact}}',
        ),
        'nav_menus' => array(
            'top' => array(
                'name' => __('Top Menu', 'wpshadcn'),
                'items' => array(
                    'link_home',
                    'page_about',
                    'page_blog',
                    'page_contact',
                ),
            ),
            'social' => array(
                'name' => __('Social Links Menu', 'wpshadcn'),
                'items' => array(
                    'link_yelp',
                    'link_facebook',
                    'link_twitter',
                    'link_instagram',
                    'link_email',
                ),
            ),
        ),
    ));
}
add_action('after_setup_theme', 'wpshadcn_starter_content_setup');

/**
 * Create demo navigation post for footer
 */
function wpshadcn_create_demo_navigation() {
    // Check if navigation post already exists
    $existing_nav = get_posts(
            array(
            'post_type' => 'wp_navigation',
            'title' => 'Dummy Navigation',
            'posts_per_page' => 1,
        )
    );

    $dummy_content = '
    <!-- wp:navigation-link {"label":"Shop","url":"#","kind":"custom"} /-->
    <!-- wp:navigation-link {"label":"Blog","url":"#","kind":"custom"} /-->
    <!-- wp:navigation-link {"label":"About","url":"#","kind":"custom"} /-->';

    if ( !empty( $existing_nav) ) {
        //Update the navigation post
        wp_update_post(array(
            'ID' => $existing_nav[0]->ID,
            'post_content' => $dummy_content,
        ));
        return;
    }

    // Create the navigation post
    $nav_post_id = wp_insert_post(
            array(
            'post_type' => 'wp_navigation',
            'post_title' => 'Dummy Navigation',
            'post_content' => $dummy_content,
            'post_status' => 'publish',
        )
    );

    if (!is_wp_error($nav_post_id)) {
        error_log('Footer navigation post created with ID: ' . $nav_post_id);
    }
}
add_action('after_setup_theme', 'wpshadcn_create_demo_navigation', 20);

