<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 24/6/15
 * Time: 11:16 PM
 * @since 0.1.0
 */

/**
 * Activated ppb add ons
 * @var array
 */
$pootlepb_installed_add_ons = apply_filters( 'pootlepb_installed_add_ons', array() );

?>
<div class="wrap">
	<h2>Pootle Page Builder Add-ons</h2>
	<?php settings_errors(); ?>
<form action='options.php' method="POST">
	<?php
	foreach ( $pootlepb_installed_add_ons as $id => $file ) {
		$addon = get_plugin_data( $file );

	?>
		<div class="ppb-addon-card-wrap <?php echo $id; ?>-wrap">
			<div class="ppb-addon-card <?php echo $id; ?> <?php if ( ! empty( $s[ $id ] ) ) {
				echo 'active';
			} ?>">

				<div class="ppb-addon-img">
					<a href="<?php echo $addon['PluginURI'] ?>" class="thickbox" style="background-image: url(<?php echo POOTLEPB_URL . 'css/images/plug.svg' ?>);"></a>
				</div>

				<div class="ppb-addon-details">
					<div class="ppb-addon-name">
						<h3><?php echo $addon['Title'] ?></h3>
					</div>
					<div class="desc ppb-addon-description">
						<p class="ppb-addon-description"><?php echo $addon['Description'] ?></p>
					</div>
				</div>
				<div class="ppb-addon-footer">
						<div class="ppb-addon-installed">
							You have this installed
						</div>
				</div>

			</div>
		</div>
	<?php
	}
	?>
	<div class="clear"></div>
	<?php
	settings_fields( 'pootlepage-add-ons' );
	submit_button();
	?>
</form>
</div>