<?php
/**
 * Add-ons page template
 * Shows add-ons cards from http://pootlepress.com/ feed
 * @author pootlepress
 * @since 0.1.0
 */

global $ppbpro_tpl;

$thmb_url = Pootle_Page_Builder_Pro::$url . '/assets/tpl/thumbs';
$thmb_pth = Pootle_Page_Builder_Pro::$path . '/assets/tpl/thumbs';
$live_page_url = admin_url( 'admin-ajax.php?action=pootlepb_live_page' );
$live_page_url = wp_nonce_url( $live_page_url, 'ppb-new-live-post', 'ppbLiveEditor' );
?>
<style>
	.ppb-templates {
		text-align: center;
	}

	.ppb-tpl-img {
		position: relative;
		width: 100%;
		background: center top/cover;
		padding-top: 100%;
		margin: 0 0 25px
	}

	.ppb-tpl-img .dashicons, .ppb-tpl-img:before {
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
	}

	.ppb-tpl-img:before {
		background: rgba(0,0,0,0.7);
		content: '';
		display:none;
	}

	.ppb-tpl-img .dashicons {
		margin: auto;
		display: none;
		height: 160px;
		width: 80%;
		padding: 0 10%;
		color: #fff;
	}

	.ppb-tpl-img .dashicons > div {
		font: 25px sans-serif;
	}

	.ppb-tpl-img .dashicons:before  {
		font-size: 70px;
	}

	.ppb-template:hover .ppb-tpl-img:before, .ppb-template:hover div {
		display: block;
	}

	.ppb-template {
		display: inline-block;
		width: 45%;
		max-width: 500px;
		min-width: 300px;
		margin: 2.5%;
		font-weight: 200;
		font-size: 25px;
		text-decoration: none;
		color: #555;
		text-align: center;
	}

	.ppb-template:hover {
		color:#333;
	}
</style>
<div class="wrap">
	<h2>Starter Templates</h2>
	<div class="ppb-templates">
		<?php
		foreach ( ppbpro_get_template() as $Tpl ) {
			$tpl = strtolower( $Tpl );
			$img_url = "$thmb_url/$tpl.png";
			if ( ! file_exists( "$thmb_pth/$tpl.png" ) ) {
				$img_url = "http://pootlepress.github.io/pootle-page-builder/tpl-thumbs/$tpl.png";
			}
			echo "<a class='ppb-template' href='$live_page_url&tpl=$Tpl'><div class='ppb-tpl-img' style='background-image:url(\"$img_url\")'><div class='dashicons dashicons-plus'><div>Add Page with this template</div></div></div>$Tpl</a>";
		}
		?>
	</div>
</div>