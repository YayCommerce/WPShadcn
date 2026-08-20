<?php
/**
 * Prompts the site owner to install or activate the Shadcn Blocks plugin.
 *
 * The theme ships layouts; the plugin ships the blocks those layouts are built
 * from and Shadcn AI that assembles them. Without the plugin the theme still
 * renders, so this is a notice rather than a hard dependency.
 *
 * @package Shadcn
 * @since 1.0.0
 */

namespace Shadcn\BlocksPlugin;

use Shadcn\Traits\SingletonTrait;

defined( 'ABSPATH' ) || exit;

class Caller {
	use SingletonTrait;

	/**
	 * WordPress.org slug — matches PLUGIN_SLUG in the plugin's release.sh, so
	 * it is also the directory name of an installed release build.
	 */
	const PLUGIN_SLUG = 'shadcn-blocks';

	/**
	 * Main file of the plugin, relative to its own directory. Used to find an
	 * installed copy whatever its folder is called (a git checkout may sit in
	 * `wpshadcn-blocks/`, a wp.org install in `shadcn-blocks/`).
	 */
	const PLUGIN_FILE = 'shadcn-blocks.php';

	/** AJAX action and nonce for the editor modal's one-click plugin setup. */
	const SETUP_ACTION = 'shadcn_ai_install_blocks';

	protected function __construct() {
		add_action( 'admin_notices', array( $this, 'render_notice' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_setup' ) );
		add_action( 'wp_ajax_' . self::SETUP_ACTION, array( $this, 'ajax_install_blocks' ) );
	}

	/**
	 * The Shadcn AI entry point when the blocks plugin is NOT active: the
	 * plugin normally ships the whole editor UI, so without it the editor
	 * would simply have no Shadcn AI anywhere — and the one thing a user
	 * would need it for is installing the plugin. This script registers the
	 * same sparkle sidebar in the same spot, but its modal offers the fix:
	 * one-click install/activate for users allowed to manage plugins, a
	 * pointer for everyone else. Never enqueued alongside the plugin, so the
	 * two can share the "shadcn-ai" plugin name without colliding.
	 */
	public function enqueue_editor_setup() {
		if ( defined( 'SHADCN_BLOCKS_VERSION' ) ) {
			return;
		}

		$version = wp_get_theme()->get( 'Version' );

		wp_enqueue_script(
			'shadcn/ai-setup',
			get_template_directory_uri() . '/inc/BlocksPlugin/editor-setup-script.js',
			array( 'wp-plugins', 'wp-editor', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-i18n' ),
			$version,
			true
		);

		$installed = '' !== $this->installed_path();
		wp_localize_script(
			'shadcn/ai-setup',
			'shadcnAiSetup',
			array(
				'installed'   => $installed,
				// Activating an installed plugin and installing a missing one
				// are different capabilities; the button only shows when this
				// user can complete the whole path.
				'canSetup'    => current_user_can( 'activate_plugins' )
					&& ( $installed || current_user_can( 'install_plugins' ) ),
				'nonce'       => wp_create_nonce( self::SETUP_ACTION ),
				'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
				// Where a user who cannot manage plugins is sent instead: the
				// Plugins screen when only activation is missing, the public
				// WordPress.org page when the plugin is absent — the admin
				// install screens would refuse them.
				'fallbackUrl' => $installed
					? admin_url( 'plugins.php?s=' . self::PLUGIN_SLUG )
					: 'https://wordpress.org/plugins/' . self::PLUGIN_SLUG . '/',
			)
		);

		wp_enqueue_style(
			'shadcn/ai-setup',
			get_template_directory_uri() . '/inc/BlocksPlugin/editor-setup-style.css',
			array(),
			$version
		);
	}

	/**
	 * One click from the editor modal: make Shadcn Blocks active, installing
	 * it from WordPress.org first when it is missing entirely.
	 */
	public function ajax_install_blocks() {
		check_ajax_referer( self::SETUP_ACTION, 'nonce' );

		$path = $this->installed_path();
		if (
			! current_user_can( 'activate_plugins' )
			|| ( '' === $path && ! current_user_can( 'install_plugins' ) )
		) {
			wp_send_json_error( __( 'You are not allowed to manage plugins on this site.', 'shadcn' ), 403 );
		}

		if ( '' === $path ) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';

			$api = plugins_api( 'plugin_information', array( 'slug' => self::PLUGIN_SLUG ) );
			if ( is_wp_error( $api ) ) {
				wp_send_json_error( $api->get_error_message(), 500 );
			}

			$upgrader = new \Plugin_Upgrader( new \WP_Ajax_Upgrader_Skin() );
			$result   = $upgrader->install( $api->download_link );
			if ( true !== $result ) {
				wp_send_json_error(
					is_wp_error( $result ) ? $result->get_error_message() : __( 'Install failed.', 'shadcn' ),
					500
				);
			}

			$path = $this->installed_path();
			if ( '' === $path ) {
				wp_send_json_error( __( 'Install failed.', 'shadcn' ), 500 );
			}
		}

		$activated = activate_plugin( $path );
		if ( is_wp_error( $activated ) ) {
			wp_send_json_error( $activated->get_error_message(), 500 );
		}

		wp_send_json_success();
	}

	/**
	 * Show the notice only where acting on it makes sense — the dashboard and
	 * the theme and plugin screens.
	 *
	 * @return bool
	 */
	private function is_relevant_screen() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$screen = get_current_screen();

		if ( ! $screen ) {
			return false;
		}

		return in_array(
			$screen->id,
			array( 'dashboard', 'themes', 'plugins', 'plugin-install' ),
			true
		);
	}

	/**
	 * Path of the installed plugin ("<dir>/shadcn-blocks.php"), or '' when it
	 * is not installed at all.
	 *
	 * @return string
	 */
	private function installed_path() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		foreach ( array_keys( get_plugins() ) as $path ) {
			if ( self::PLUGIN_FILE === basename( $path ) ) {
				return $path;
			}
		}

		return '';
	}

	/**
	 * Print the notice when the plugin is missing or installed but inactive.
	 */
	public function render_notice() {
		// The plugin defines this the moment it loads, so it doubles as the
		// "is it active" check without touching is_plugin_active().
		if ( defined( 'SHADCN_BLOCKS_VERSION' ) ) {
			return;
		}

		if ( ! $this->is_relevant_screen() ) {
			return;
		}

		$path   = $this->installed_path();
		$action = '' === $path
			? $this->install_action()
			: $this->activate_action( $path );

		if ( empty( $action ) ) {
			// The user cannot install or activate plugins; a button they
			// cannot use would only be noise.
			return;
		}
		?>
		<div class="notice notice-info">
			<p>
				<strong><?php esc_html_e( 'Shadcn', 'shadcn' ); ?>:</strong>
				<?php esc_html_e( 'Install the Shadcn Blocks plugin to use the theme\'s blocks, section patterns and Shadcn AI.', 'shadcn' ); ?>
			</p>
			<p>
				<a class="button button-primary" href="<?php echo esc_url( $action['url'] ); ?>">
					<?php echo esc_html( $action['label'] ); ?>
				</a>
			</p>
		</div>
		<?php
	}

	/**
	 * Link that installs the plugin from WordPress.org.
	 *
	 * @return array{url:string,label:string}|array{} Empty when not permitted.
	 */
	private function install_action() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return array();
		}

		$url = wp_nonce_url(
			self_admin_url( 'update.php?action=install-plugin&plugin=' . self::PLUGIN_SLUG ),
			'install-plugin_' . self::PLUGIN_SLUG
		);

		return array(
			'url'   => $url,
			'label' => __( 'Install Shadcn Blocks', 'shadcn' ),
		);
	}

	/**
	 * Link that activates an already installed copy.
	 *
	 * @param string $path Plugin path, e.g. "shadcn-blocks/shadcn-blocks.php".
	 * @return array{url:string,label:string}|array{} Empty when not permitted.
	 */
	private function activate_action( $path ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return array();
		}

		$url = wp_nonce_url(
			self_admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( $path ) ),
			'activate-plugin_' . $path
		);

		return array(
			'url'   => $url,
			'label' => __( 'Activate Shadcn Blocks', 'shadcn' ),
		);
	}
}

Caller::get_instance();
