<?php
/**
 * Welcome to pootle page builder settings page template
 * @author pootlepress
 * @since 0.1.0
 */

$ajax_with_nonce = wp_nonce_url( admin_url( 'admin-ajax.php' ), 'ppb-new-live-post', 'ppbLiveEditor' )

/** Template start */
?>
<div class="wrap ppb-welcome about-wrap">

	<h1><?php _e( 'Welcome to Pootle Pagebuilder', 'pootle-page-builder' ); ?></h1>

	<div class="about-text ppb-about-text">
		<?php _e( 'Thank you for using Pootle Pagebuilder. The aim of Pootle Pagebuilder is to help you create compelling WordPress pages more easily. We hope you like it.', 'pootle-page-builder' ); ?>
	</div>

	<div class="ppb-badge"></div>

	<p class="ppb-actions">
		<a href="<?php
		echo "$ajax_with_nonce&action=pootlepb_live_page&tour=1";
		?>" class="button pootle"><?php _e( 'Tour', 'pootle-page-builder' ); ?></a>
		<a href="<?php
		echo esc_url( admin_url( 'admin.php?page=page_builder_settings' ) ); ?>" class="button pootle"><?php _e( 'Settings', 'pootle-page-builder' ); ?></a>
		<a href="http://docs.pootlepress.com/" class="button pootle"><?php _e( 'Docs', 'pootle-page-builder' ); ?></a>
		<b><?php printf( __( 'Version %s', 'pootle-page-builder' ), POOTLEPB_VERSION ); ?></b>
	</p>
	<hr>
	<?php echo '<div class="ppb-video-container">' . wp_oembed_get( 'https://vimeo.com/215980602' ) . '</div>'; ?>
	<div class="ppb-side-50">
		<h3><?php _e( 'Free features', 'pootle-page-builder' ); ?></h3>

		<ul>
			<li><a class="thickbox" href="https://player.vimeo.com/video/185787894?k=v&TB_iframe=true&height=540&width=960"><?php _e( 'Basic usage', 'pootle-page-builder' ); ?></a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/180052795?k=v&TB_iframe=true&height=540&width=960"><?php _e( 'Adding drag and drop modules into page', 'pootle-page-builder' ); ?></a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/192113398?k=v&TB_iframe=true&height=540&width=960"><?php _e( 'Adding a hero photo to the top of your page', 'pootle-page-builder' ); ?></a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/185792519?k=v&TB_iframe=true&height=540&width=960"><?php _e( 'Snap to grid for pixel perfect layout', 'pootle-page-builder' ); ?></a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/197875188?k=v&TB_iframe=true&height=540&width=960"><?php _e( 'Row Animations', 'pootle-page-builder' ); ?></a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/185800528?k=v&TB_iframe=true&height=540&width=960"><?php _e( 'Background image effects (parallax)', 'pootle-page-builder' ); ?></a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/185803787?k=v&TB_iframe=true&height=540&width=960"><?php _e( 'Gradient backgrounds', 'pootle-page-builder' ); ?></a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/185800527?k=v&TB_iframe=true&height=540&width=960"><?php _e( 'Accordians', 'pootle-page-builder' ); ?></a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/185801458?k=v&TB_iframe=true&height=540&width=960"><?php _e( 'Adding icons', 'pootle-page-builder' ); ?></a></li>
		</ul>
	</div>
	<div class="ppb-side-50">
		<h3><?php _e( 'Pro features', 'pootle-page-builder' ); ?></h3>
		<ul>
			<li><a class="thickbox" href="https://player.vimeo.com/video/197584823?k=v&TB_iframe=true&height=540&width=960"><?php _e( 'WooCommerce Builder (for individual products)', 'pootle-page-builder' ); ?></a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/197742676?k=v&TB_iframe=true&height=540&width=960"><?php _e( 'WooCommerce Builder (for your shop pages)', 'pootle-page-builder' ); ?></a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/163400847?k=v&TB_iframe=true&height=540&width=960"><?php _e( 'Blog and Posts customizer', 'pootle-page-builder' ); ?></a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/191164397?k=v&TB_iframe=true&height=540&width=960"><?php _e( 'Create landing pages, sale pages and squeeze pages', 'pootle-page-builder' ); ?></a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/197873693?k=v&TB_iframe=true&height=540&width=960"><?php _e( 'One Page websites', 'pootle-page-builder' ); ?></a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/190233819?k=v&TB_iframe=true&height=540&width=960"><?php _e( 'Pootle slider', 'pootle-page-builder' ); ?></a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/166312827?k=v&TB_iframe=true&height=540&width=960"><?php _e( 'Pro slideshows and galleries', 'pootle-page-builder' ); ?></a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/166309495?k=v&TB_iframe=true&height=540&width=960"><?php _e( 'Starter templates', 'pootle-page-builder' ); ?></a></li>
		</ul>
		&nbsp;
	</div>
</div>