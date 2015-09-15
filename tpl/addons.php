<?php
/**
 * Add-ons page template
 * Shows add-ons cards from http://pootlepress.com/ feed
 * @author pootlepress
 * @since 0.1.0
 */

/**
 * Activated ppb add ons
 * @var array
 */
$pootlepb_installed_add_ons = apply_filters( 'pootlepb_installed_add_ons', array() );
$installed_plugins = array();
foreach ( $pootlepb_installed_add_ons as $id => $file ) {
	$addon = get_plugin_data( $file );
	$installed_plugins[] = strip_tags( $addon['Title'] );
}

$url = 'http://pootlepress.com/shop/feed/?product_cat=pootle-page-builder-add-ons';
$sxml = simplexml_load_file( $url, null, LIBXML_NOCDATA );
?>
<div class="wrap">
	<h2>Pootle Page Builder Add-ons</h2>
	<?php settings_errors();

	foreach ( $sxml as $plugins ) {
		foreach ( $plugins as $plgn ) {
			if ( ! empty( $plgn->title ) ) {
				$desc = new SimpleXMLElement( '<p>' . $plgn->description . '</p>' );
				?>
				<div class="ppb-addon-card-wrap <?php echo $id; ?>-wrap">
					<div class="ppb-addon-card <?php echo $id; ?> <?php if ( ! empty( $s[ $id ] ) ) {
						echo 'active';
					} ?>">

						<div class="ppb-addon-img">
							<a href="<?php echo $plgn->link ?>" class="thickbox" style="background-image: url(<?php echo $desc->img['src']; ?>);"></a>
						</div>

						<div class="ppb-addon-details">
							<div class="ppb-addon-name">
								<h3><?php echo $plgn->title ?></h3>
							</div>
							<div class="desc ppb-addon-description">
								<p class="ppb-addon-description"><?php echo strip_tags( $desc->asXML() ); ?></p>
							</div>
						</div>
						<div class="ppb-addon-footer">
							<?php
							if ( in_array( $plgn->title, $installed_plugins ) ){
								?>
								<div class="ppb-addon-installed">
									You have this installed
								</div>
							<?php
							} else {
								?>
								<div class="ppb-addon-installed">
									<a class="button pootle" href="<?php echo $plgn->link; ?>"> Get it now </a>
								</div>
							<?php
							}
							?>
						</div>

					</div>
				</div>
			<?php
			};
		}
	}
	?>
	<div class="clear"></div>
</div>