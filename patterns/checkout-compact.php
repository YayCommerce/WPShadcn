<?php
/**
 * Title: Checkout - Compact
 * Slug: shadcn/checkout-compact
 * Categories: shadcn, woocommerce
 * Inserter: no
 * Description: Dense spacing and flat sections for shops with short forms.
 * AI Hint: Compact checkout: tighter spacing and a flat order summary with no card chrome. Applied through the theme's Change layout picker on the Checkout block.
 *
 * WooCommerce renders the checkout with React at runtime, so this pattern only
 * supplies the block tree and the root attributes. The arrangement itself lives
 * in `is-checkout-compact` in assets/css/checkout-layouts.css, and the picker
 * derives that class from this pattern's slug.
 */

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Block markup built by the theme.
echo \Shadcn\Patterns\CheckoutLayout::render(  );
