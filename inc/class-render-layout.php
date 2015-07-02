<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 25/6/15
 * Time: 11:22 PM
 * @since 0.1.0
 */

/**
 * Pootle_Page_Builder_Render_Grid class
 */
require_once POOTLEPB_DIR . 'inc/class-render-grid.php';

/**
 * @extends Pootle_Page_Builder_Render_Grid
 * Class Pootle_Page_Builder_Render_Layout
 */
final class Pootle_Page_Builder_Render_Layout extends Pootle_Page_Builder_Render_Grid {

	/**
	 * @var Pootle_Page_Builder_Render_Layout
	 * @access protected
	 * @since 0.1.0
	 */
	protected static $instance;

	/**
	 * Magic __construct
	 * @since 0.1.0
	 */
	protected function __construct() {
		$this->hooks();
	}

	/**
	 * Adds the actions and filter hooks for plugin functioning
	 * @since 0.1.0
	 */
	private function hooks() {
		/* Main content filter */
		add_filter( 'the_content', array( $this, 'content_filter' ) );
		parent::row_hooks();
	}

	/**
	 * Filter the content of the panel, adding all the widgets.
	 * @param string $content Post content
	 * @return string Pootle page builder post content
	 * @filter the_content
	 * @since 0.1.0
	 */
	function content_filter( $content ) {

		$postID = get_the_ID();

		$isWooCommerceInstalled =
			function_exists( 'is_shop' ) && function_exists( 'wc_get_page_id' );

		if ( $isWooCommerceInstalled ) {
			// prevent Page Builder overwrite taxonomy description with widget content
			if ( ( is_tax( array( 'product_cat', 'product_tag' ) ) && get_query_var( 'paged' ) == 0 ) || ( is_post_type_archive() && ! is_shop() ) ) {
				return $content;
			}

			if ( is_shop() ) {
				$postID = wc_get_page_id( 'shop' );
			}
		} else {
			if ( is_post_type_archive() ) {
				return $content;
			}
		}

		//If product done once set $postID to Tabs Post ID
		if ( isset( $GLOBALS['canvasPB_ProductDoneOnce'] ) ) {
			global $wpdb;
			$results = $wpdb->get_results(
				"SELECT ID FROM "
				. $wpdb->posts
				. " WHERE "
				. "post_content LIKE '"
				. esc_sql( $content )
				. "'"
				. " AND post_type LIKE 'wc_product_tab'"
				. " AND post_status LIKE 'publish'" );
			foreach ( $results as $id ) {
				$postID = $id->ID;
			}
		}
		//If its product set canvasPB_ProductDoneOnce to skip this for TAB
		if ( function_exists( 'is_product' ) ) {
			if ( is_single() && is_product() ) {
				$GLOBALS['canvasPB_ProductDoneOnce'] = true;
			}
		}

		$post = get_post( $postID );

		if ( empty( $post ) ) {
			return $content;
		}
		if ( in_array( $post->post_type, pootlepb_settings( 'post-types' ) ) ) {
			$panel_content = $this->panels_render( $post->ID );

			if ( ! empty( $panel_content ) ) {
				$content = $panel_content;
			}
		}

		return $content;
	}

	/**
	 * Render the panels
	 *
	 * @param int|string|bool $post_id The Post ID or 'home'.
	 * @param bool $enqueue_css Should we also enqueue the layout CSS.
	 * @param array|bool $panels_data Existing panels data. By default load from settings or post meta.
	 * @uses Pootle_Page_Builder_Front_Css_Js::panels_generate_css()
	 * @return string
	 * @since 0.1.0
	 */
	function panels_render( $post_id = false, $panels_data = false ) {
		//Post ID and Panels Data
		if ( $this->any_problem( $panels_data, $post_id ) ) {
			return '';
		}

		global $pootlepb_current_post;
		$old_current_post = $pootlepb_current_post;
		$pootlepb_current_post = $post_id;

		if ( post_password_required( $post_id ) && get_post_type( $post_id ) != 'wc_product_tab' ) {
			return false;
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

		$this->grids_array( $grids, $panels_data );

		ob_start();

		global $pootlepb_inline_css;
		$pootlepb_inline_css .= Pootle_Page_Builder_Front_Css_Js::instance()->panels_generate_css( $post_id, $panels_data );

		$this->output_rows( $grids, $panels_data, $post_id );

		$html = ob_get_clean();

		// Reset the current post
		$pootlepb_current_post = $old_current_post;

		return apply_filters( 'pootlepb_render', $html, $post_id, null );
	}
}

//Instantiating Pootle_Page_Builder_Render_Layout class
Pootle_Page_Builder_Render_Layout::instance();