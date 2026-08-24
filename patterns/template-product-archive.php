<?php
/**
 * Title: Template Product Archive
 * Slug: shadcn/template-product-archive
 * Inserter: no
 * Categories: shadcn, woocommerce
 * Description: Main area of a product archive - breadcrumbs, archive title, term description, store notices and the product grid.
 * AI Hint: Shared body for the shop, product category, product tag, product brand and product attribute templates.
 */

?>

<!-- wp:group {"tagName":"main","style":{"spacing":{"blockGap":"var:preset|spacing|4","padding":{"top":"var:preset|spacing|8","bottom":"var:preset|spacing|10","left":"var:preset|spacing|4","right":"var:preset|spacing|4"}}},"layout":{"type":"constrained"}} -->
<main class="wp-block-group" style="padding-top:var(--wp--preset--spacing--8);padding-right:var(--wp--preset--spacing--4);padding-bottom:var(--wp--preset--spacing--10);padding-left:var(--wp--preset--spacing--4)"><!-- wp:woocommerce/breadcrumbs {"align":"wide","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|6"}}},"textColor":"muted-foreground","fontSize":"sm"} /-->

<!-- wp:query-title {"type":"archive","showPrefix":false,"level":1,"align":"wide","style":{"typography":{"fontWeight":"700","lineHeight":"1.1"},"spacing":{"margin":{"bottom":"var:preset|spacing|2"}}},"fontSize":"4-xl"} /-->

<!-- wp:term-description {"align":"wide","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|8"}}},"textColor":"muted-foreground","fontSize":"base"} /-->

<!-- wp:woocommerce/store-notices {"align":"wide"} /-->

<!-- wp:pattern {"slug":"shadcn/template-product-loop"} /--></main>
<!-- /wp:group -->
