<?php
/**
 * Title: Header Section Centered Text
 * Slug: shadcn/header-section-centered-text
 * Categories: shadcn, header
 * Description: A header section with centered title, subtitle, and call-to-action button.
 * AI Hint: Centered page-title band with H1 and subtitle, no buttons; best as the heading of interior pages like About or Contact.
 */

?>

<!-- wp:group {"metadata":{"name":"Header Section Centered Text"},"className":"section-padding-y","style":{"spacing":{"padding":{"top":"var:preset|spacing|9","bottom":"var:preset|spacing|9"}}},"backgroundColor":"background"} -->
<div class="wp-block-group section-padding-y has-background-background-color has-background"
	style="padding-top:var(--wp--preset--spacing--9);padding-bottom:var(--wp--preset--spacing--9)">
	<!-- wp:group {"className":"container-padding-x container mx-auto"} -->
	<div class="wp-block-group container-padding-x container mx-auto">
		<!-- wp:group {"className":"section-title-gap-xl mx-auto has-text-align-center","layout":{"type":"constrained","contentSize":"576px"}} -->
		<div class="wp-block-group section-title-gap-xl mx-auto has-text-align-center">
			<!-- wp:group {"className":"section-title-gap-xl","layout":{"type":"constrained"}} -->
			<div class="wp-block-group section-title-gap-xl">
				<!-- wp:paragraph {"metadata":{"name":"Subtitle"},"className":"tagline","fontSize":"sm"} -->
				<p class="tagline has-sm-font-size">Header section</p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"level":1,"className":"heading-xl","textColor":"foreground"} -->
				<h1 class="wp-block-heading heading-xl has-foreground-color has-text-color">Short engaging headline</h1>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"lg:text-lg","textColor":"muted-foreground","fontSize":"base"} -->
				<p class="lg:text-lg has-muted-foreground-color has-text-color has-base-font-size">Lorem ipsum dolor sit
					amet, consectetur adipiscing elit interdum hendrerit ex vitae sodales.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->