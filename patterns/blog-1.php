<?php
/**
 * Title: Blog 1
 * Slug: shadcn/blog-1
 * Categories: shadcn, blog
 * Description: A list of blog posts.
 */

?>

<!-- wp:query {"queryId":14,"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"metadata":{"categories":["posts"],"patternName":"core/query-standard-posts","name":"Standard"}} -->
<div class="wp-block-query"><!-- wp:post-template -->
<!-- wp:post-featured-image {"isLink":true,"align":"wide","style":{"spacing":{"margin":{"bottom":"20px"}}}} /-->

<!-- wp:group {"style":{"spacing":{"blockGap":"10px","margin":{"bottom":"16px"}},"elements":{"link":{"color":{"text":"var:preset|color|muted-foreground"}}}},"textColor":"muted-foreground","fontSize":"xs","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group has-muted-foreground-color has-text-color has-link-color has-xs-font-size" style="margin-bottom:16px"><!-- wp:post-author-name /-->

<!-- wp:paragraph -->
<p>•</p>
<!-- /wp:paragraph -->

<!-- wp:post-date /--></div>
<!-- /wp:group -->

<!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"0px","bottom":"12px"}}}} /-->

<!-- wp:post-excerpt {"moreText":"Read more","style":{"elements":{"link":{"color":{"text":"var:preset|color|muted-foreground"}}}},"textColor":"muted-foreground"} /-->
<!-- /wp:post-template --></div>
<!-- /wp:query -->
