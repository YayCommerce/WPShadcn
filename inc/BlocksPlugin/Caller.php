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

	protected function __construct() {
		add_action( 'admin_notices', array( $this, 'render_notice' ) );
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
