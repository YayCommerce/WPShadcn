<?php
/**
 * Title: Template Query Loop
 * Slug: shadcn/template-query-loop
 * Inserter: no
 * Categories: shadcn, blog
 * Description: Post list with pagination, used by the archive, index and search templates.
 * AI Hint: Query loop that inherits the template query; renders post cards in a responsive grid with pagination and a no-results message.
 */

?>

<!-- wp:query {"queryId":1,"query":{"perPage":9,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-query alignwide"><!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|6"}},"layout":{"type":"grid","minimumColumnWidth":"20rem"}} -->
<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|3","padding":{"top":"var:preset|spacing|4","right":"var:preset|spacing|4","bottom":"var:preset|spacing|4","left":"var:preset|spacing|4"}},"border":{"radius":"0.5rem","color":"var(--wp--preset--color--muted)","width":"1px"}},"backgroundColor":"card","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color has-card-background-color has-background" style="border-color:var(--wp--preset--color--muted);border-width:1px;border-radius:0.5rem;padding-top:var(--wp--preset--spacing--4);padding-right:var(--wp--preset--spacing--4);padding-bottom:var(--wp--preset--spacing--4);padding-left:var(--wp--preset--spacing--4)"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9","style":{"border":{"radius":"0.375rem"}}} /-->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|2"}},"textColor":"muted-foreground","fontSize":"xs","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group has-muted-foreground-color has-text-color has-xs-font-size"><!-- wp:post-date /-->

<!-- wp:post-terms {"term":"category"} /--></div>
<!-- /wp:group -->

<!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"typography":{"fontWeight":"600","lineHeight":"1.3"}},"fontSize":"xl"} /-->

<!-- wp:post-excerpt {"moreText":"Read more","excerptLength":24,"style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}}},"textColor":"muted-foreground","fontSize":"sm"} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-pagination {"style":{"spacing":{"margin":{"top":"var:preset|spacing|8"}}},"layout":{"type":"flex","justifyContent":"center"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"align":"center","textColor":"muted-foreground"} -->
<p class="has-text-align-center has-muted-foreground-color has-text-color">No posts found.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query -->
