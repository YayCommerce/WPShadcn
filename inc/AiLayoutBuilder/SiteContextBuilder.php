<?php
/**
 * Auto-derives the site's business identity for the AI Layout Builder.
 *
 * Collected signals: site title, tagline, and (when WooCommerce is active)
 * the most-used product categories. Empty or known-default values are
 * omitted entirely so the LLM never sees blank or misleading hints —
 * a site with no usable signals sends no siteContext at all.
 *
 * @package Shadcn
 * @since 1.0.0
 */

namespace Shadcn\AiLayoutBuilder;

class SiteContextBuilder {

	/**
	 * Most product categories that add signal; more just adds tokens.
	 */
	const MAX_CATEGORIES = 5;

	/**
	 * Taglines that describe nothing — WP's install default.
	 */
	const DEFAULT_TAGLINES = array( 'Just another WordPress site' );

	/**
	 * Build the context. Keys are omitted when they carry no signal.
	 *
	 * @return array{siteName?: string, tagline?: string, productCategories?: string[]}
	 */
	public function build() {
		$context = array();

		$name = trim( (string) get_bloginfo( 'name' ) );
		if ( '' !== $name ) {
			$context['siteName'] = $name;
		}

		$tagline = trim( (string) get_bloginfo( 'description' ) );
		if ( '' !== $tagline && ! in_array( $tagline, self::DEFAULT_TAGLINES, true ) ) {
			$context['tagline'] = $tagline;
		}

		$categories = $this->product_categories();
		if ( ! empty( $categories ) ) {
			$context['productCategories'] = $categories;
		}

		return $context;
	}

	/**
	 * Top product categories by product count. Empty when WooCommerce is
	 * inactive or the store has none worth mentioning.
	 *
	 * @return string[]
	 */
	private function product_categories() {
		if ( ! class_exists( 'WooCommerce' ) || ! taxonomy_exists( 'product_cat' ) ) {
			return array();
		}

		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'orderby'    => 'count',
				'order'      => 'DESC',
				'number'     => self::MAX_CATEGORIES,
				'hide_empty' => true,
			)
		);

		if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
			return array();
		}

		$names = array();

		foreach ( $terms as $term ) {
			if ( 'uncategorized' === $term->slug ) {
				continue;
			}

			$names[] = $term->name;
		}

		return $names;
	}
}
