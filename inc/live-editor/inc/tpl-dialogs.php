<?php
/**
 * @developer wpdevelopment.me <shramee@wpdvelopment.me>
 */
global $pootlepb_row_settings_tabs;

$panel_tabs = array(
	'-' => array(
		'style'            => array(
			'label'    => 'Style',
			'class'    => 'pootle-style-fields',
			'priority' => 1,
		),
		'editor'           => array(
			'label'    => 'Editor',
			'priority' => 2,
		),
		'advanced'            => array(
			'label'    => 'Advanced',
			'class'    => 'pootle-style-fields',
			'priority' => 2,
		),
		'editor-separator' => array(
			'priority' => 3,
		),
	)
);

add_filter( 'mce_buttons', function ( $buttons ) {
	return array('formatselect','bold','italic','bullist','numlist','link','fullscreen','wp_adv',);
} );

add_filter( 'mce_buttons_2', function ( $buttons ) {
	return 	array('strikethrough','hr','forecolor','pastetext','removeformat','charmap','outdent','indent','undo','redo', );
} );

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
					echo
						"<li class='content-block-tab content-block-tab-$k'>" .
							"<a class='ppb-tabs-anchors' href='#pootle-$k-tab'>$tab[label]</a>" .
						'</li>';
				}
			}
			?>
		</ul>
		<div class="content-wrap">
			<a href="javascript:void(0)" class="back">Back</a>
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
	</div>

	<div id="pootlepb-confirm-delete" class="pootlepb-dialog">
		Are you sure you want to delete this <span id="pootlepb-deleting-item">row</span>?
		<br>
		This action cannot be undone.
	</div>

<?php

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

$row_panel_tabs = apply_filters( 'pootlepb_le_row_block_tabs', $row_panel_tabs );
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
		<div class="content-wrap">
			<a href="javascript:void(0)" class="back">Back</a>
			<?php
			//Output the tab panels
			foreach ( $row_panel_tabs as $k => $tab ) {
				if ( empty( $tab['label'] ) ) {
					continue;
				}
				?>
				<div id="pootlepb-<?php echo $k; ?>-row-tab" class="pootlepb-row-tab">

					<?php
					echo "<h3>$tab[label]</h3>";
					do_action( "pootlepb_row_settings_{$k}_tab" );
					pootlepb_row_dialog_fields_output( $k );
					do_action( "pootlepb_row_settings_{$k}_tab_after_fields" );
					?>

				</div>
				<?php
			} ?>
		</div>
	</div>
	<div class="pootlepb-dialog" id="pootlepb-add-row">
		<label>
			<p>How many columns do you want your row to have?</p>
			<input max="10" min="1" id="ppb-row-add-cols" type="number" value="1">
		</label>
	</div>
	<div class="pootlepb-dialog" id="pootlepb-set-title" data-title="Set title of the <?php echo get_post_type() ?>">
		<label>
			<p>Please set the title for the <?php echo get_post_type() ?></p>
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
	<div class="pootlepb-dialog" id="pootlepb-post-settings" data-title="Set title of the <?php get_post_type() ?>">
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

$ppb_modules = apply_filters( 'pootlepb_modules', array() );

if ( 'post' == get_post_type() ) {
	unset( $ppb_modules['blog-posts'] );
}

$default_module_args = array(
	'label'      => 'Unlabeled Module',
	'icon_class' => 'dashicons dashicons-star-filled',
	'icon_html'  => '',
);

$enabled_modules = get_option( 'ppb_enabled_addons', array(
	'hero-section',
	'image',
	'pootle-slider',
	'photo-gallery',
	'unsplash',
	'pbtn',
	'blog-posts',
	'wc-products',
	'ninja_forms-form',
	'metaslider-slider',
) );
$disabled_modules = get_option( 'ppb_disabled_addons', array() );

$enabled_modules = apply_filters( 'pootlepb_enabled_addons', $enabled_modules );

$disabled_modules = apply_filters( 'pootlepb_disabled_addons', $disabled_modules );

// Removing disabled modules
if ( $disabled_modules ) {
	foreach ( $disabled_modules as $id ) {
		unset( $ppb_modules[ $id ] );
	}
}

// Prioritizing active modules
if ( $disabled_modules ) {
	foreach ( $enabled_modules as $i => $id ) {
		if ( ! empty( $ppb_modules[ $id ] ) ) {
			$ppb_modules[ $id ]['priority'] = $i * 2 + 1;
		}
	}
}

if ( $enabled_modules ) {
	$side = pootlepb_settings( 'modules-position' );
	?>
	<div id="pootlepb-modules-wrap" class="position-<?php echo $side ?>">
		<div class="dashicons dashicons-screenoptions" onclick="jQuery(this).parent().toggleClass('toggle')"></div>
		<div id="pootlepb-modules">
			<?php

			pootlepb_prioritize_array( $ppb_modules );

			foreach ( $ppb_modules as $module ) {
				$id     = $module['id'];
				$module = wp_parse_args( $module, array(
					'label'      => 'Unlabeled Module',
					'icon_class' => 'dashicons dashicons-star-filled',
					'icon_html'  => '',
				) );

				$classes = "mod-$id";

				$attr = "";

				if ( empty( $module['only_new_row'] ) ) {
					$classes .= ' ppb-module-existing-row';
				}

				if ( empty( $module['only_existing_row'] ) ) {
					$classes .= ' ppb-module-new-row';
				}

				if ( ! empty( $module['callback'] ) ) {
					$attr .= " data-callback='$module[callback]'";
				}

				if ( ! empty( $module['row_callback'] ) ) {
					$attr .= " data-row-callback='$module[row_callback]'";
				}

				if ( ! empty( $module['tab'] ) ) {
					$attr .= " data-tab='$module[tab]'";
				}

				if ( ! empty( $module['style_data'] ) ) {
					$attr .= " data-style_data='$module[style_data]'";
				}

				$tooltip = '';
				if ( ! empty( $module['tooltip'] ) ) {
					$tooltip = "<span  class='dashicons dashicons-editor-help tooltip-module' data-tooltip=\"$module[tooltip]\"></span>";
				}

				if ( empty( $module['active_class'] ) || class_exists( $module['active_class'] ) ) {
					$classes .= ' ppb-module';
					echo
						"<div id='ppb-mod-$id' class='$classes' $attr>" .
							"<i class='icon $module[icon_class]'>$module[icon_html]</i>" .
							"<div class='label'>$module[label]</div>" .
							$tooltip .
						'</div>';
				} else {

					$classes .= ' ppb-module-disabled';
					echo
						'<a target="_blank" href="' . admin_url( 'admin.php?page=page_builder_modules' ) . '">' .
							"<div id='ppb-mod-$id' class='$classes' $attr>" .
								"<i class='icon $module[icon_class]'>$module[icon_html]</i>" .
								"<div class='label'>$module[label]</div>"	.
								$tooltip .
							'</div>' .
						'</a>';
				}
			}
			?>
		</div>
	</div>
	<?php
}
?>
<div id="ppb-loading">
	<div id="ppb-loader"></div>
</div>

<div id="ppb-iconpicker" style="display: none;">
	<label>
		<span>Choose icon</span>
		<input id="ppb-icon-choose" type="hidden">
	</label>
	<label>
		<span>Icon size</span>
		<input id="ppb-icon-size" value="160" type="number" min="25" max="295" step="5"> px
	</label>
	<label>
		<span>Icon color</span>
		<input id="ppb-icon-color" value="#999" type="text">
	</label>
	<label>
		<span>Icon link</span>
		<input id="ppb-icon-link" type="url">
	</label>
	<label>
		<span>Display inline</span>
		<input id="ppb-icon-inline" type="checkbox">
	</label>
	<label>
		<span>Preview</span>
		<span id="ppb-icon-preview"></span>
	</label>
</div>
<div id="ppb-tooltip" style="display:none"></div>

<div id="ppb-notify" class="ppb-notify" style="display:none;"></div>

<?php
do_action( 'pootlepb_le_dialogs' );