<?php
/**
 * Title: Template Related Products
 * Slug: shadcn/template-related-products
 * Inserter: no
 * Categories: shadcn, woocommerce
 * Description: Related products grid shown at the bottom of the single product template.
 * AI Hint: Product collection using the "related" collection, matched by category and tag.
 */

?>

<!-- wp:woocommerce/product-collection {"queryId":0,"query":{"perPage":4,"pages":1,"offset":0,"postType":"product","order":"asc","orderBy":"title","search":"","exclude":[],"inherit":false,"taxQuery":[],"isProductCollectionBlock":true,"featured":false,"woocommerceOnSale":false,"woocommerceStockStatus":["instock","onbackorder"],"woocommerceAttributes":[],"woocommerceHandPickedProducts":[],"filterable":false,"relatedBy":{"categories":true,"tags":true}},"tagName":"div","displayLayout":{"type":"flex","columns":4,"shrinkColumns":true},"dimensions":{"widthType":"fill"},"collection":"woocommerce/product-collection/related","hideControls":["inherit"],"queryContextIncludes":["collection"],"align":"wide"} -->
<div class="wp-block-woocommerce-product-collection alignwide"><!-- wp:heading {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|6"}},"typography":{"fontWeight":"700"}},"fontSize":"2-xl"} -->
<h2 class="wp-block-heading has-2-xl-font-size" style="margin-top:var(--wp--preset--spacing--10);margin-bottom:var(--wp--preset--spacing--6);font-weight:700">Related products</h2>
<!-- /wp:heading -->

<!-- wp:woocommerce/product-template -->
<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|3","padding":{"top":"var:preset|spacing|4","right":"var:preset|spacing|4","bottom":"var:preset|spacing|4","left":"var:preset|spacing|4"}},"border":{"radius":"0.5rem","color":"var(--wp--preset--color--muted)","width":"1px"}},"backgroundColor":"card","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color has-card-background-color has-background" style="border-color:var(--wp--preset--color--muted);border-width:1px;border-radius:0.5rem;padding-top:var(--wp--preset--spacing--4);padding-right:var(--wp--preset--spacing--4);padding-bottom:var(--wp--preset--spacing--4);padding-left:var(--wp--preset--spacing--4)"><!-- wp:woocommerce/product-image {"imageSizing":"thumbnail","isDescendentOfQueryLoop":true,"style":{"border":{"radius":"0.375rem"}}} -->
<!-- wp:woocommerce/product-sale-badge {"isDescendentOfQueryLoop":true,"align":"right"} /-->
<!-- /wp:woocommerce/product-image -->

<!-- wp:post-title {"level":3,"isLink":true,"__woocommerceNamespace":"woocommerce/product-collection/product-title","style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"typography":{"fontWeight":"600","lineHeight":"1.4"}},"fontSize":"base"} /-->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"style":{"typography":{"fontWeight":"600"}},"fontSize":"lg"} /--></div>
<!-- /wp:group -->
<!-- /wp:woocommerce/product-template --></div>
<!-- /wp:woocommerce/product-collection -->
