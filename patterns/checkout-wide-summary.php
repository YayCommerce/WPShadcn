<?php
/**
 * Title: Checkout - Wide Summary
 * Slug: shadcn/checkout-wide-summary
 * Categories: shadcn, woocommerce
 * Inserter: no
 * Description: Even split between the form and a roomy order summary with larger line items.
 * AI Hint: Checkout split evenly between the form and a wide order summary column. Applied through the theme's Change layout picker on the Checkout block.
 *
 * WooCommerce renders the checkout with React at runtime, so this pattern only
 * supplies the block tree and the root attributes. The arrangement itself lives
 * in `is-checkout-wide-summary` in assets/css/checkout-layouts.css, and the picker
 * derives that class from this pattern's slug.
 */

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Block markup built by the theme.
echo \Shadcn\Patterns\CheckoutLayout::render(  );
