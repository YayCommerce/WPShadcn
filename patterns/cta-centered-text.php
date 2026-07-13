<?php
/**
 * Title: CTA Centered Text
 * Slug: shadcn/cta-centered-text
 * Categories: shadcn, cta
 * Description: A CTA with centered text.
 * AI Hint: Centered H2, one supporting line, and a single button on a plain band; best as the closing call to action of a page.
 */

?>

<!-- wp:group {"metadata":{"name":"CTA Centered Text"},"className":"section-padding-y","style":{"spacing":{"padding":{"top":"var:preset|spacing|9","bottom":"var:preset|spacing|9"}},"border":{"radius":"8px"}},"backgroundColor":"primary"} -->
<div class="wp-block-group section-padding-y has-primary-background-color has-background"
	style="border-radius:8px;padding-top:var(--wp--preset--spacing--9);padding-bottom:var(--wp--preset--spacing--9)">
	<!-- wp:group {"className":"container-padding-x container mx-auto"} -->
	<div class="wp-block-group container-padding-x container mx-auto">
		<!-- wp:group {"className":"mx-auto md:gap-10","style":{"spacing":{"blockGap":"var:preset|spacing|8"}},"layout":{"type":"constrained","contentSize":"576px"}} -->
		<div class="wp-block-group mx-auto md:gap-10">
			<!-- wp:group {"className":"section-title-gap-lg mx-auto has-text-align-center","layout":{"type":"constrained","contentSize":"576px"}} -->
			<div class="wp-block-group section-title-gap-lg mx-auto has-text-align-center">
				<!-- wp:paragraph {"metadata":{"name":"Subtitle"},"className":"tagline has-muted-foreground-color has-text-color has-sm-font-size","textColor":"muted-foreground","fontSize":"sm"} -->
				<p class="tagline has-muted-foreground-color has-text-color has-sm-font-size">CTA Section</p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"className":"heading-lg","textColor":"primary-foreground"} -->
				<h2 class="wp-block-heading heading-lg has-primary-foreground-color has-text-color">Action-driving
					headline that creates urgency</h2>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"has-muted-foreground-color has-text-color","textColor":"muted-foreground"} -->
				<p class="has-muted-foreground-color has-text-color has-muted-foreground-color has-text-color">Add one
					or two compelling sentences that reinforce your main value proposition and overcome final
					objections. End with a clear reason to act now. Align this copy with your CTA button text.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->

			<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
			<div class="wp-block-buttons">
				<!-- wp:button {"backgroundColor":"primary-foreground","textColor":"primary"} -->
				<div class="wp-block-button"><a
						class="wp-block-button__link has-primary-color has-primary-foreground-background-color has-text-color has-background wp-element-button">Get
						started</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->