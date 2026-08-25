<?php
/**
 * Title: Mini Cart - Bold Header
 * Slug: shadcn/mini-cart-bold-header
 * Block Types: core/template-part/mini-cart
 * Categories: shadcn, woocommerce
 * Inserter: no
 * Description: Mini-Cart drawer layout with a solid title bar above a standard item list.
 * AI Hint: Alternative Mini-Cart drawer layout; applied through the theme's Change layout picker.
 *
 * The block tree is repeated in full in every mini-cart-*.php pattern rather
 * than shared through a helper. WordPress registers patterns by executing the
 * file during `init`, so a shared helper would tie all six layouts to load
 * ordering: if it were ever unavailable, every pattern would register empty and
 * the picker would silently break. Duplicated markup fails far more visibly.
 * Keep every child block WooCommerce expects - dropping one breaks the drawer.
 */

?>

<!-- wp:woocommerce/mini-cart-contents {"className":"is-mini-cart-bold-header"} -->
<div class="wp-block-woocommerce-mini-cart-contents is-mini-cart-bold-header"><!-- wp:woocommerce/filled-mini-cart-contents-block -->
<div class="wp-block-woocommerce-filled-mini-cart-contents-block"><!-- wp:woocommerce/mini-cart-title-block -->
<div class="wp-block-woocommerce-mini-cart-title-block"><!-- wp:woocommerce/mini-cart-title-label-block -->
<div class="wp-block-woocommerce-mini-cart-title-label-block"></div>
<!-- /wp:woocommerce/mini-cart-title-label-block -->

<!-- wp:woocommerce/mini-cart-title-items-counter-block -->
<div class="wp-block-woocommerce-mini-cart-title-items-counter-block"></div>
<!-- /wp:woocommerce/mini-cart-title-items-counter-block --></div>
<!-- /wp:woocommerce/mini-cart-title-block -->

<!-- wp:woocommerce/mini-cart-items-block -->
<div class="wp-block-woocommerce-mini-cart-items-block"><!-- wp:woocommerce/mini-cart-products-table-block -->
<div class="wp-block-woocommerce-mini-cart-products-table-block"></div>
<!-- /wp:woocommerce/mini-cart-products-table-block --></div>
<!-- /wp:woocommerce/mini-cart-items-block -->

<!-- wp:woocommerce/product-collection {"queryId":0,"query":{"perPage":3,"pages":1,"offset":0,"postType":"product","order":"asc","orderBy":"title","search":"","exclude":[],"inherit":false,"taxQuery":[],"isProductCollectionBlock":true,"featured":false,"woocommerceOnSale":false,"woocommerceStockStatus":["instock"],"woocommerceAttributes":[],"woocommerceHandPickedProducts":[],"filterable":false},"tagName":"div","displayLayout":{"type":"flex","columns":3,"shrinkColumns":true},"dimensions":{"widthType":"fill"},"collection":"woocommerce/product-collection/best-sellers","hideControls":["inherit"],"queryContextIncludes":["collection"],"className":"is-mini-cart-suggestions"} -->
<div class="wp-block-woocommerce-product-collection is-mini-cart-suggestions"><!-- wp:heading {"level":3,"style":{"typography":{"fontWeight":"600"}},"fontSize":"sm"} -->
<h3 class="wp-block-heading has-sm-font-size" style="font-weight:600">You might also like</h3>
<!-- /wp:heading -->

<!-- wp:woocommerce/product-template -->
<!-- wp:woocommerce/product-image {"imageSizing":"thumbnail","isDescendentOfQueryLoop":true,"style":{"border":{"radius":"0.375rem"}}} /-->

<!-- wp:post-title {"level":4,"isLink":true,"__woocommerceNamespace":"woocommerce/product-collection/product-title","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"fontSize":"xs"} /-->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"fontSize":"xs"} /-->
<!-- wp:woocommerce/product-button {"isDescendentOfQueryLoop":true,"fontSize":"xs"} /-->
<!-- /wp:woocommerce/product-template --></div>
<!-- /wp:woocommerce/product-collection -->

<!-- wp:woocommerce/mini-cart-footer-block -->
<div class="wp-block-woocommerce-mini-cart-footer-block"><!-- wp:woocommerce/mini-cart-cart-button-block -->
<div class="wp-block-woocommerce-mini-cart-cart-button-block"></div>
<!-- /wp:woocommerce/mini-cart-cart-button-block -->

<!-- wp:woocommerce/mini-cart-checkout-button-block -->
<div class="wp-block-woocommerce-mini-cart-checkout-button-block"></div>
<!-- /wp:woocommerce/mini-cart-checkout-button-block --></div>
<!-- /wp:woocommerce/mini-cart-footer-block --></div>
<!-- /wp:woocommerce/filled-mini-cart-contents-block -->

<!-- wp:woocommerce/empty-mini-cart-contents-block -->
<div class="wp-block-woocommerce-empty-mini-cart-contents-block"><!-- wp:pattern {"slug":"woocommerce/mini-cart-empty-cart-message"} /-->

<!-- wp:woocommerce/mini-cart-shopping-button-block -->
<div class="wp-block-woocommerce-mini-cart-shopping-button-block"></div>
<!-- /wp:woocommerce/mini-cart-shopping-button-block --></div>
<!-- /wp:woocommerce/empty-mini-cart-contents-block --></div>
<!-- /wp:woocommerce/mini-cart-contents -->
