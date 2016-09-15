<?php
/**
 * Contains Pootle_Page_Builder_Render_Grid class
 * @author pootlepress
 * @since 0.1.0
 */

/**
 * Class Pootle_Page_Builder_Render_Grid
 * Renders all the rows on the page
 */
class Pootle_Page_Builder_Render_Grid {
	/**
	 * @var Pootle_Page_Builder_Render_Grid Instance
	 * @access protected
	 * @since 0.1.0
	 */
	protected static $instance;

	/**
	 * Outputs the pootle page builder grids
	 *
	 * @param array $grids
	 * @param array $panels_data
	 * @param int $post_id
	 */
	protected function output_rows( $grids, $panels_data, $post_id ) {
		do_action( 'pootlepb_before_pb' );
		foreach ( $grids as $gi => $cells ) {

			$rowID = 'pg-' . $post_id . '-' . $gi;

			echo '<div ' . $this->get_row_attributes( $gi, $rowID, $panels_data ) . '>';

			/**
			 * Triggered before rendering the row
			 * Allows other themes and plugins to add html before the row
			 * @param array $data	Grid data
			 * @param int	$gi		Grid Index
			 */
			do_action( 'pootlepb_before_row', $panels_data['grids'][ $gi ], $gi );

			//Row style wrapper
			$this->row_style_wrapper( $rowID, $gi, $cells, $panels_data );
			echo "<div class='panel-grid-cell-container'>";

			$this->output_cells( $cells, $gi, $post_id, $panels_data );

			echo "</div><!--.panel-grid-cell-container-->";
			echo '</div><!--.panel-row-style-->';

			/**
			 * Triggered after rendering the row
			 * Allows other themes and plugins to add html after the row
			 * @param array $data	Grid data
			 * @param int	$gi		Grid Index
			 */
			do_action( 'pootlepb_after_row', $panels_data['grids'][ $gi ], $gi );

			echo '</div><!--.panel-grid-->';
		}

		do_action( 'pootlepb_after_pb' );
	}

	/**
	 * Returns the row attributes in string
	 * @param int $gi
	 * @param string $rowID
	 * @param array $panels_data
	 * @return string
	 */
	private function get_row_attributes( $gi, $rowID, $panels_data ) {

		/**
		 * Filters row classes
		 * @param array $classes
		 * @param array $row_data
		 */
		$grid_classes    = apply_filters( 'pootlepb_row_classes', array( 'panel-grid', 'ppb-row' ), $panels_data['grids'][ $gi ] );
		/**
		 * Filters row attributes
		 * @param array $attributes
		 * @param array $row_data
		 */
		$grid_attributes = apply_filters( 'pootlepb_row_attributes', array(
			'class' => implode( ' ', $grid_classes ),
			'id'    => $rowID
		), $panels_data['grids'][ $gi ] );

		return pootlepb_stringify_attributes( $grid_attributes );
	}

	/**
	 * Outputs the rows style wrapper and calls pootlepb_before_cells hook
	 *
	 * @param string $rowID
	 * @param int $gi
	 * @param array $cells
	 * @param array $panels_data
	 */
	private function row_style_wrapper( $rowID, $gi, $cells, $panels_data ) {

		$styleArray = ! empty( $panels_data['grids'][ $gi ]['style'] ) ? $panels_data['grids'][ $gi ]['style'] : array();

		echo '<div ' . $this->get_row_style_attributes( $gi, $styleArray, $cells, $panels_data ) . '>';

		/**
		 * Fires in row before the cells are rendered
		 * @hooked Pootle_Page_Builder_Render_Layout::row_bg_video
		 * @hooked Pootle_Page_Builder_Render_Layout::row_embed_css
		 */
		do_action( 'pootlepb_before_cells', $styleArray, $rowID );

	}

	/**
	 * Returns the row style attributes in string
	 * @param int $gi
	 * @param array $styleArray
	 * @param array $cells
	 * @param array $panels_data
	 * @return string
	 */
	private function get_row_style_attributes( $gi, $styleArray, $cells, $panels_data ) {

		$style_attributes          = array();

		$style_attributes['class'] = array(
			'ppb-row',
			'panel-row-style',
			'panel-row-style-' . $panels_data['grids'][ $gi ]['style']['class'],
			$panels_data['grids'][ $gi ]['style']['class'],
		);

		/**
		 * Filters row style container attributes
		 * @param array $attributes
		 * @param array $row_data
		 */
		$style_attributes = apply_filters( 'pootlepb_row_style_attributes', $style_attributes, $styleArray, $cells );

		return pootlepb_stringify_attributes( $style_attributes );
	}

	/**
	 * Outputs the cells
	 * @param array $cells
	 * @param int $gi
	 * @param int $post_id
	 * @param array $panels_data
	 */
	private function output_cells( $cells, $gi, $post_id, $panels_data ) {
		foreach ( $cells as $ci => $content_blocks ) {
			echo '<div '. $this->get_cell_attributes( $ci, $gi, $post_id, $panels_data ) . '>';

			/**
			 * Executed before content blocks are rendered
			 * @param array $cell_data
			 * * @param int $ci - Cell Index
			 * * @param int $gi - Grid Index
			 * * @param int $blocks_num - Total number of Blocks in cell
			 * * @param int $post_id - The current post ID
			 * @since 0.1.0
			 */
			do_action( 'pootlepb_before_content_blocks', array(
				'ci' =>$ci,
				'gi' =>$gi,
				'count' =>count( $content_blocks ),
				'post_id' =>$post_id,
				) );

			foreach ( $content_blocks as $pi => $content_block) {
				/**
				 * Render the content block via this hook
				 * @param array $content_block- Info for this block - backwards compatible with content blocks
				 * @param int $gi - Grid Index
				 * @param int $ci - Cell Index
				 * @param int $pi - Panel/Content Block Index
				 * @param int $blocks_num - Total number of Blocks in cell
				 * @param int $post_id - The current post ID
				 * @since 0.1.0
				 */
				do_action( 'pootlepb_render_content_block', $content_block, $gi, $ci, $pi, count( $content_blocks ), $post_id );
			}

			/**
			 * Executed after content blocks are rendered
			 * @param array $cell_data
			 * * @param int $ci - Cell Index
			 * * @param int $gi - Grid Index
			 * * @param int $blocks_num - Total number of Blocks in cell
			 * * @param int $post_id - The current post ID
			 * @since 0.1.0
			 */
			do_action( 'pootlepb_after_content_blocks', array(
				'ci' =>$ci,
				'gi' =>$gi,
				'count' =>count( $content_blocks ),
				'post_id' =>$post_id,
			) );

			echo '</div>';
		}
	}

	/**
	 * Returns the cell attributes in string
	 * @param int $ci
	 * @param int $gi
	 * @param int $post_id
	 * @param array $panels_data
	 * @return string
	 */
	private function get_cell_attributes( $ci, $gi, $post_id, $panels_data ) {
		$cellId = 'pgc-' . $post_id . '-' . $gi . '-' . $ci;

		$col_class = '';
		if ( ! empty( $panels_data['grids'][ $gi ]['style']['col_class'] ) ) {
			$col_class = $panels_data['grids'][ $gi ]['style']['col_class'];
		}

		$cell_classes = apply_filters( 'pootlepb_row_cell_classes', array( "ppb-col panel-grid-cell $col_class" ), $panels_data );

		$cell_attributes = array( 'class' => implode( ' ', $cell_classes ), 'id'    => $cellId,	);
		$cell_attributes = apply_filters( 'pootlepb_row_cell_attributes', $cell_attributes, $ci, $gi, $panels_data['grids'][ $gi ]['style'], $panels_data );

		return pootlepb_stringify_attributes( $cell_attributes );
	}

	/**
	 * Output row bg video
	 *
	 * @param array $style
	 * @param array $row_id
	 */
	public function row_embed_css( $style, $row_id ) {

		$row_id = '#' . $row_id;

		/** Fires in row to embed row styles */
		$embed_styles = trim( apply_filters( 'pootlepb_row_embed_style', '', $style, $row_id ) );

		if ( ! empty( $embed_styles ) ) {
			echo "<style>{$embed_styles}</style>";
		}
	}

	/**
	 * Output row bg video
	 *
	 * @param array $style
	 */
	public function row_accordion( $row ) {
		if ( ! empty( $row['style']['accordion'] ) ) {
			$color = empty( $row['style']['accordion_text_color'] ) ? '#fff' : $row['style']['accordion_text_color'];
			if ( ! empty( $row['style']['accordion_text'] ) ) {
				echo "<h2 class='ppb-row-accordion-text' style='color:$color;'>{$row['style']['accordion_text']}</h2>";
			}
			?>
			<span class="ppb-row-accordion-toggle" style='border-color:<?php echo $color ?>;' onclick="jQuery( this ).toggleClass( 'ppb-accordion-open' )
			.siblings( '.panel-row-style' ).slideToggle(520);
			jQuery('html, body').animate({scrollTop:jQuery(this).offset().top - 50}, 520)"></span>
			<?php
		}
	}

	/**
	 * Output row bg video
	 *
	 * @param array $style
	 */
	public function row_bg_ken_burns( $style ) {
		echo
			'<div class="ppb-row-kenburns-wrap">' .
			"<img src='{$style['background_image']}' class='ppb-row-kenburns'>" .
			'</div>';
		remove_action( 'pootlepb_before_cells', array( $GLOBALS['Pootle_Page_Builder_Render_Layout'], 'row_bg_ken_burns' ) );
	}

	/**
	 * Output row bg video
	 *
	 * @param array $style
	 */
	public function row_bg_video( $style ) {

		if ( ! empty( $style['bg_video'] ) ) {

			$videoClasses = 'ppb-bg-video-container';

			if ( ! empty( $style['bg_mobile_image'] ) ) {
				$videoClasses .= ' hide-on-mobile';
			}
			?>
			<div class="<?php echo $videoClasses; ?>">
				<video class="<?php echo str_replace( '-container', '', $videoClasses ); ?>" preload="auto" autoplay="true" loop="loop" muted="muted"
				       volume="0">
					<?php
					echo "<source src='{$style['bg_video']}' type='video/mp4'>";
					echo "<source src='{$style['bg_video']}' type='video/webm'>";
					?>
					Sorry, your browser does not support HTML5 video.
				</video>
			</div>
		<?php
		}
		remove_action( 'pootlepb_before_cells', array( $GLOBALS['Pootle_Page_Builder_Render_Layout'], 'row_bg_video' ) );
	}

	/**
	 * Adds css to cells for column gutters
	 *
	 * @param string $css
	 * @param array $style
	 * @param string $rowID
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function row_col_gutter( $css, $style, $rowID ) {

		if ( isset( $style['col_gutter'] ) && is_numeric( $style['col_gutter'] ) ) {
			$css .= $rowID . ' .panel-grid-cell { padding: 0 ' . ( $style['col_gutter'] / 2 ) . '% 0; }';
		}

		return $css;
	}

	/**
	 * Sets the styles for column gutter
	 *
	 * @param string $css
	 * @param array $style
	 * @param string $rowID
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function row_overlay( $css, $style, $rowID ) {

		if ( isset( $style['background'] ) && ! empty( $style['bg_overlay_color'] ) ) {
			$overlay_color = $style['bg_overlay_color'];
			if ( ! empty( $style['bg_overlay_opacity'] ) ) {
				$overlay_color = 'rgba( ' . pootlepb_hex2rgb( $overlay_color ) . ', ' . ( 1 - $style['bg_overlay_opacity'] ) . ' )';
			}
			$css .= "$rowID .panel-row-style:before { background-color: $overlay_color; }";
		}

		return $css;
	}
}


/** @var Pootle_Page_Builder_Render_Grid Instance */
$GLOBALS['Pootle_Page_Builder_Render_Grid'] = new Pootle_Page_Builder_Render_Grid();