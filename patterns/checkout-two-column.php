<?php
/**
 * Title: Checkout - Two Column
 * Slug: shadcn/checkout-two-column
 * Categories: shadcn, woocommerce
 * Inserter: no
 * Description: Checkout form beside a sticky order summary on the right. The theme default.
 * AI Hint: Default checkout arrangement: form in a wide left column, order summary card on the right. Applied through the theme's Change layout picker on the Checkout block.
 *
 * WooCommerce renders the checkout with React at runtime, so this pattern only
 * supplies the block tree and the root attributes. The arrangement itself lives
 * in `is-checkout-two-column` in assets/css/checkout-layouts.css, and the picker
 * derives that class from this pattern's slug.
 */

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Block markup built by the theme.
echo \Shadcn\Patterns\CheckoutLayout::render(  );
