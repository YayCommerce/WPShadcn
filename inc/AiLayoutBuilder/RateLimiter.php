<?php
/**
 * Per-user, per-hour rate limiter for the AI Layout Builder REST route.
 *
 * Uses a direct atomic SQL increment against wp_options rather than
 * get_transient()/set_transient() or wp_cache_incr(): a plain transient
 * read-then-write is a TOCTOU race under concurrent requests, and
 * wp_cache_incr() is only cross-request-safe when a persistent object cache
 * (Redis/Memcached) is installed, which cannot be assumed here. The
 * INSERT ... ON DUPLICATE KEY UPDATE below is serialized at the database row
 * level regardless of object-cache configuration.
 *
 * @package Shadcn
 * @since 1.0.0
 */

namespace Shadcn\AiLayoutBuilder;

class RateLimiter {

	/**
	 * Placeholder threshold — no real usage data exists yet to size this
	 * properly (see plan.md Open Questions). Tune via this constant.
	 */
	const LIMIT_PER_HOUR = 20;

	/**
	 * @param int $user_id
	 * @return bool True if this request is allowed under the current hour's cap.
	 */
	public function is_allowed( $user_id ) {
		global $wpdb;

		$option_name = $this->option_name( $user_id );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- atomic increment has no Settings-API equivalent.
		$write_result = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$wpdb->options} (option_name, option_value, autoload)
				VALUES (%s, '1', 'no')
				ON DUPLICATE KEY UPDATE option_value = option_value + 1",
				$option_name
			)
		);

		// Fail closed (deny) on a DB error, matching the "fail closed on
		// cost" design goal — a silent failure here must not be
		// indistinguishable from "count is 0, request allowed".
		if ( false === $write_result ) {
			return false;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- reading the just-incremented row.
		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT option_value FROM {$wpdb->options} WHERE option_name = %s",
				$option_name
			)
		);

		if ( null === $count ) {
			return false; // Read failed right after a successful write — fail closed, not "0 requests so far".
		}

		return (int) $count <= self::LIMIT_PER_HOUR;
	}

	/**
	 * One row per user per hour bucket — rows naturally stop growing once a
	 * user stops using the tool. No cleanup job exists yet (accepted v1
	 * limitation: row count is bounded by realistic usage, not unbounded).
	 */
	private function option_name( $user_id ) {
		return sprintf( 'shadcn_ai_layout_rl_%d_%s', (int) $user_id, gmdate( 'YmdH' ) );
	}
}
