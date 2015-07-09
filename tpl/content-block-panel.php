<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 26/6/15
 * Time: 11:56 PM
 * @since 0.1.0
 */
global $pootlepb_content_block_tabs;

/**
 * Add your own tabs to Content block editor panel
 *	'tabName' => array(
 *		'label' => 'Tab Name',
 *		'priority' => 10,
 *	),
 */
$pootlepb_content_block_tabs = apply_filters( 'pootlepb_content_block_tabs', $pootlepb_content_block_tabs );

$panel_tabs = array();

foreach ( $pootlepb_content_block_tabs as $k => $tab ) {
	if ( empty( $tab['priority'] ) ) {
		$tab['priority'] = 10;
	}
	$panel_tabs[ $tab['priority'] ][ $k ] = $tab;
}

ksort( $panel_tabs );
?>
	<div class="ppb-cool-panel-wrap">
		<ul class="ppb-acp-sidebar">

			<?php
			//Output the tabs
			foreach ( $panel_tabs as $tabs ) {
				foreach ( $tabs as $k => $tab ) {
					if ( empty( $tab['label'] ) ) {
						echo '<li class="ppb-separator"></li>';
						continue;
					}
				?>
					<li>
						<a class="ppb-tabs-anchors ppb-content-block-tab-<?php echo $k; ?>"
							href="#pootle-<?php echo $k; ?>-tab">
							<?php echo $tab['label']; ?>
						</a>
					</li>
				<?php
				}
			}
			?>
		</ul>

		<?php ?>
		<?php
		//Output the tabs
		foreach ( $panel_tabs as $tabs ) {
			foreach ( $tabs as $k => $tab ) {
				if ( empty( $tab['label'] ) ) { continue; }
				if ( empty( $tab['class'] ) ) { $tab['class'] = ''; }
				?>
				<div id="pootle-<?php echo $k; ?>-tab" class="tab-contents pootle-style-fields <?php echo $tab['class']; ?>">

					<?php
					do_action( 'pootlepb_content_block_' . $k . '_tab', $request );
					pootlepb_block_dialog_fields_output( $k );
					?>

				</div>
			<?php
			}
		}
		?>

	</div>
