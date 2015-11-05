<?php
/**
 * Add-ons key management page template
 * Add-on activation management page in pootle page builder settings
 * @author pootlepress
 * @since 0.3.0
 */
$tabs = apply_filters( 'pootle_pb_addon_key_tabs', array() );
$url_base = '?page=page_builder_settings&tab=addons&addon=';

if ( empty( $tabs ) ) {
	?>
	<h3 style="display:block;">Hi,</h3>
	<h4>Seems like you don't have any add-ons yet.</h4>
	<h3 style="display:block;">Head over to <a href="http://www.pootlepress.com/product-category/pootle-page-builder-add-ons/">pootle page builder add-ons</a> to grab some awesome add-ons.</h3>
	<?php
	return;
}
?>
	<ul class="subsubsub">
		<?php
		$tab_now = filter_input( INPUT_GET, 'addon' );
		//Output the tabs
		foreach ( $tabs as $k => $tab ) {
			//Set first element active if no tab is selected
			if ( empty( $tab_now ) ) { $tab_now = $k; }
			?>
			<li>
				<a href="<?php echo $url_base . $k ?>"
					class="<?php echo $tab_now == $k ? 'current' : ''; ?>">
					<?php echo $tab ?>
				</a>
			</li>
		<?php
		}
		?>
	</ul>
	<div class="clear"></div>
	<?php
		do_action( 'pootle_pb_addon_key_' . $tab_now . '_tab', $url_base . $tab_now );
do_action( 'pootlepb_addon_page' );