<?php
/**
 * Title: Checkout - Summary First
 * Slug: shadcn/checkout-summary-left
 * Categories: shadcn, woocommerce
 * Inserter: no
 * Description: Order summary in the left column so shoppers review the cart before filling the form.
 * AI Hint: Checkout with the order summary moved to the left column, ahead of the form. Applied through the theme's Change layout picker on the Checkout block.
 *
 * WooCommerce renders the checkout with React at runtime, so this pattern only
 * supplies the block tree and the root attributes. The arrangement itself lives
 * in `is-checkout-summary-left` in assets/css/checkout-layouts.css, and the picker
 * derives that class from this pattern's slug.
 */

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Block markup built by the theme.
echo \Shadcn\Patterns\CheckoutLayout::render(  );
