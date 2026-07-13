<?php
/**
 * WP-CLI eval runner for the AI Layout Builder.
 *
 * Exercises the real pipeline (CatalogBuilder → LlmClient → proxy) against
 * the golden prompt set in tools/ai-eval/golden-prompts.json and records
 * results for before/after quality comparison. Requires the AI endpoint URL
 * and auth token to be configured in wp-admin first.
 *
 * Usage (from the site shell, e.g. LocalWP "Open Site Shell"):
 *   wp shadcn-ai-eval run --label=baseline
 *   wp shadcn-ai-eval run --label=improved --prompt-id=saas-landing-en
 *   wp shadcn-ai-eval catalog
 *
 * @package Shadcn
 * @since 1.0.0
 */

namespace Shadcn\AiLayoutBuilder;

class EvalCommand {

	/**
	 * Slug-prefix → section-type map. The token is the first dash-separated
	 * word of the pattern name after the "shadcn/" prefix.
	 */
	const SECTION_TYPES = array(
		'hero'       => 'hero',
		'features'   => 'features',
		'feature'    => 'features',
		'incentives' => 'incentives',
		'cta'        => 'cta',
		'logo'       => 'logo',
		'blog'       => 'blog',
		'header'     => 'header',
		'footer'     => 'footer',
		'404'        => '404',
	);

	/**
	 * Run the golden prompt set through the live pipeline.
	 *
	 * ## OPTIONS
	 *
	 * [--label=<label>]
	 * : Label for the results file (e.g. baseline, improved). Default: run.
	 *
	 * [--prompt-id=<id>]
	 * : Only run the golden prompt with this id.
	 *
	 * @param array $args       Positional args (unused).
	 * @param array $assoc_args Associative args.
	 */
	public function run( $args, $assoc_args ) {
		$label     = isset( $assoc_args['label'] ) ? sanitize_key( $assoc_args['label'] ) : 'run';
		$prompt_id = isset( $assoc_args['prompt-id'] ) ? $assoc_args['prompt-id'] : '';

		if ( '' === get_option( 'shadcn_ai_layout_endpoint_url', '' ) ) {
			\WP_CLI::error( 'AI endpoint URL is not configured. Set it under Appearance → AI Layout Builder first.' );
		}

		$prompts = $this->load_golden_prompts();

		if ( '' !== $prompt_id ) {
			$prompts = array_values(
				array_filter(
					$prompts,
					function ( $entry ) use ( $prompt_id ) {
						return $entry['id'] === $prompt_id;
					}
				)
			);

			if ( empty( $prompts ) ) {
				\WP_CLI::error( sprintf( 'No golden prompt with id "%s".', $prompt_id ) );
			}
		}

		$catalog = ( new CatalogBuilder() )->build();

		if ( empty( $catalog ) ) {
			\WP_CLI::error( 'Pattern catalog is empty — are the shadcn/* patterns registered?' );
		}

		$catalog_by_slug = array();
		foreach ( $catalog as $entry ) {
			$catalog_by_slug[ $entry['slug'] ] = $entry;
		}

		$site_context = ( new SiteContextBuilder() )->build();
		\WP_CLI::log( 'siteContext sent: ' . ( empty( $site_context ) ? '(none)' : wp_json_encode( $site_context, JSON_UNESCAPED_UNICODE ) ) );

		$client  = new LlmClient();
		$results = array();
		$rows    = array();

		foreach ( $prompts as $golden ) {
			\WP_CLI::log( sprintf( 'Running "%s"…', $golden['id'] ) );

			$result = array(
				'id'               => $golden['id'],
				'lang'             => $golden['lang'],
				'prompt'           => $golden['prompt'],
				'expectedSections' => $golden['expectedSections'],
				'returnedSlugs'    => array(),
				'returnedSections' => array(),
				'inventedSlugs'    => array(),
				'invalidSlots'     => array(),
				'error'            => null,
				// Filled in by a human after the run: true|false|null.
				'judgedCorrect'    => null,
			);

			try {
				// The proxy returns a BARE array of {slug, textSlots} entries —
				// no envelope object (see the theme's RestController, which
				// consumes LlmClient::request()'s return directly as the plan).
				$response = $client->request( $golden['prompt'], $catalog, $site_context );
				$plan     = is_array( $response ) ? $response : array();

				if ( empty( $plan ) ) {
					$result['error'] = 'Response contained no plan array.';
				}

				foreach ( $plan as $step ) {
					$slug = isset( $step['slug'] ) ? (string) $step['slug'] : '';

					$result['returnedSlugs'][]    = $slug;
					$result['returnedSections'][] = $this->section_type( $slug );

					if ( ! isset( $catalog_by_slug[ $slug ] ) ) {
						$result['inventedSlugs'][] = $slug;
						continue;
					}

					$valid_indices = wp_list_pluck( $catalog_by_slug[ $slug ]['textSlots'], 'index' );
					$text_slots    = ( isset( $step['textSlots'] ) && is_array( $step['textSlots'] ) ) ? $step['textSlots'] : array();

					foreach ( array_keys( $text_slots ) as $index ) {
						if ( ! in_array( (int) $index, $valid_indices, true ) ) {
							$result['invalidSlots'][] = $slug . '#' . $index;
						}
					}
				}
			} catch ( LlmRequestException $e ) {
				$result['error'] = $e->getMessage();
			}

			$results[] = $result;
			$rows[]    = array(
				'id'         => $golden['id'],
				'expected'   => implode( ' → ', $golden['expectedSections'] ),
				'returned'   => implode( ' → ', $result['returnedSections'] ),
				'violations' => count( $result['inventedSlugs'] ) + count( $result['invalidSlots'] ),
				'error'      => $result['error'] ? $result['error'] : '-',
			);
		}

		$file = $this->write_results( $label, $results, $site_context );

		\WP_CLI\Utils\format_items( 'table', $rows, array( 'id', 'expected', 'returned', 'violations', 'error' ) );
		\WP_CLI::success( sprintf( 'Results written to %s — fill in judgedCorrect per prompt manually.', $file ) );
	}

	/**
	 * Print the catalog exactly as sent to the LLM, plus hint coverage
	 * warnings, for hand verification.
	 */
	public function catalog() {
		$catalog = ( new CatalogBuilder() )->build();

		\WP_CLI::log( wp_json_encode( $catalog, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
		\WP_CLI::log( sprintf( 'Entries: %d, serialized size: %d bytes.', count( $catalog ), strlen( wp_json_encode( $catalog ) ) ) );

		// Drift guard: every registered shadcn/* pattern should carry a
		// curated AI hint once the aiHint field exists in the catalog.
		$missing_hints = array();
		foreach ( $catalog as $entry ) {
			if ( array_key_exists( 'aiHint', $entry ) && '' === $entry['aiHint'] ) {
				$missing_hints[] = $entry['slug'];
			}
		}

		if ( ! empty( $missing_hints ) ) {
			\WP_CLI::warning( 'Patterns missing an AI Hint header: ' . implode( ', ', $missing_hints ) );
		}
	}

	/**
	 * @return array[] Golden prompt entries.
	 */
	private function load_golden_prompts() {
		$path = get_theme_file_path( 'tools/ai-eval/golden-prompts.json' );

		if ( ! file_exists( $path ) ) {
			\WP_CLI::error( sprintf( 'Golden prompts file not found: %s', $path ) );
		}

		$data = json_decode( (string) file_get_contents( $path ), true );

		if ( ! is_array( $data ) || empty( $data['prompts'] ) ) {
			\WP_CLI::error( 'Golden prompts file is malformed (expected {"prompts": [...]}).' );
		}

		return $data['prompts'];
	}

	/**
	 * @param string $slug Pattern slug, e.g. "shadcn/hero-section-3".
	 * @return string Section type or "unknown".
	 */
	private function section_type( $slug ) {
		$name  = str_replace( CatalogBuilder::SLUG_PREFIX, '', $slug );
		$token = strtok( $name, '-' );

		return isset( self::SECTION_TYPES[ $token ] ) ? self::SECTION_TYPES[ $token ] : 'unknown';
	}

	/**
	 * @param string $label        Results label.
	 * @param array  $results      Per-prompt results.
	 * @param array  $site_context siteContext sent with every request.
	 * @return string Written file path.
	 */
	private function write_results( $label, array $results, array $site_context = array() ) {
		$dir = get_theme_file_path( 'tools/ai-eval/results' );

		if ( ! wp_mkdir_p( $dir ) ) {
			\WP_CLI::error( sprintf( 'Could not create results directory: %s', $dir ) );
		}

		$file    = sprintf( '%s/%s-%s.json', $dir, gmdate( 'ymd-Hi' ), $label );
		$payload = array(
			'label'     => $label,
			'ranAt'     => gmdate( 'c' ),
			'model'     => 'proxy-configured (default gpt-4o-mini)',
			'singleRun' => 'Results are single-shot and nondeterministic; treat as directional.',
			'siteContext' => $site_context,
			'results'   => $results,
		);

		if ( false === file_put_contents( $file, wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ) ) {
			\WP_CLI::error( sprintf( 'Could not write results file: %s', $file ) );
		}

		return $file;
	}
}
