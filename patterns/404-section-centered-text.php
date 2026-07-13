<?php
/**
 * Title: 404 Section Centered Text
 * Slug: shadcn/404-section-centered-text
 * Categories: shadcn, 404
 * Description: A 404 section with centered title, subtitle, and call-to-action button.
 * AI Hint: Centered H1, short apology text, and one back-to-home button; best for 404 or error pages only.
 */

?>

<!-- wp:group {"metadata":{"name":"404 Section Centered Text"},"className":"section-padding-y","style":{"spacing":{"padding":{"top":"var:preset|spacing|9","bottom":"var:preset|spacing|9"}}},"backgroundColor":"background"} -->
<div class="wp-block-group section-padding-y has-background-background-color has-background"
	style="padding-top:var(--wp--preset--spacing--9);padding-bottom:var(--wp--preset--spacing--9)">
	<!-- wp:group {"className":"container-padding-x relative z-10 container mx-auto lg:flex-row lg:gap-16","style":{"spacing":{"blockGap":"var:preset|spacing|12"}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group container-padding-x relative z-10 container mx-auto lg:flex-row lg:gap-16">
		<!-- wp:group {"className":"m-auto has-text-align-center lg:gap-8","style":{"spacing":{"blockGap":"var:preset|spacing|6"}},"layout":{"type":"constrained","contentSize":"576px"}} -->
		<div class="wp-block-group m-auto has-text-align-center lg:gap-8">
			<!-- wp:group {"className":"section-title-gap-xl has-text-align-center","layout":{"type":"constrained"}} -->
			<div class="wp-block-group section-title-gap-xl has-text-align-center">
				<!-- wp:paragraph {"metadata":{"name":"Subtitle"},"className":"tagline","fontSize":"sm"} -->
				<p class="tagline has-sm-font-size">404 Section</p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"level":1,"className":"heading-xl"} -->
				<h1 class="wp-block-heading heading-xl">Page not found</h1>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"lg:text-lg","textColor":"muted-foreground","fontSize":"base"} -->
				<p class="lg:text-lg has-muted-foreground-color has-text-color has-base-font-size">Sorry, we
					couldn&#039;t find the page you&#039;re looking for. Please check the URL or navigate back home.</p>
				<!-- /wp:paragraph -->

			</div>
			<!-- /wp:group -->

			<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
			<div class="wp-block-buttons">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">Go to homepage</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

		</div>
		<!-- /wp:group -->

	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->