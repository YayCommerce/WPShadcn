<?php
/**
 * Title: Checkout - Single Column
 * Slug: shadcn/checkout-single-column
 * Categories: shadcn, woocommerce
 * Inserter: no
 * Description: Order summary stacked above a narrow, centred checkout form.
 * AI Hint: Single column checkout: summary on top, form below, constrained to a readable width. Applied through the theme's Change layout picker on the Checkout block.
 *
 * WooCommerce renders the checkout with React at runtime, so this pattern only
 * supplies the block tree and the root attributes. The arrangement itself lives
 * in `is-checkout-single-column` in assets/css/checkout-layouts.css, and the picker
 * derives that class from this pattern's slug.
 */

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Block markup built by the theme.
echo \Shadcn\Patterns\CheckoutLayout::render(  );
