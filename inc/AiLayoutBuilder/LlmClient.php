<?php
/**
 * HTTP client for the configured external AI Layout Builder endpoint.
 * Purely an HTTP client — no knowledge of patterns/catalog internals.
 *
 * @package Shadcn
 * @since 1.0.0
 */

namespace Shadcn\AiLayoutBuilder;

class LlmClient {

	/**
	 * Cap the AI endpoint's response body size to guard against a
	 * compromised/misbehaving endpoint sending an oversized response.
	 */
	const MAX_RESPONSE_BYTES = 524288; // 512KB.

	/**
	 * Send { prompt, catalog, siteContext } to the configured endpoint and
	 * return the decoded JSON response as-is (shape validation is
	 * RestController's job, not this class's — see Phase 2 plan).
	 *
	 * @param string $prompt       User's design request.
	 * @param array  $catalog      Pattern catalog.
	 * @param array  $site_context Optional site identity hints; omitted from
	 *                             the request body when empty.
	 * @return mixed Whatever the endpoint's JSON body decodes to.
	 * @throws LlmRequestException On missing config, transport error,
	 *                              non-200 response, or malformed JSON.
	 */
	public function request( $prompt, array $catalog, array $site_context = array() ) {
		$endpoint = get_option( 'shadcn_ai_layout_endpoint_url', '' );
		$token    = get_option( 'shadcn_ai_layout_auth_token', '' );

		if ( empty( $endpoint ) ) {
			throw new LlmRequestException( 'AI endpoint URL is not configured.' );
		}

		// HTTPS is normally enforced at save time (SettingsPage::sanitize_endpoint_url()),
		// but the option could be set through a path that bypasses that
		// sanitize callback (WP-CLI, direct DB import) — re-check here so
		// the bearer token is never sent in cleartext regardless of how the
		// option was set.
		if ( 0 !== strpos( $endpoint, 'https://' ) ) {
			throw new LlmRequestException( 'AI endpoint URL must use https://.' );
		}

		// Clamp below the environment's max_execution_time so PHP doesn't
		// kill the request before our own HTTP timeout would have fired.
		$max_execution_time = (int) ini_get( 'max_execution_time' );
		$timeout             = min( 30, $max_execution_time ?: 30 );

		$response = wp_remote_post(
			$endpoint,
			array(
				'timeout'             => $timeout,
				'limit_response_size' => self::MAX_RESPONSE_BYTES,
				'headers'             => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $token,
				),
				'body'                => wp_json_encode(
					array_merge(
						array(
							'prompt'  => $prompt,
							'catalog' => $catalog,
						),
						// Force object encoding — a PHP array with string keys
						// already encodes as an object, but never send an
						// empty [] where the proxy expects an object.
						empty( $site_context ) ? array() : array( 'siteContext' => (object) $site_context )
					)
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			throw new LlmRequestException( $response->get_error_message() );
		}

		$status_code = (int) wp_remote_retrieve_response_code( $response );

		if ( 200 !== $status_code ) {
			throw new LlmRequestException(
				sprintf( 'AI endpoint returned HTTP %d.', $status_code )
			);
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( null === $data && JSON_ERROR_NONE !== json_last_error() ) {
			throw new LlmRequestException( 'AI endpoint returned malformed JSON.' );
		}

		return $data;
	}
}
