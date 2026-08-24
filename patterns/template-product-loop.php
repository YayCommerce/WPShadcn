<?php
/**
 * Title: Template Product Loop
 * Slug: shadcn/template-product-loop
 * Inserter: no
 * Categories: shadcn, woocommerce
 * Description: Product grid with results count, catalog sorting, pagination and a no-results message.
 * AI Hint: WooCommerce product collection grid that inherits the template query; used by product archive and product search templates.
 */

?>

<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|4","margin":{"bottom":"var:preset|spacing|6"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide" style="margin-bottom:var(--wp--preset--spacing--6)"><!-- wp:woocommerce/product-results-count /-->

<!-- wp:woocommerce/catalog-sorting /--></div>
<!-- /wp:group -->

<!-- wp:woocommerce/product-collection {"queryId":0,"query":{"woocommerceAttributes":[],"woocommerceStockStatus":["instock","outofstock","onbackorder"],"taxQuery":{},"isProductCollectionBlock":true,"perPage":12,"pages":0,"offset":0,"postType":"product","order":"asc","orderBy":"title","author":"","search":"","exclude":[],"sticky":"","inherit":true},"tagName":"div","dimensions":{"widthType":"fill","fixedWidth":""},"displayLayout":{"type":"flex","columns":3,"shrinkColumns":true},"convertedFromProducts":false,"queryContextIncludes":["collection"],"align":"wide"} -->
<div class="wp-block-woocommerce-product-collection alignwide"><!-- wp:woocommerce/product-template -->
<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|3","padding":{"top":"var:preset|spacing|4","right":"var:preset|spacing|4","bottom":"var:preset|spacing|4","left":"var:preset|spacing|4"}},"border":{"radius":"0.5rem","color":"var(--wp--preset--color--muted)","width":"1px"}},"backgroundColor":"card","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color has-card-background-color has-background" style="border-color:var(--wp--preset--color--muted);border-width:1px;border-radius:0.5rem;padding-top:var(--wp--preset--spacing--4);padding-right:var(--wp--preset--spacing--4);padding-bottom:var(--wp--preset--spacing--4);padding-left:var(--wp--preset--spacing--4)"><!-- wp:woocommerce/product-image {"imageSizing":"thumbnail","isDescendentOfQueryLoop":true,"style":{"border":{"radius":"0.375rem"}}} -->
<!-- wp:woocommerce/product-sale-badge {"isDescendentOfQueryLoop":true,"align":"right"} /-->
<!-- /wp:woocommerce/product-image -->

<!-- wp:post-title {"level":3,"isLink":true,"__woocommerceNamespace":"woocommerce/product-collection/product-title","style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"typography":{"fontWeight":"600","lineHeight":"1.4"}},"fontSize":"base"} /-->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"style":{"typography":{"fontWeight":"600"}},"fontSize":"lg"} /-->

<!-- wp:woocommerce/product-button {"isDescendentOfQueryLoop":true,"fontSize":"sm"} /--></div>
<!-- /wp:group -->
<!-- /wp:woocommerce/product-template -->

<!-- wp:query-pagination {"style":{"spacing":{"margin":{"top":"var:preset|spacing|8"}}},"layout":{"type":"flex","justifyContent":"center"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:woocommerce/product-collection-no-results -->
<!-- wp:paragraph {"align":"center","textColor":"muted-foreground"} -->
<p class="has-text-align-center has-muted-foreground-color has-text-color">No products found.</p>
<!-- /wp:paragraph -->
<!-- /wp:woocommerce/product-collection-no-results --></div>
<!-- /wp:woocommerce/product-collection -->
