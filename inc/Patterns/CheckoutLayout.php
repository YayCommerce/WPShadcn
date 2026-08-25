<?php

namespace Shadcn\Patterns;

/**
 * Block markup for the Checkout layout patterns.
 *
 * Every checkout layout ships the same block tree. WooCommerce renders the
 * checkout with React at runtime, so the markup only decides two things: the
 * order of the sections, and the classes and attributes on the wrapper. The
 * layouts differ in the second, which is why one builder can serve all of them
 * rather than six near-identical pattern files.
 *
 * The tree has to stay complete. `checkout-fields-block` and
 * `checkout-totals-block` declare a `defaultTemplate`, and WooCommerce's forced
 * layout puts any missing child back at its default position the moment the
 * block is edited - so dropping a section here would silently reappear.
 *
 * The wrapper deliberately carries no layout class. The picker writes the class
 * through `updateBlockAttributes`, letting the editor serialize it, so the
 * pattern never has to guess the class order that block validation expects.
 */
class CheckoutLayout {

	/**
	 * Sections inside checkout-fields-block, in render order.
	 */
	private const FIELD_BLOCKS = array(
		'express-payment',
		'contact-information',
		'shipping-method',
		'pickup-options',
		'shipping-address',
		'billing-address',
		'shipping-methods',
		'payment',
		'additional-information',
		'order-note',
		'terms',
		'actions',
	);

	/**
	 * Sections inside checkout-order-summary-totals-block, in render order.
	 */
	private const TOTALS_BLOCKS = array(
		'subtotal',
		'fee',
		'discount',
		'shipping',
		'taxes',
	);

	/**
	 * One self-closing WooCommerce checkout child block.
	 *
	 * @param string $slug    Block slug without the `woocommerce/checkout-`
	 *                        prefix or the `-block` suffix.
	 * @param string $content Inner markup, if the block wraps other blocks.
	 * @return string
	 */
	private static function block( $slug, $content = '' ) {
		$name = 'woocommerce/checkout-' . $slug . '-block';

		return sprintf(
			'<!-- wp:%1$s --><div class="wp-block-%2$s">%3$s</div><!-- /wp:%1$s -->',
			$name,
			str_replace( '/', '-', $name ),
			$content
		);
	}

	/**
	 * Build a checkout layout pattern.
	 *
	 * @param array $attributes Attributes for the root `woocommerce/checkout`
	 *                          block. Pass every attribute a layout cares about,
	 *                          including the ones it wants switched off: the
	 *                          picker copies this set onto the block, so an
	 *                          omitted attribute keeps whatever the previous
	 *                          layout left behind.
	 * @return string
	 */
	public static function render( array $attributes = array() ) {
		$attributes = array_merge(
			array(
				'align'               => 'wide',
				'showFormStepNumbers' => false,
			),
			$attributes
		);

		$fields = '';

		foreach ( self::FIELD_BLOCKS as $slug ) {
			$fields .= self::block( $slug );
		}

		$totals = '';

		foreach ( self::TOTALS_BLOCKS as $slug ) {
			$totals .= self::block( 'order-summary-' . $slug );
		}

		$summary = self::block( 'order-summary-cart-items' )
			. self::block( 'order-summary-coupon-form' )
			. self::block( 'order-summary-totals', $totals );

		return sprintf(
			'<!-- wp:woocommerce/checkout %1$s --><div class="wp-block-woocommerce-checkout alignwide wc-block-checkout is-loading">%2$s%3$s</div><!-- /wp:woocommerce/checkout -->',
			wp_json_encode( $attributes ),
			self::block( 'fields', $fields ),
			self::block( 'totals', self::block( 'order-summary', $summary ) )
		);
	}
}
