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

	<h1>Welcome to pootle page builder</h1>

	<div class="about-text ppb-about-text">
		Thank you for using pootle page builder. The aim of pootle page builder is to help you create compelling WordPress pages more easily. We hope you like it.
	</div>

	<div class="ppb-badge"></div>

	<p class="ppb-actions">
		<a href="<?php
		echo "$ajax_with_nonce&action=pootlepb_live_page&tour=1";
		?>" class="button pootle">Tour</a>
		<a href="<?php
		echo esc_url( admin_url( 'admin.php?page=page_builder_settings' ) ); ?>" class="button pootle">Settings</a>
		<a href="http://docs.pootlepress.com/" class="button pootle">Docs</a>
		<b>Version <?php echo esc_attr( POOTLEPB_VERSION ); ?></b>
	</p>
	<hr>
	<?php echo '<div class="ppb-video-container">' . wp_oembed_get( 'https://vimeo.com/215980602' ) . '</div>'; ?>
	<div class="ppb-side-50">
		<h3>Free features</h3>

		<ul>
			<li><a class="thickbox" href="https://player.vimeo.com/video/185787894?k=v&TB_iframe=true&height=540&width=960">Basic usage</a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/180052795?k=v&TB_iframe=true&height=540&width=960">Adding drag and drop modules into page</a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/192113398?k=v&TB_iframe=true&height=540&width=960">Adding a hero photo to the top of your page</a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/185792519?k=v&TB_iframe=true&height=540&width=960">Snap to grid for pixel perfect layout</a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/197875188?k=v&TB_iframe=true&height=540&width=960">Row Animations</a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/185800528?k=v&TB_iframe=true&height=540&width=960">Background image effects (parallax)</a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/185803787?k=v&TB_iframe=true&height=540&width=960">Gradient backgrounds</a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/185800527?k=v&TB_iframe=true&height=540&width=960">Accordians</a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/185801458?k=v&TB_iframe=true&height=540&width=960">Adding icons</a></li>
		</ul>
	</div>
	<div class="ppb-side-50">
		<h3>Pro features</h3>
		<ul>
			<li><a class="thickbox" href="https://player.vimeo.com/video/197584823?k=v&TB_iframe=true&height=540&width=960">WooCommerce Builder (for individual products)</a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/197742676?k=v&TB_iframe=true&height=540&width=960">WooCommerce Builder (for your shop pages)</a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/163400847?k=v&TB_iframe=true&height=540&width=960">Blog and Posts customizer</a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/191164397?k=v&TB_iframe=true&height=540&width=960">Create landing pages, sale pages and squeeze pages</a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/197873693?k=v&TB_iframe=true&height=540&width=960">One Page websites</a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/190233819?k=v&TB_iframe=true&height=540&width=960">Pootle slider</a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/166312827?k=v&TB_iframe=true&height=540&width=960">Pro slideshows and galleries</a></li>

			<li><a class="thickbox" href="https://player.vimeo.com/video/166309495?k=v&TB_iframe=true&height=540&width=960">Starter templates</a></li>
		</ul>
		&nbsp;
	</div>
</div>