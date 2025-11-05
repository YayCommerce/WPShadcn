<?php
/**
 * Title: Header 1
 * Slug: shadcn/header-1
 * Categories: shadcn, header
 * Description: A header with logo, title, tagline, dark mode toggle, navigation, and customer account and mini cart.
 */

$darkmode_image_url = get_template_directory_uri() . '/assets/images/dark-mode.png';

?>

<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
	<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|4","padding":{"top":"var:preset|spacing|4","bottom":"var:preset|spacing|4","left":"0","right":"0"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group alignwide"
		style="padding-top:var(--wp--preset--spacing--4);padding-right:0;padding-bottom:var(--wp--preset--spacing--4);padding-left:0">
		<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|4"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
		<div class="wp-block-group">
			<!-- wp:site-logo {"width":40,"shouldSyncIcon":true,"style":{"border":{"radius":"0.375rem"}}} /-->

			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|1","padding":{"right":"0","left":"0","top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0">
				<!-- wp:site-title {"level":0,"style":{"typography":{"fontWeight":"600","textDecoration":"none","fontStyle":"normal"}},"fontSize":"2-xl"} /-->

				<!-- wp:site-tagline {"style":{"typography":{"fontSize":"var:preset|font-size|sm"},"color":{"text":"var:preset|color|muted-foreground"}}} /-->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"style":{"spacing":{"blockGap":"10px"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group">
			<!-- wp:image {"id":12,"width":"20px","sizeSlug":"full","linkDestination":"none","className":"dark-mode-toggle","style":{"color":{"duotone":["rgb(0, 0, 0)","rgb(255, 255, 255)"]}}} -->
			<figure class="wp-block-image size-full is-resized dark-mode-toggle"><img
					src="<?php echo esc_url( $darkmode_image_url ); ?>" alt="" class="wp-image-12"
					style="width:20px" /></figure>
			<!-- /wp:image -->

			<!-- wp:navigation {"overlayBackgroundColor":"background","overlayTextColor":"foreground","className":"is-style-pill","style":{"spacing":{"blockGap":"var:preset|spacing|5"}},"layout":{"type":"flex","justifyContent":"right"}} /-->

			<?php if ( function_exists( 'WC' ) ) : ?>
			<!-- wp:woocommerce/customer-account {"displayStyle":"icon_only","iconStyle":"line","iconClass":"wc-block-customer-account__account-icon","style":{"typography":{"fontSize":"var(\u002d\u002dwp\u002d\u002dpreset\u002d\u002dfont-size\u002d\u002dsm)"}}} /-->

			<!-- wp:woocommerce/mini-cart {"cartAndCheckoutRenderStyle":"removed","style":{"typography":{"fontSize":"var:preset|font-size|sm"}}} /-->
			 <?php endif; ?>
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
