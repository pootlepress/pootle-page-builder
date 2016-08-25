<?php
/**
 * Pootle Page Builder Pro Admin class
 * @property string token Plugin token
 * @property string $url Plugin root dir url
 * @property string $path Plugin root dir path
 * @property string $version Plugin version
 */
class Pootle_Page_Builder_Pro_Admin{

	/**
	 * @var 	Pootle_Page_Builder_Pro_Admin Instance
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Main Pootle Page Builder Pro Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @return Pootle_Page_Builder_Pro_Admin instance
	 * @since 	1.0.0
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Constructor function.
	 * @access  private
	 * @since 	1.0.0
	 */
	private function __construct() {
		$this->token   =   Pootle_Page_Builder_Pro::$token;
		$this->url     =   Pootle_Page_Builder_Pro::$url;
		$this->path    =   Pootle_Page_Builder_Pro::$path;
		$this->version =   Pootle_Page_Builder_Pro::$version;
	} // End __construct()

	/**
	 * Adds add ons admin menu page
	 * @param array $tabs The array of tabs
	 * @action admin_menu
	 * @since 	1.0.0
	 */
	public function admin_menu() {
		remove_submenu_page( 'page_builder', 'page_builder_addons' );
		add_submenu_page( 'page_builder', 'Templates', 'Templates', 'manage_options', 'page_builder_templates', array(
			$this,
			'templates',
		) );
		/*
		add_submenu_page( 'page_builder', 'Pootle Page Builder Pro', 'Page Builder Pro', 'manage_options', 'page_builder_pro', array(
			$this,
			'menu_page',
		) );
		*/
	}

	/**
	 * Modifies templateß
	 * @param array $otpl Original template data
	 * @return array Template data
	 */
	public function filter_template( $otpl, $id ) {
		$tpl_key = filter_input( INPUT_GET, 'tpl' );
		$tpl = ppbpro_get_template( $tpl_key );
		
		if ( $tpl ) {
			$pc = ppbpro_get_tpl_pc_data( $tpl_key );
			if ( $pc ) {
				update_post_meta( $id, 'pootle-page-customizer', $pc );
			}
			return $tpl;
		} else {
			return $otpl;
		}
	}

	/**
	 * Adds row settings panel fields
	 * @param array $fields Fields to output in row settings panel
	 * @return array Tabs
	 * @filter pootlepb_row_settings_fields
	 * @since 	1.0.0
	 */
	public function row_settings_fields( $fields ) {
		$fields[ $this->token . '_sample_color' ] = array(
			'name' => 'Sample color',
			'type' => 'color',
			'priority' => 1,
			'tab' => $this->token,
			'help-text' => 'This is a sample boilerplate field, Sets 12px outline color.'
		);
		return $fields;
	}

	/**
	 * Display the admin page.
	 * @since 0.1.0
	 */
	public function menu_page() {

		include 'tpl-addons.php';
	}

	/**
	 * Display the admin page.
	 * @since 0.1.0
	 */
	public function templates() {

		include 'tpl-templates.php';
	}

	/**
	 * Initiates Settings API sections, controls and settings
	 * @action init
	 * @since    1.0.0
	 */
	public function init_settings() {
		// Finally, we register the fields with WordPress
		register_setting(
			'ppbpro_active_addons',
			'ppbpro_active_addons'
		);
	}

	/**
	 * Adds content block panel fields
	 * @param array $fields Fields to output in content block panel
	 * @return array Tabs
	 * @filter pootlepb_content_block_fields
	 * @since 	1.0.0
	 */
	function row_fields( $fields ) {
		$fields[ 'ppbpro-row-css' ] = array(
			'name' => 'CSS for Row Elements',
			'placeholder' => 'Styles with selector here...',
			'type' => 'textarea',
			'priority' => 1,
			'tab' => 'advanced',
		);
		return $fields;
	}

	/**
	 * Adds content block panel fields
	 * @param array $fields Fields to output in content block panel
	 * @return array Tabs
	 * @filter pootlepb_content_block_fields
	 * @since 	1.0.0
	 */
	function modules( $modules ) {
		$modules['photo-gallery'] = array(
			'label'       => 'Photo Gallery',
			'icon_class'  => 'dashicons dashicons-images-alt2',
			'tab'         => '#pootle-ppb-photo-addon-tab',
			'ActiveClass' => 'page_builder_photo_addon',
			'priority'    => 10,
		);
		$modules['blog-posts'] = array(
			'label'       => 'Blog posts',
			'icon_class'  => 'dashicons dashicons-admin-post',
			'tab'         => '#pootle-ppb-blog-customizer-tab',
			'ActiveClass' => 'page_builder_photo_addon',
			'priority'    => 25,
		);
		$modules['wc-products'] = array(
			'label'       => 'WooCommerce',
			'icon_class'  => 'dashicons dashicons-cart',
			'tab'         => '#pootle-wc_prods-tab',
			'ActiveClass' => 'pootle_page_builder_for_WooCommerce',
			'priority'    => 30,
		);
		return $modules;
	}

	/**
	 * Enqueue admin scripts and styles
	 * @global $pagenow
	 * @action admin_notices
	 * @since 0.1.0
	 */
	public function enqueue(){
		global $pagenow;
		if ( $pagenow == 'admin.php' && 'page_builder_modules' == filter_input( INPUT_GET, 'page' ) ) {
			$token = $this->token;
			$url = $this->url;
			wp_enqueue_style( $token . '-css', $url . '/assets/admin-page.css' );
			wp_enqueue_script( $token . '-js', $url . '/assets/admin-page.js', array( 'jquery' ) );
		}
	}

}