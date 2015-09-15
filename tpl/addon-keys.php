<?php
/**
 * Add-ons key management page template
 * Add-on activation management page in pootle page builder settings
 * @author pootlepress
 * @since 0.3.0
 */
$tabs = apply_filters( 'pootle_pb_addon_key_tabs', array() );
$url_base = '?page=page_builder_settings&tab=addons&addon=';
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
		do_action( 'pootle_pb_addon_key_' . $tab_now . '_tab', $url_base . $k );
do_action( 'pootlepb_addon_page' );