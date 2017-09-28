<?php
/**
 * Contains Pootle_Page_Builder_Render_Layout class
 * @author pootlepress
 * @since 0.1.0
 */

/**
 * Pootle_Page_Builder_Render_Grid class
 */
require_once POOTLEPB_DIR . 'inc/class-render-grid.php';

/**
 * Class Pootle_Page_Builder_Render_Layout
 * Renders the grid on front end
 * @uses Pootle_Page_Builder_Render_Grid
 */
final class Pootle_Page_Builder_Render_Layout extends Pootle_Page_Builder_Render_Grid {

	/**
	 * @var Pootle_Page_Builder_Render_Layout Instance
	 * @access protected
	 * @since 0.1.0
	 */
	protected static $instance;

	static function render( $post_id = false, $panels_data = array() ) {
		return Pootle_Page_Builder_Render_Layout::$instance->panels_render( $post_id, $panels_data );
	}

	/**
	 * Magic __construct
	 * @since 0.1.0
	 */
	public function __construct() {
		Pootle_Page_Builder_Render_Layout::$instance = $this;
		/* Main content filter */
		add_filter( 'the_content', array( $this, 'content_filter' ) );

		//Row custom styles
		require_once POOTLEPB_DIR . 'inc/class-custom-styles.php';

		/* Puts stuff in row */
		add_action( 'pootlepb_before_row', array( $this, 'row_accordion' ) );
		add_action( 'pootlepb_before_cells', array( $this, 'row_embed_css' ), 10, 2 );
		add_filter( 'pootlepb_row_cell_attributes', array( $this, 'row_animation' ), 10, 4 );

		/* Embed styles */
		add_action( 'pootlepb_row_embed_style', array( $this, 'row_col_gutter' ), 10, 3 );
		add_action( 'pootlepb_row_embed_style', array( $this, 'row_overlay' ), 10, 3 );
	}

	/**
	 * Filter the content of the panel, adding all the content blocks.
	 *
	 * @param string $content Post content
	 *
	 * @return string Pootle page builder post content
	 * @filter the_content
	 * @since 0.1.0
	 */
	public function content_filter( $content ) {

		$postID = apply_filters( 'pootlepb_the_content_id', get_the_ID() );

		$pass = apply_filters( 'pootlepb_the_content_pass', in_array( get_post_type(), pootlepb_settings( 'post-types' ) ) );

		if ( ! $pass ) {
			return $content;
		}

		$post          = get_post( $postID );
		$panel_content = $this->panels_render( $post->ID );

		if ( ! empty( $panel_content ) ) {
			$content = $panel_content;
		}

		return $content;
	}

	/**
	 * Set's panels data if empty
	 *
	 * @param array|bool $panels_data
	 * @param int $post_id
	 *
	 * @return bool
	 * @since 0.1.0
	 */
	protected function any_problem( &$panels_data, &$post_id ) {

		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		if ( empty( $panels_data ) ) {
			$panels_data = get_post_meta( $post_id, 'panels_data', true );
		}

		$panels_data = apply_filters( 'pootlepb_data', $panels_data, $post_id );

		$ppbnew = get_post_meta( $post_id, 'pootlepb-new', true );

		if ( empty( $ppbnew ) && ( empty( $panels_data ) || empty( $panels_data['grids'] ) ) ) {
			return true;
		}
	}

	/**
	 * Render the panels
	 * @param int|string|bool $post_id The Post ID or 'home'.
	 * @param array|bool $panels_data Existing panels data. By default load from settings or post meta.
	 * @uses Pootle_Page_Builder_Front_Css_Js::panels_generate_css()
	 * @return string
	 * @since 0.1.0
	 */
	function panels_render( $post_id = false, $panels_data = array() ) {
		//Post ID and Panels Data
		if ( $this->any_problem( $panels_data, $post_id ) ) {
			return '';
		}

		global $pootlepb_current_post;
		$old_current_post      = $pootlepb_current_post;
		$pootlepb_current_post = $post_id;

		if ( post_password_required( $post_id ) && get_post_type( $post_id ) != 'wc_product_tab' ) {
			return '';
		}

		//Removing filters for proper functionality
		//wptexturize : Replaces each & with &#038; unless it already looks like an entity
		remove_filter( 'the_content', 'wptexturize' );
		//convert_chars : Converts & characters into &#38; ( a.k.a. &amp; )
		remove_filter( 'the_content', 'convert_chars' );
		//wpautop : Adds paragraphs for every two line breaks
		remove_filter( 'the_content', 'wpautop' );

		// Create the skeleton of the grids
		$grids = array();

		$this->google_fonts( $panels_data );
		$this->grids_array( $grids, $panels_data );

		ob_start();

		global $pootlepb_inline_css;
		$pootlepb_inline_css .= $GLOBALS['Pootle_Page_Builder_Front_Css_Js']->panels_generate_css( $post_id, $panels_data );

		?>
		<div id="pootle-page-builder">
			<!----------Pootle Page Builder Inline Styles---------->
			<style id="pootle-live-editor-styles" type="text/css" media="all">
				<?php echo $pootlepb_inline_css ?>
			</style>
			<?php

			$pootlepb_inline_css = '';

			$this->output_rows( $grids, $panels_data, $post_id );

			?>
		</div>
		<?php

		$html = ob_get_clean();

		// Reset the current post
		$pootlepb_current_post = $old_current_post;
		return apply_filters( 'pootlepb_render', $html, $post_id, null );
	}

	protected function google_fonts( $panels_data ) {
		if ( ! empty( $panels_data['google_fonts'] ) ) {
			echo '<link href="https://fonts.googleapis.com/css?family=' . implode( $panels_data['google_fonts'], '|' ) . '" rel="stylesheet">';
		}
	}

	/**
	 * Convert panels data into grid>cell>widget format
	 *
	 * @param array $grids
	 * @param array $panels_data
	 *
	 * @since 0.1.0
	 */
	protected function grids_array( &$grids, $panels_data ) {

		if ( ! empty( $panels_data['grids'] ) ) {
			foreach ( $panels_data['grids'] as $gi => $grid ) {
				$gi           = intval( $gi );
				$grids[ $gi ] = array();
				for ( $i = 0; $i < $grid['cells']; $i ++ ) {
					$grids[ $gi ][ $i ] = array();
				}
			}
		}

		$this->grids_array_add_blocks( $grids, $panels_data );
	}

	/**
	 * Adds content blocks to grid array from panels data
	 *
	 * @param array $grids
	 * @param array $panels_data
	 *
	 * @since 0.1.0
	 */
	protected function grids_array_add_blocks( &$grids, $panels_data ) {

		if ( ! empty( $panels_data['widgets'] ) && is_array( $panels_data['widgets'] ) ) {
			foreach ( $panels_data['widgets'] as $widget ) {

				if ( ! empty( $widget['info'] ) ) {
					$grids[ intval( $widget['info']['grid'] ) ][ intval( $widget['info']['cell'] ) ][] = $widget;
				}
			}
		}
	}

	public function row_animation( $attrs, $ci, $gi, $style ) {
		if ( ! empty( $style['animate_cols'] ) ) {
			$attrs['data--100-bottom-top'] = "@data-animation:!$style[animate_cols]";
			$attrs['data-bottom-top'] = "@data-animation:!0";
			$attrs['class'] .= "ppb-desktop-transparent";
		}

		return $attrs;
	}
}

/** @var Pootle_Page_Builder_Render_Layout Instance */
$GLOBALS['Pootle_Page_Builder_Render_Layout'] = new Pootle_Page_Builder_Render_Layout();