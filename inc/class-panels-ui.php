<?php
/**
 * Contains Pootle_Page_Builder_Admin_UI class
 * @author pootlepress
 * @since 0.1.0
 */

/**
 * Class Pootle_Page_Builder_Admin_UI
 * Renders pootle page builder user interface
 * @since 0.1.0
 */
final class Pootle_Page_Builder_Admin_UI {
	/**
	 * @var Pootle_Page_Builder_Admin_UI Instance
	 * @access protected
	 * @since 0.1.0
	 */
	protected static $instance;

	/**
	 * Magic __construct
	 * Adds the actions and filter hooks for plugin functioning
	 * @since 0.1.0
	 */
	public function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'metabox' ) );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_print_styles-post.php', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_print_styles-post.php', array( $this, 'enqueue_scripts' ) );
		add_filter( 'pootlepb_prebuilt_layouts', array( $this, 'cloned_page_layouts' ) );
		add_action( 'wp_ajax_so_panels_prebuilt', array( $this, 'ajax_action_prebuilt' ) );

	}

	/**
	 * Callback to register the Panels Metaboxes
	 * @since 0.1.0
	 */
	public function metabox() {
		foreach ( pootlepb_settings( 'post-types' ) as $type ) {
			add_meta_box( 'pootlepb-panels', __( 'Page Builder', 'ppb-panels' ), array(
				$this,
				'metabox_render'
			), $type, 'advanced', 'high' );
		}
	}

	/**
	 * Render a panel metabox.
	 * @param $post
	 * @since 0.1.0
	 */
	public function metabox_render( $post ) {
		include POOTLEPB_DIR . '/tpl/metabox-panels.php';
	}

	/**
	 * Enqueue the admin panel styles
	 *
	 * @action admin_print_styles-post-new.php
	 * @action admin_print_styles-post.php
	 * @since 0.1.0
	 */
	public function enqueue_styles() {
		$screen = get_current_screen();
		if ( in_array( $screen->id, pootlepb_settings( 'post-types' ) ) ) {
			wp_enqueue_script( 'pootlepb-ui' );
			wp_enqueue_style( 'pootlepb-ui-styles' );
			wp_enqueue_style( 'pootlepb-admin', POOTLEPB_URL . 'css/admin.css', array(), POOTLEPB_VERSION );
			wp_enqueue_style( 'ppb-chosen-style', POOTLEPB_URL . 'js/chosen/chosen.css' );
			wp_enqueue_style( 'wp-jquery-ui-dialog' );
			do_action( 'pootlepb_enqueue_admin_styles' );
		}
	}

	/**
	 * Enqueue the panels admin scripts
	 *
	 * @action admin_print_scripts-post-new.php
	 * @action admin_print_scripts-post.php
	 * @uses Pootle_Page_Builder_Admin_UI::enqueue_ui_scripts()
	 * @uses Pootle_Page_Builder_Admin_UI::localize_ui_scripts()
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();

		if ( in_array( $screen->id, pootlepb_settings( 'post-types' ) ) ) {

			$this->enqueue_dependencies();

			$this->enqueue_ui_scripts();

			$this->localize_ui_scripts();

			// This gives panels a chance to enqueue scripts too, without having to check the screen ID.
			do_action( 'pootlepb_enqueue_admin_scripts' );
			do_action( 'sidebar_admin_setup' );
		}
	}

	/**
	 * Enqueue UI scripts and their dependencies
	 * @since 0.1.0
	 */
	public function enqueue_ui_scripts() {

		global $pootlepb_ui_js_deps;

		//UI Scripts
		wp_enqueue_script( 'pootlepb-ui', POOTLEPB_URL . 'js/ui.dialog.js', POOTLEPB_VERSION );
		wp_enqueue_script( 'pootlepb-ui-admin', POOTLEPB_URL . 'js/ui.admin.js', $pootlepb_ui_js_deps, POOTLEPB_VERSION );
		wp_enqueue_script( 'pootlepb-ui-admin-sticky', POOTLEPB_URL . 'js/ui.admin.sticky.js', array( 'pootlepb-ui-admin', ), POOTLEPB_VERSION );
		wp_enqueue_script( 'pootlepb-ui-admin-panels', POOTLEPB_URL . 'js/ui.admin.panels.js', array( 'pootlepb-ui-admin', ), POOTLEPB_VERSION );
		wp_enqueue_script( 'pootlepb-ui-admin-grid', POOTLEPB_URL . 'js/ui.admin.grid.js', array( 'pootlepb-ui-admin' ), POOTLEPB_VERSION );
		wp_enqueue_script( 'pootlepb-ui-admin-prebuilt', POOTLEPB_URL . 'js/ui.admin.prebuilt.js', array( 'pootlepb-ui-admin', 'pootlepb-chosen', ), POOTLEPB_VERSION );
		wp_enqueue_script( 'pootlepb-ui-admin-tooltip', POOTLEPB_URL . 'js/ui.admin.tooltip.min.js', array( 'pootlepb-ui-admin', ), POOTLEPB_VERSION );
		wp_enqueue_script( 'pootlepb-ui-admin-media', POOTLEPB_URL . 'js/ui.admin.media.min.js', array( 'pootlepb-ui-admin', ), POOTLEPB_VERSION );
		wp_enqueue_script( 'pootlepb-ui-admin-styles', POOTLEPB_URL . 'js/ui.admin.styles.js', array( 'pootlepb-ui-admin', ), POOTLEPB_VERSION );
		wp_enqueue_script( 'pootlepb-ui-admin-media-buttons', POOTLEPB_URL . 'js/ui.admin.fields-handler.js', array( 'pootlepb-ui-admin', ) );
	}

	/**
	 * Enqueue the dependencies for the ui scripts
	 */
	protected function enqueue_dependencies() {

		global $pootlepb_ui_js_deps, $pootlepb_color_deps;

		//Dependencies
		foreach ( $pootlepb_ui_js_deps as $dep ) {
			wp_enqueue_script( $dep );
		}

		wp_dequeue_script( "iris" );
		wp_enqueue_script( "pp-pb-iris", POOTLEPB_URL . 'js/iris.js', $pootlepb_color_deps );
		wp_enqueue_script( 'pp-pb-color-picker', POOTLEPB_URL . 'js/color-picker-custom.js', array( 'pp-pb-iris' ) );
		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_script( 'pootlepb-ui-undomanager', POOTLEPB_URL . 'js/ui.admin.undomanager.min.js', array( 'jquery', ), POOTLEPB_VERSION );
		wp_enqueue_script( 'pootlepb-chosen', POOTLEPB_URL . 'js/chosen/chosen.jquery.min.js', array( 'jquery' ), POOTLEPB_VERSION );

	}

	/**
	 * Localizes UI scripts with panels data, i18n stuff and style fields
	 * @since 0.1.0
	 */
	protected function localize_ui_scripts() {
		global $pootlepb_ui_i18n, $pootlepb_color_i18n;

		//User Interface i18n
		$preview_url = wp_nonce_url( add_query_arg( 'pootlepb_preview', 'true', get_home_url() ), 'ppb-panels-preview' );
		wp_localize_script( 'pootlepb-ui-admin', 'panels', array( 'previewUrl' => $preview_url, 'i10n' => $pootlepb_ui_i18n, ) );

		//Panels Data
		$panels_data = $this->get_current_admin_panels_data();

		if ( count( $panels_data ) > 0 ) {
			wp_localize_script( 'pootlepb-ui-admin', 'panelsData', $panels_data );
		}

		// Row styles
		wp_localize_script( 'pootlepb-ui-admin', 'panelsStyleFields', pootlepb_row_settings_fields() );

		//Color picker i18n
		wp_localize_script( 'pp-pb-color-picker', 'wpColorPicker_i18n', $pootlepb_color_i18n );

	}

	/**
	 * Add current pages as cloneable pages
	 *
	 * @param $layouts
	 *
	 * @return mixed
	 * @since 0.1.0
	 */
	public function cloned_page_layouts( $layouts ) {
		$pages = get_posts( array(
			'post_type'   => pootlepb_settings( 'post-types' ),
			'post_status' => array( 'publish', 'draft' ),
			'numberposts' => 250,
		) );

		foreach ( $pages as $page ) {
			$panels_data = apply_filters( 'pootlepb_data', get_post_meta( $page->ID, 'panels_data', true ), $page->ID );

			if ( ! empty( $panels_data ) ) {

				$this->get_layout_from_post( $page, $panels_data, $layouts );
			}
		}

		return $layouts;
	}

	/**
	 * Get the Page Builder data for the current admin page.
	 * @return array
	 * @since 0.1.0
	 */
	public function get_current_admin_panels_data() {
		global $post;

		$panels_data = get_post_meta( $post->ID, 'panels_data', true );
		$panels_data = apply_filters( 'pootlepb_data', $panels_data, $post->ID );

		if ( empty( $panels_data ) ) {
			$panels_data = array();
		}

		//Set default styles if none
		if ( isset( $panels_data['widgets'] ) ) {
			//Add styles to content blocks if not set
			$this->add_default_styles_to_blocks( $panels_data['widgets'] );
		}

		return $panels_data;
	}


	/**
	 * Adds default styles to content block if not set
	 * @param $widgets
	 * @return array
	 * @since 0.1.0
	 */
	public function add_default_styles_to_blocks( &$widgets ) {

		//Set default styles if none
		foreach ( $widgets as &$widget ) {
			//If content block info is set but style info ain't
			if ( ! empty( $widget['info'] ) && empty( $widget['info']['style'] ) ) {
				//Set default style
				$widget['info']['style'] = pootlepb_default_content_block_style();
			}
		}
	}

	/**
	 * Add current pages as cloneable pages
	 *
	 * @param object $page Post object
	 * @param array $panels_data
	 * @param array $layouts Prebuilt layouts
	 *
	 * @return mixed
	 * @since 0.1.0
	 */
	protected function get_layout_from_post( $page, $panels_data, &$layouts ) {

		$name = empty( $page->post_title ) ? __( 'Untitled', 'ppb-panels' ) : $page->post_title;

		if ( $page->post_status != 'publish' ) {
			$name .= ' ( ' . __( 'Unpublished', 'ppb-panels' ) . ' )';
		}

		if ( current_user_can( 'edit_post', $page->ID ) ) {

			$layouts[ 'post-' . $page->ID ] = wp_parse_args(
				array(
					'name' => sprintf( __( 'Clone Page: %s', 'ppb-panels' ), $name )
				),
				$panels_data
			);
		}
	}

	/**
	 * Admin ajax handler for loading a prebuilt layout.
	 * @since 0.1.0
	 */
	public function ajax_action_prebuilt() {
		// Get any layouts that the current user could edit.
		$layouts = apply_filters( 'pootlepb_prebuilt_layouts', array() );

		if ( empty( $_GET['layout'] ) ) {
			exit();
		}
		if ( empty( $layouts[ $_GET['layout'] ] ) ) {
			exit();
		}

		header( 'content-type: application/json' );

		$layout = ! empty( $layouts[ $_GET['layout'] ] ) ? $layouts[ $_GET['layout'] ] : array();
		$layout = apply_filters( 'pootlepb_prebuilt_layout', $layout );

		echo json_encode( $layout );
		exit();
	}
}

/** @var Pootle_Page_Builder_Content_Block Instance */
$GLOBALS['Pootle_Page_Builder_Admin_UI'] = new Pootle_Page_Builder_Admin_UI();