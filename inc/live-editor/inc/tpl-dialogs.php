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
foreach ( $disabled_modules as $id ) {
	unset( $ppb_modules[ $id ] );
}

// Prioritizing active modules
foreach ( $enabled_modules as $i => $id ) {
	if ( ! empty( $ppb_modules[ $id ] ) ) {
		$ppb_modules[ $id ]['priority'] = $i * 2 + 1;
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

				if ( ! empty( $module['callback'] ) ) {
					$attr .= " data-callback='$module[callback]'";
				}

				if ( ! empty( $module['tab'] ) ) {
					$attr .= " data-tab='$module[tab]'";
				}

				if ( ! empty( $module['style_data'] ) ) {
					$attr .= " data-style_data='$module[style_data]'";
				}

				if ( ! empty( $module['ActiveClass'] ) && class_exists( $module['ActiveClass'] ) ) {
					$classes .= ' ppb-module';
					echo "<div id='ppb-mod-$id' class='$classes' $attr><i class='icon $module[icon_class]'>$module[icon_html]</i><div class='label'>$module[label]</div></div>";
				} else {
					$classes .= ' ppb-module-disabled';
					echo
						'<a target="_blank" href="' . admin_url( 'admin.php?page=page_builder_modules' ) . '">' .
						"<div id='ppb-mod-$id' class='$classes' $attr><i class='icon $module[icon_class]'>$module[icon_html]</i><div class='label'>$module[label]</div></div>" .
						'</a>';
				}
			}
			?>
		</div>
	</div>
	<?php
}
?>

<div id="ppb-loading-overlay">
	<div id="ppb-loader"></div>
	<style>
		#ppb-loading-overlay,
		#ppb-loader {
			position:fixed;
			top:-9999px;
			right:-9999px;
			bottom:-9999px;
			left:-9999px;
			z-index: 9999;
		}
		#ppb-loading-overlay {
			background: rgba(0, 0, 0, 0.5);
			display: none;
		}
		#ppb-loader {
			margin: auto;
			width: 160px;
			height: 160px;
			border: 16px solid #fdb;
			border-radius: 50%;
			border-top-color: #ef4832;
			-webkit-animation: ppb-spin 1.6s linear infinite;
			animation: ppb-spin 1.6s linear infinite;
		}
	</style>
</div>