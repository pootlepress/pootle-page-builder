<?php
/**
 * Add-ons page template
 * Shows add-ons cards from http://pootlepress.com/ feed
 * @author pootlepress
 * @since 0.1.0
 */

global $ppbpro_addons_data;

$active_addons = array();
if ( class_exists( 'Pootle_Page_Builder_Pro' ) ) {
	$active_addons = get_option( 'ppbpro_active_addons', array(
		'blog-customizer',
		'page-customizer',
		'photography',
	) );
}

?>
<div class="wrap">
	<h2>Pootle Page Builder Pro</h2>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'ppbpro_active_addons' );
		settings_errors();

		foreach ( $ppbpro_addons_data as $addon ) {
			$id = $addon['path'];
			$plugin = get_plugin_data( dirname( __FILE__ ) . "/ext/$addon[path]/init.php", false );
			$active = isset( $active_addons[ $id ] ) ? $active_addons[ $id ] : '';
			?>
			<div class="ppb-addon-card-wrap">
				<div class="ppb-addon-card <?php if ( $active ) {
					echo 'active';
				} ?>">
					<div class="ppb-addon-img">
						<a class="thickbox"
						   style="background-image: url(<?php echo $addon['img']; ?>);"></a>
					</div>

					<div class="ppb-addon-details">
						<div class="ppb-addon-name">
							<h3><?php echo $plugin['Name']; ?></h3>
						</div>
						<div class="desc ppb-addon-description">
							<p class="ppb-addon-description"><?php echo strip_tags( $plugin['Description'], '<a>' ); ?></p>
							<cite>By <a href="//pootlepress.com">pootlepress</a></cite>
						</div>
					</div>
					<div class="ppb-addon-footer">
						<div class="ppb-addon-controls ppb-addon-installed">
							<input type="hidden" name="ppbpro_active_addons[<?php echo $id; ?>]"
							       value="<?php echo $active; ?>">
							<a class="button pootle activate"><?php _e( 'Activate' ) ?></a>

							<p class="deactivate">
								<span class="dashicons dashicons-yes"></span>
								<?php
								if ( $active ) {
									_e( 'This addon is active!', 'ppbpro' );
								} else {
									_e( 'Hit "Save Changes" to activate!', 'ppbpro' );
								}
								?>
							</p>
							<a class="button deactivate"><?php _e( 'Deactivate' ) ?></a>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		?>
		<div class="clear"></div>
		<?php
		submit_button();
		?>
	</form>
</div>