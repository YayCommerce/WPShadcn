<?php
/**
 * Title: Hero Section
 * Slug: shadcn/hero-section
 * Categories: shadcn, shadcn-banner
 * Description: A hero section with title, description, and call-to-action buttons.
 */

?>
<!-- wp:group {"tagName":"main","metadata":{"categories":["shadcn"],"patternName":"shadcn/hero-section","name":"Hero Section"},"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|10","left":"var:preset|spacing|4","right":"var:preset|spacing|4"}}},"layout":{"type":"constrained","contentSize":"800px"}} -->
<main class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--10);padding-right:var(--wp--preset--spacing--4);padding-bottom:var(--wp--preset--spacing--10);padding-left:var(--wp--preset--spacing--4)"><!-- wp:heading {"textAlign":"center","level":1} -->
<h1 class="wp-block-heading has-text-align-center"><?php esc_html_e( 'Welcome to Our Website', 'shadcn' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"var:preset|font-size|lg","lineHeight":"1.6"}}} -->
<p class="has-text-align-center" style="font-size:var(--wp--preset--font-size--lg);line-height:1.6"><?php esc_html_e( 'Discover amazing content and connect with our community. We\'re here to help you succeed with modern, accessible design.', 'shadcn' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"style":{"spacing":{"blockGap":"var:preset|spacing|4"}},"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-fill"} -->
<div class="wp-block-button is-style-fill"><a class="wp-block-button__link wp-element-button"><?php esc_html_e( 'Get Started', 'shadcn' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-ghost"} -->
<div class="wp-block-button is-style-ghost"><a class="wp-block-button__link wp-element-button"><?php esc_html_e( 'Learn More', 'shadcn' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></main>
<!-- /wp:group -->
