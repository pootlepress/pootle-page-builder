<?php
/**
 * Add-ons page template
 * Shows add-ons cards from http://pootlepress.com/ feed
 * @author pootlepress
 * @since 0.1.0
 */

global $ppbpro_tpl;

$plg_url = Pootle_Page_Builder_Pro::$url . 'assets/tpl';
$live_page_url = admin_url( 'admin-ajax.php?action=pootlepb_live_page' );
$live_page_url = wp_nonce_url( $live_page_url, 'ppb-new-live-post', 'ppbLiveEditor' );
?>
<div class="wrap">
	<h2>Starter Templates</h2>
	<?php
	foreach ( ppbpro_get_template() as $tpl ) {
		echo "<a href='$live_page_url&tpl=$tpl'><img src='$plg_url/$tpl.jpg'></a>";
	}
	?>
</div>