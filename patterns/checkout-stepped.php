<?php
/**
 * Title: Checkout - Stepped
 * Slug: shadcn/checkout-stepped
 * Categories: shadcn, woocommerce
 * Inserter: no
 * Description: Numbered checkout steps, each in its own boxed section, beside the order summary.
 * AI Hint: Checkout with numbered form steps and each section drawn as a separate card. Applied through the theme's Change layout picker on the Checkout block.
 *
 * WooCommerce renders the checkout with React at runtime, so this pattern only
 * supplies the block tree and the root attributes. The arrangement itself lives
 * in `is-checkout-stepped` in assets/css/checkout-layouts.css, and the picker
 * derives that class from this pattern's slug.
 */

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Block markup built by the theme.
echo \Shadcn\Patterns\CheckoutLayout::render(
	array(
		'showFormStepNumbers' => true,
	)
);
