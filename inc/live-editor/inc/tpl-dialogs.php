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

	<div id="pootlepb-confirm-delete" class="pootlepb-dialog">
		Are you sure you want to delete this <span id="pootlepb-deleting-item">row</span>?
		<br>
		This action cannot be undone.
	</div>

<?php
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
	<div class="pootlepb-dialog" id="pootlepb-set-title" data-title="Set title of the page">
		<label>
			<p>Please set the title for the page,</p>
			<input id="ppble-live-page-title" type="text" placeholder="Untitled" value="<?php get_the_title() !== 'Untitled' ? the_title() : null ?>">
		</label>
	</div>

<?php
if ( get_post_type() == 'post' ) {
	$cats_data = get_the_category();
	$cats      = array();
	if ( $cats_data ) {
		foreach ( $cats_data as $term ) {
			$cats[] = $term->term_id;
		}
	}
	$tags_data = get_the_tags();
	$tags      = array();
	if ( $tags_data ) {
		foreach ( $tags_data as $term ) {
			$tags[] = $term->name;
		}
	}

	?>
	<div class="pootlepb-dialog" id="pootlepb-post-settings" data-title="Set title of the post">
		<label>
			<h3>Featured image</h3>
			<span>
			<div id="ppble-feat-img-wrapper">
				<div style="background-image: url('<?php the_post_thumbnail_url() ?>');" class="ppble-img-prevu"
				     id="ppble-feat-img-prevu">Choose Image
				</div>
			</div>
			</span>
		</label>
		<label>
			<h3>Categories</h3>
			<span>
			<select multiple="multiple" class="post-category">
				<?php
				$terms = get_terms( array(
					'taxonomy'   => 'category',
					'hide_empty' => false,
				) );

				foreach ( $terms as $cat ) {
					echo "<option value='$cat->term_id'" . ( in_array( $cat->term_id, $cats ) ? ' selected="selected"' : '' ) . ">$cat->name</option>";
				}
				?>
			</select>
			</span>
		</label>
		<label>
			<h3>Tags</h3>
			<span>
			<select multiple="multiple" class="post-tags">
				<?php
				$terms = get_terms( array(
					'taxonomy'   => 'post_tag',
					'hide_empty' => false,
				) );

				foreach ( $terms as $tag ) {
					echo "<option value='$tag->name'" . ( in_array( $tag->name, $tags ) ? ' selected="selected"' : '' ) . ">$tag->name</option>";
				}
				?>
			</select>
			</span>
		</label>

	</div>
	<?php
}
if ( isset( $_REQUEST['tour'] ) ) {
	include "tpl-tour.php";
}
?>
<div id="pootlepb-modules">
	<div class="dashicons dashicons-screenoptions" onclick="jQuery(this).parent().toggleClass('toggle')"></div>
	<?php
	$ppb_modules = array(
		'wc-popular-products' => array(
			'label' => 'WooCommerce - Popular Products',
			'icon_class' => 'dashicons dashicons-cart',
			'icon_html' => '',
		),
		'photo-slider' => array(
			'label' => 'Photography - Slider',
			'icon_class' => 'dashicons dashicons-images-alt2',
			'icon_html' => '',
		),
		'photo-gallery' => array(
			'label' => 'Photography - Gallery',
			'icon_class' => 'dashicons dashicons-grid-view',
			'icon_html' => '',
		),
	);

	$ppb_modules = apply_filters( 'pootlepb_modules', $ppb_modules );

	foreach ( $ppb_modules as $id => $module ) {
		$module = wp_parse_args( $module, array(
			'label' => 'Unlabeled Module',
			'icon_class' => 'dashicons dashicons-star-filled',
			'icon_html' => '',
		) );
		echo "<div id='ppb-mod-$id' class='module mod-$id'><i class='icon $module[icon_class]'>$module[icon_html]</i><div class='label'>$module[label]</div></div>";
	}
	?>
</div>
