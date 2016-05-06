<?php
/**
 * Add-ons page template
 * Shows add-ons cards from http://pootlepress.com/ feed
 * @author pootlepress
 * @since 0.1.0
 */

global $ppbpro_tpl;

$plg_url = Pootle_Page_Builder_Pro::$url . 'assets/tpl/thumbs';
$live_page_url = admin_url( 'admin-ajax.php?action=pootlepb_live_page' );
$live_page_url = wp_nonce_url( $live_page_url, 'ppb-new-live-post', 'ppbLiveEditor' );
?>
<style>
	.ppb-templates {
		text-align: center;
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
	.ppb-template img {
		display: block;
		width: 100%;
		height: auto;
		float: right;
		margin: 0 0 25px;
	}
</style>
<div class="wrap">
	<h2>Starter Templates</h2>
	<div class="ppb-templates">
		<?php
		foreach ( ppbpro_get_template() as $Tpl ) {
			$tpl = strtolower( $Tpl );
			echo "<a class='ppb-template' href='$live_page_url&tpl=$Tpl'><img src='$plg_url/$tpl.png'>$Tpl</a>";
		}
		?>
	</div>
</div>