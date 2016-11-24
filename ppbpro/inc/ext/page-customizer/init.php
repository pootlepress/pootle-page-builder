<?php
/**
 * Plugin Name: Page Customizer
 * Plugin URI:  http://pootlepress.com/
 * Description: Page customizer adds options for individual pages. Add a fullscreen background video, change page background image and color, change header background image and color. Hide header, titles, breadcrumbs, sidebar and footer. Mobile options to change background image and color for phones and tablets.
 * Version:     1.0.0
 * Author:      PootlePress
 * Author URI:  http://pootlepress.com/
 * Requires at least: 4.0.0
 * Tested up to: 4.1.1
 *
 * Text Domain: pootle-page-customizer
 * Domain Path: /languages/
 *
 * @package Pootle_Page_Customizer
 * @category Core
 * @author PootlePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

//Variables
require_once( dirname( __FILE__ ) . '/includes/vars.php' );

//Post meta customizer
require_once( dirname( __FILE__ ) . '/includes/class-customizer-postmeta.php' );

//Post meta customizer
require_once( dirname( __FILE__ ) . '/includes/class-public-styles.php' );

/** Addon update API */
add_action( 'plugins_loaded', 'pootle_page_customizer_api_init' );

/**
 * Instantiates Pootle_Page_Builder_Addon_Manager with current add-on data
 * @action plugins_loaded
 */
function pootle_page_customizer_api_init() {
	$instance = Pootle_Page_Customizer();
	//Return if POOTLEPB_DIR not defined
	if ( ! defined( 'POOTLEPB_DIR' ) ) { return; }
	/** Including PootlePress_API_Manager class */
	require_once POOTLEPB_DIR . 'inc/addon-manager/class-manager.php';
	/** Instantiating PootlePress_API_Manager */
	new Pootle_Page_Builder_Addon_Manager(
		$instance->token,
		'Page Customizer',
		$instance->version,
		__FILE__,
		$instance->token
	);
}

/**
 * Returns the main instance of Pootle_Page_Customizer to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Pootle_Page_Customizer
 */
function Pootle_Page_Customizer() {
	return Pootle_Page_Customizer::instance();
} // End Pootle_Page_Customizer()

$Pootle_Page_Customizer_Instance = Pootle_Page_Customizer();

/**
 * Main Pootle_Page_Customizer Class
 *
 * @class Pootle_Page_Customizer
 * @version    1.0.0
 * @since 1.0.0
 * @package    Pootle_Page_Customizer
 * @author PootlePress
 */
final class Pootle_Page_Customizer {
	/**
	 * Pootle_Page_Customizer The single instance of Pootle_Page_Customizer.
	 * @var    object
	 * @access  private
	 * @since    1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	/**
	 * The plugin directory URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_url;

	/**
	 * The plugin directory path.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_path;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * The settings object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings;

	/**
	 * All the post metas to populate.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $fields = array();

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 */
	public function __construct() {
		$this->token       = 'pootle-page-customizer';
		$this->plugin_url  = plugin_dir_url( __FILE__ );
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->version     = '1.0.0';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'setup' ) );
	}

	/**
	 * Main Pootle_Page_Customizer Instance
	 *
	 * Ensures only one instance of Pootle_Page_Customizer is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Pootle_Page_Customizer()
	 * @return Pootle_Page_Customizer instance
	 */
	public static function instance() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'pootle-page-customizer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Installation.
	 * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
	}

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

	/**
	 * Setup all the things.
	 * @return void
	 */
	public function setup() {
		$this->load_plugin_textdomain();
		$this->get_meta_fields();

		new Lib_Customizer_Postmeta( $this->token, 'Page Customizer', $this->fields );
		new Pootle_Page_Customizer_Public( $this->token );

		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customizer_script' ) );
		add_action( 'admin_bar_menu', array( $this, 'add_item' ), 999 );
	}

	/**
	 * @param $admin_bar
	 */
	function add_item( $admin_bar ) {
		global $post;
		if ( is_page() ) {
			$args = array(
				'id'    => 'page-custo-link',
				'title' => 'Customize Page',
				'href'  => admin_url( "customize.php?post_id={$post->ID}&autofocus[panel]=lib-pootle-page-customizer&url=" . get_permalink( $post->ID ) . "?post_id={$post->ID}" ),
				'meta'  => array(
					'title' => __( 'Customize this page in customizer' ), // Text will be shown on hovering
				),
			);
			$admin_bar->add_menu( $args );
		}
	}

	/**
	 * Adds control scripts to WP_Customize_Manager
	 * @since 1.0.0
	 */
	public function customizer_script() {
		wp_enqueue_script( 'pppc-customize-controls', plugin_dir_url( __FILE__ ) . 'assets/js/customizer.js', array( 'jquery' ), false, true );
		wp_enqueue_style( 'pppc-customize-controls-styles', plugin_dir_url( __FILE__ ) . 'assets/css/customizer.css' );
	}

	private function get_meta_fields() {
		global $page_customizer_fields;
		$this->fields = apply_filters( 'storefront_page_customizer', $page_customizer_fields );
	}

} // End Class