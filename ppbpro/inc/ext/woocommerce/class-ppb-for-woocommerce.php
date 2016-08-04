<?php

class pootle_page_builder_for_WooCommerce{

	/**
	 * pootle_page_builder_for_WooCommerce Instance of main plugin class.
	 *
	 * @var 	object pootle_page_builder_for_WooCommerce
	 * @access  private
	 * @since 	0.1.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var	 string
	 * @access  public
	 * @since   0.1.0
	 */
	public static $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public static $version;

	/**
	 * @var 	string Plugin main __FILE__
	 * @access  public
	 * @since 	1.0.0
	 */
	public static $file;

	/**
	 * pootle page builder for WooCommerce plugin directory URL.
	 *
	 * @var 	string Plugin directory
	 * @access  private
	 * @since 	0.1.0
	 */
	public static $url;

	/**
	 * pootle page builder for WooCommerce plugin directory Path.
	 *
	 * @var 	string Plugin directory
	 * @access  private
	 * @since 	0.1.0
	 */
	public static $path;

	/**
	 * @var 	pootle_page_builder_for_WooCommerce_Admin Instance
	 * @access  public
	 * @since 	1.0.0
	 */
	public $admin;

	/**
	 * @var 	pootle_page_builder_for_WooCommerce_Public Instance
	 * @access  public
	 * @since 	1.0.0
	 */
	public $public;

	/**
	 * Main pootle page builder for WooCommerce Instance
	 *
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 *
	 * @since 0.1.0
	 * @return pootle_page_builder_for_WooCommerce instance
	 */
	public static function instance( $file ) {
		if ( null == self::$_instance ) {
			self::$_instance = new self( $file );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Constructor function.
	 * @access  private
	 * @since   0.1.0
	 */
	private function __construct( $file ) {

		self::$token   = 'ppb-for-WooCommerce';
		self::$file    = $file;
		self::$url     = plugin_dir_url( $file );
		self::$path    = plugin_dir_path( $file );
		self::$version = '1.1.0';

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'check_ppb_version' ) );
	} // End __construct()

	/**
	 * Initiates the plugin
	 * @action init
	 * @since 0.1.0
	 */
	public function init() {

		if ( class_exists( 'Pootle_Page_Builder' ) ) {

			//Initiate admin
			$this->_admin();

			//Initiate public
			$this->_public();

			//Mark this add on as active - Not required with Freemius handling updates
			//add_filter( 'pootlepb_installed_add_ons', array( $this, 'add_on_active' ) );
		}
	} // End init()

	/**
	 * Initiates admin class and adds admin hooks
	 * @since 1.0.0
	 */
	private function _admin() {
		//Instantiating admin class
		$this->admin = pootle_page_builder_for_WooCommerce_Admin::instance();

		//Adding admin scripts
		add_action( 'pootlepb_enqueue_admin_scripts', array( $this->admin, 'admin_enqueue' ) );
		//Adds wc tabs and products as supported post types
		add_filter( 'pootlepb_builder_post_types', array( $this->admin, 'add_wc_posts' ) );
		//Remove the default wc tab
		remove_action( 'pootlepb_content_block_tabs', array( $GLOBALS['Pootle_Page_Builder_Content_Block'], 'add_wc_tab' ) );
		remove_action( 'pootlepb_content_block_WooCommerce_tab', array( $GLOBALS['Pootle_Page_Builder_Content_Block'], 'wc_tab' ) );
		//Change WooCommerce tab to products
		add_filter( 'pootlepb_content_block_tabs', array( $this->admin, 'add_tab' ) );
		add_filter( 'pootlepb_le_content_block_tabs', array( $this->admin, 'add_tab' ), 11 );
		//Content block fields
		add_filter( 'pootlepb_content_block_fields', array( $this->admin, 'content_block_fields' ) );
		//Add our awesome stuff
		add_action( 'pootlepb_content_block_wc_prods_tab_after_fields', array( $this->admin, 'wc_required_notice' ) );

	}

	/**
	 * Initiates public class and adds public hooks
	 * @since 1.0.0
	 */
	private function _public() {
		//Instantiating public class
		$this->public = pootle_page_builder_for_WooCommerce_Public::instance();

		//Adding front end JS and CSS in /assets folder
		add_action( 'wp_enqueue_scripts', array( $this->public, 'enqueue' ) );
		//Filter the tabs content
		add_filter( 'woocommerce_tab_manager_tab_panel_content', array( $this->public, 'wc_tabs_filter' ), 7, 3 );
		//Add the products to content block
		add_action( 'pootlepb_render_content_block', array( $this->public, 'render_products' ), 52 );

	} // End enqueue()

	/**
	 * Marks this add on as active on
	 * @param array $active Active add ons
	 * @return array Active add ons
	 * @since 1.0.0
	 */
	public function add_on_active( $active ) {

		// To allows ppb add ons page to fetch name, description etc.
		$active[ self::$token ] = self::$file;

		return $active;
	}

	/**
	 * Activation hook
	 * @param array $active Active add ons
	 * @return array Active add ons
	 * @since 1.0.0
	 */
	public function check_ppb_version() {
		if ( ! defined( 'POOTLEPB_VERSION' ) || version_compare( POOTLEPB_VERSION, '0.3.0', '<' ) ) {
			deactivate_plugins( plugin_basename( self::$file ) );
			wp_die( "We can't activate pootle page builder for WooCommmerce unless pootle page builder version 0.3+ is installed <br>" . '<a href="' . admin_url( '/plugins.php' ) . '"> Back </a>' );
		}
	}
}
