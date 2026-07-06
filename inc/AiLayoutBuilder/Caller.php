<?php
/**
 * Bootstrap for the AI Layout Builder feature.
 *
 * @package Shadcn
 * @since 1.0.0
 */

namespace Shadcn\AiLayoutBuilder;

use Shadcn\Traits\SingletonTrait;

class Caller {
	use SingletonTrait;

	protected function __construct() {
		require_once __DIR__ . '/TextSlotHtml.php';
		require_once __DIR__ . '/CatalogBuilder.php';
		require_once __DIR__ . '/UnknownPatternSlugException.php';
		require_once __DIR__ . '/PlanResolver.php';
		require_once __DIR__ . '/LlmRequestException.php';
		require_once __DIR__ . '/LlmClient.php';
		require_once __DIR__ . '/RateLimiter.php';
		require_once __DIR__ . '/RestController.php';
		require_once __DIR__ . '/SettingsPage.php';

		RestController::get_instance();
		SettingsPage::get_instance();

		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
	}

	/**
	 * Editor-only sidebar panel assets — never enqueued on the frontend.
	 */
	public function enqueue_editor_assets() {
		$version = wp_get_theme()->get( 'Version' );

		wp_enqueue_script(
			'shadcn-ai-layout-builder',
			get_template_directory_uri() . '/inc/AiLayoutBuilder/script.js',
			array(
				'wp-plugins',
				'wp-editor',
				'wp-edit-post',
				'wp-element',
				'wp-components',
				'wp-block-editor',
				'wp-blocks',
				'wp-api-fetch',
				'wp-i18n',
				'wp-data',
			),
			$version,
			true
		);

		wp_enqueue_style(
			'shadcn-ai-layout-builder',
			get_template_directory_uri() . '/inc/AiLayoutBuilder/style.css',
			array(),
			$version
		);
	}
}

Caller::get_instance();
