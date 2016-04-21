<?php
/**
 * @developer wpdevelopment.me <shramee@wpdvelopment.me>
 */
global $pootlepb_row_settings_tabs;

$panel_tabs = array(
	'-' => array(
		'editor'           => array(
			'label'    => 'Editor',
			'priority' => 1,
		),
		'style'            => array(
			'label'    => 'Style',
			'class'    => 'pootle-style-fields',
			'priority' => 2,
		),
		'editor-separator' => array(
			'priority' => 3,
		),
	)
);

foreach ( $this->addons as $k => $tab ) {
	$panel_tabs[ $tab['label'] ][ $k ] = $tab;
}

ksort( $panel_tabs );
?>
	<div class="pootlepb-dialog" id="pootlepb-content-editor-panel">
		<ul>
			<?php
			//Output the tabs
			foreach ( $panel_tabs as $tabs ) {
				foreach ( $tabs as $k => $tab ) {
					if ( empty( $tab['label'] ) ) {
						echo '<li class="ppb-separator"></li>';
						continue;
					}
					echo "
					<li>
						<a class='ppb-tabs-anchors ppb-content-block-tab-$k' href='#pootle-$k-tab'>
							$tab[label]
						</a>
					</li>
					";
				}
			}
			?>
		</ul>
		<?php
		//Output the tabs
		foreach ( $panel_tabs as $tabs ) {
			foreach ( $tabs as $k => $tab ) {
				if ( empty( $tab['label'] ) ) {
					continue;
				}
				if ( empty( $tab['class'] ) ) {
					$tab['class'] = '';
				}

				echo "\n\n<div id='pootle-$k-tab' class='tab-contents pootle-style-fields $tab[class]'>";
				echo "<h3>$tab[label]</h3>";
				do_action( "pootlepb_content_block_{$k}_tab" );
				pootlepb_block_dialog_fields_output( $k );
				do_action( "pootlepb_content_block_{$k}_tab_after_fields" );
				echo '</div>';
			}
		}
		?>

	</div>
<?php /*


	<div class="pootlepb-dialog" id="pootlepb-content-editor-panel">
		<ul>
			<li><a href="#pootlepb-editor-content-tab">Editor</a></li>
			<li><a href="#pootlepb-style-content-tab">Style</a></li>
		</ul>
		<div id="pootlepb-editor-content-tab" class="pootlepb-content-tab">
			<?php
			do_action( "pootlepb_content_block_editor_tab" );
			pootlepb_block_dialog_fields_output( 'editor' );
			do_action( "pootlepb_content_block_editor_tab_after_fields" );
			?>
		</div>
		<div id="pootlepb-style-content-tab" class="pootlepb-content-tab pootlepb-style-fields">
			<?php
			do_action( "pootlepb_content_block_style_tab" );
			pootlepb_block_dialog_fields_output( 'style' );
			do_action( "pootlepb_content_block_style_tab_after_fields" );
			?>
		</div>
	</div>

	*/

$row_panel_tabs = apply_filters( 'pootlepb_le_row_block_tabs', $panel_tabs );

$row_panel_tabs = array(
	'background' => array(
		'label'    => 'Background',
		'priority' => 1,
	),
	'layout'     => array(
		'label'    => 'Layout',
		'priority' => 2,
	),
	'advanced'   => array(
		'label'    => 'Advanced',
		'priority' => 10,
	),
);

?>
	<div class="pootlepb-dialog" id="pootlepb-row-editor-panel">
		<ul>
			<?php
			//Output the tabs
			foreach ( $row_panel_tabs as $k => $tab ) {
				if ( empty( $tab['label'] ) ) {
					echo '<li class="ppb-separator"></li>';
					continue;
				}
				echo "<li><a href='#pootlepb-$k-row-tab'>$tab[label]</a></li>";
			}
			?>
		</ul>
		<?php
		//Output the tab panels
		foreach ( $row_panel_tabs as $k => $tab ) {
			if ( empty( $tab['label'] ) ) {
				continue;
			}
			?>
			<div id="pootlepb-<?php echo $k; ?>-row-tab" class="pootlepb-row-tab">

				<?php
				do_action( "pootlepb_row_settings_{$k}_tab" );
				pootlepb_row_dialog_fields_output( $k );
				do_action( "pootlepb_row_settings_{$k}_tab_after_fields" );
				?>

			</div>
			<?php
		} ?>
	</div>
	<div class="pootlepb-dialog" id="pootlepb-add-row">
		<label>
			<p>How many columns do you want your row to have?</p>
			<input max="10" min="1" id="ppb-row-add-cols" type="number" value="2">
		</label>
	</div>
	<div class="pootlepb-dialog" id="pootlepb-set-title">
		<label>
			<p>Please set the title for the page,</p>
			<input id="ppble-live-page-title" type="text" placeholder="<?php echo $this->edit_title; ?>">
		</label>
	</div>
<?php
