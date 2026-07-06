<?php
/**
 * REST route for the AI Layout Builder: prompt in, plan validated, resolved
 * markup + warnings out. Owns the security trust boundary — permission
 * check, rate limit, LLM response shape validation, and slug whitelisting
 * all happen here before anything reaches PlanResolver.
 *
 * @package Shadcn
 * @since 1.0.0
 */

namespace Shadcn\AiLayoutBuilder;

use Shadcn\Traits\SingletonTrait;

class RestController {
	use SingletonTrait;

	/**
	 * Reject/truncate plans larger than this — an unbounded plan is a
	 * resource-exhaustion vector, not just a correctness concern.
	 */
	const MAX_PLAN_ENTRIES = 10;

	private $rate_limiter;

	protected function __construct() {
		$this->rate_limiter = new RateLimiter();

		add_action( 'rest_api_init', array( $this, 'register_route' ) );
	}

	public function register_route() {
		register_rest_route(
			'shadcn/v1',
			'/ai-layout/generate',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'handle_generate' ),
				'permission_callback' => array( $this, 'check_permission' ),
				'args'                => array(
					'prompt' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);
	}

	/**
	 * Gated on edit_theme_options (not edit_posts) — this route spends an
	 * admin-configured, cost-bearing external credential; edit_posts would
	 * let any Contributor-level account independently drive billed requests.
	 */
	public function check_permission() {
		return current_user_can( 'edit_theme_options' );
	}

	public function handle_generate( \WP_REST_Request $request ) {
		$user_id = get_current_user_id();

		// Rate limit is checked FIRST, before any catalog build or LLM call,
		// to fail closed on cost rather than just on abuse.
		if ( ! $this->rate_limiter->is_allowed( $user_id ) ) {
			return new \WP_Error(
				'shadcn_ai_layout_rate_limited',
				__( 'You have reached the AI Layout Builder request limit for this hour. Please try again later.', 'shadcn' ),
				array( 'status' => 429 )
			);
		}

		$prompt          = $request->get_param( 'prompt' );
		$catalog_builder = new CatalogBuilder();
		$catalog         = $catalog_builder->build();

		try {
			$llm_client = new LlmClient();
			$raw_plan   = $llm_client->request( $prompt, $catalog );
		} catch ( LlmRequestException $e ) {
			return new \WP_Error(
				'shadcn_ai_layout_llm_error',
				$e->getMessage(),
				array( 'status' => 502 )
			);
		}

		list( $valid_plan, $warnings ) = $this->validate_and_filter_plan( $raw_plan, $catalog );

		if ( empty( $valid_plan ) ) {
			// Surface WHY every entry was rejected (unknown slug, malformed
			// shape, etc.) instead of discarding $warnings here — this is
			// the only diagnostic signal available when the whole plan
			// fails, and without it there's no way to tell "the LLM
			// hallucinated a slug" apart from "the LLM returned garbage
			// shape" apart from "the LLM returned zero entries". Also
			// surface catalog/raw-plan sizes: an empty $warnings array with
			// a non-empty catalog means the LLM itself returned zero
			// entries (the foreach loop in validate_and_filter_plan never
			// ran); an empty catalog means CatalogBuilder found no patterns
			// to send in the first place — two very different root causes.
			return new \WP_Error(
				'shadcn_ai_layout_empty_plan',
				__( 'No valid layout could be generated from that prompt.', 'shadcn' ),
				array(
					'status'         => 422,
					'warnings'       => $warnings,
					'catalog_count'  => count( $catalog ),
					'raw_plan_count' => is_array( $raw_plan ) ? count( $raw_plan ) : null,
				)
			);
		}

		$resolver = new PlanResolver();

		try {
			$markup = $resolver->resolve_plan( $valid_plan );
		} catch ( UnknownPatternSlugException $e ) {
			// The whitelist filter below should have already caught this —
			// PlanResolver's own internal guard tripping here means our
			// filter had a bug, not that the LLM is untrustworthy. Fail
			// closed rather than surface a fatal.
			return new \WP_Error(
				'shadcn_ai_layout_resolve_error',
				__( 'Layout could not be generated.', 'shadcn' ),
				array( 'status' => 500 )
			);
		}

		return rest_ensure_response(
			array(
				'markup'   => $markup,
				'warnings' => $warnings,
			)
		);
	}

	/**
	 * Validate the LLM's decoded response shape, then filter to only
	 * whitelisted pattern slugs, collecting a human-readable warning for
	 * every dropped/rejected entry instead of silently discarding them.
	 *
	 * @return array{0: array, 1: string[]} [ valid plan entries, warnings ]
	 */
	private function validate_and_filter_plan( $raw_plan, array $catalog ) {
		$warnings = array();

		if ( ! is_array( $raw_plan ) ) {
			return array( array(), array( __( 'AI response was not in the expected format.', 'shadcn' ) ) );
		}

		$total_entries = count( $raw_plan );
		$entries       = array_slice( $raw_plan, 0, self::MAX_PLAN_ENTRIES );

		if ( $total_entries > self::MAX_PLAN_ENTRIES ) {
			$warnings[] = sprintf(
				/* translators: %d: max number of layout sections used. */
				__( 'Only the first %d layout sections were used; the rest were dropped.', 'shadcn' ),
				self::MAX_PLAN_ENTRIES
			);
		}

		$valid_slugs = wp_list_pluck( $catalog, 'slug' );
		$valid       = array();

		foreach ( $entries as $entry ) {
			if ( ! is_array( $entry ) || empty( $entry['slug'] ) || ! is_string( $entry['slug'] ) ) {
				$warnings[] = __( 'A layout section had an invalid format and was skipped.', 'shadcn' );
				continue;
			}

			// array_key_exists(), not isset() — isset() returns false for an
			// explicit null value, which would silently default a malformed
			// "textSlots": null response to an empty array instead of
			// rejecting it as invalid shape.
			$text_slots = array_key_exists( 'textSlots', $entry ) ? $entry['textSlots'] : array();

			if ( ! is_array( $text_slots ) ) {
				$warnings[] = sprintf(
					/* translators: %s: pattern slug. */
					__( 'Section "%s" had invalid text and was skipped.', 'shadcn' ),
					$entry['slug']
				);
				continue;
			}

			if ( ! in_array( $entry['slug'], $valid_slugs, true ) ) {
				$warnings[] = sprintf(
					/* translators: %s: pattern slug. */
					__( 'Section "%s" is not a known layout pattern and was skipped.', 'shadcn' ),
					$entry['slug']
				);
				continue;
			}

			$sanitized_slots = array();

			foreach ( $text_slots as $index => $text ) {
				if ( ! is_int( $index ) && ! ctype_digit( (string) $index ) ) {
					continue; // Non-numeric slot key — not a valid index, silently ignored.
				}

				// Never render LLM text as HTML/markup — plain text only.
				$sanitized_slots[ (int) $index ] = sanitize_text_field( (string) $text );
			}

			$valid[] = array(
				'slug'      => $entry['slug'],
				'textSlots' => $sanitized_slots,
			);
		}

		return array( $valid, $warnings );
	}
}
