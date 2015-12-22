<?php
/**
 * Add-ons page template
 * Shows add-ons cards from http://pootlepress.com/ feed
 * @author pootlepress
 * @since 0.1.0
 */

/**
 * @var array Activated ppb add ons
 */
$pootlepb_installed_add_ons = apply_filters( 'pootlepb_installed_add_ons', array() );

$ppb_addon_common_words = '/\bpootle\b|\bpage\b|\bbuilder\b|\bfor\b|\badd\b|\bextension\b|\bon\b|\bthe\b/i';

$installed_plugins = array();
foreach ( $pootlepb_installed_add_ons as $id => $file ) {
	$addon = get_plugin_data( $file );
	$installed_plugins[] = strtolower(
		trim(
			preg_replace(
				$ppb_addon_common_words,
				'',
				strip_tags( $addon['Title'] )
			)
		)
	);
}

// Check transient
$xml = get_transient( 'pootlepb-addons-data' );

// No transient
if( empty( $xml ) || filter_input( INPUT_GET, 'reload-data' ) ) {
	$url = 'http://pootlepress.github.io/pootle-page-builder/add-ons.xml';
	$xml = file_get_contents( $url );
	// Save the API response so we don't have to call again until tomorrow.
	set_transient( 'pootlepb-addons-data', $xml, DAY_IN_SECONDS );
}
$sxml = simplexml_load_string( $xml );

?>
<div class="wrap">
	<h2>Pootle Page Builder Add-ons</h2>
	<?php settings_errors();

	foreach ( $sxml as $plugins ) {
		foreach ( $plugins as $plgn ) {
			if ( ! empty( $plgn->title ) ) {

				$title_trimmed = strtolower(
					trim(
						preg_replace(
							$ppb_addon_common_words,
							'',
							strip_tags( $plgn->title )
						)
					)
				);

				$desc = new SimpleXMLElement( '<p>' . $plgn->description . '</p>' );

				?>
				<div class="ppb-addon-card-wrap">
					<div class="ppb-addon-card">

						<div class="ppb-addon-img">
							<a href="<?php echo $plgn->link ?>" class="thickbox"
							   style="background-image: url(<?php echo $desc->img['src']; ?>);"></a>
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
							if ( in_array( $title_trimmed, $installed_plugins ) ) {
								?>
								<div class="ppb-addon-installed">
									<span class="dashicons dashicons-yes"></span><span>You have this installed</span>
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