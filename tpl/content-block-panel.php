<?php
/**
 * Content block panel template
 * Used by content editor panels in pootle page builder post
 * @author pootlepress
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

$add_on_tabs = apply_filters( 'pootlepb_content_block_tabs', array() );

$panel_tabs = array(
	'-' => $pootlepb_content_block_tabs
);

foreach ( $add_on_tabs as $k => $tab ) {
	$panel_tabs[ $tab['label'] ][ $k ] = $tab;
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
					do_action( "pootlepb_content_block_{$k}_tab" );
					pootlepb_block_dialog_fields_output( $k );
					do_action( "pootlepb_content_block_{$k}_tab_after_fields" );
					?>

				</div>
			<?php
			}
		}
		?>

	</div>
