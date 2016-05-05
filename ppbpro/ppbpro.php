<?php
/*
 * Plugin Name: Pootle Page Builder Pro
 * Plugin URI: http://pootlepress.com/
 * Description: Pro add on for pootle page builder, exhibit your posts, WooCommerce products, photos in grids, masonry layout or slides. Customize individual pages and create beautiful one page parallax websites.
 * Author: pootlepress
 * Version: 1.0.0
 * Author URI: http://pootlepress.com/
 * @developer wpdevelopment.me <shramee@wpdevelopment.me>
 */

/** Set variables */
require 'inc/vars.php';
/** Plugin admin class */
require 'inc/class-admin.php';
/** Plugin public class */
require 'inc/class-public.php';
/** Including Main Plugin class */
require 'class-ppbpro.php';
/** Intantiating main plugin class */
Pootle_Page_Builder_Pro::instance( __FILE__ );

/** Addon update API */
add_action( 'plugins_loaded', 'Pootle_Page_Builder_Pro_api_init' );

/**
 * Instantiates Pootle_Page_Builder_Addon_Manager with current add-on data
 * @action plugins_loaded
 */
function Pootle_Page_Builder_Pro_api_init() {
	//Return if POOTLEPB_DIR not defined
	if ( ! defined( 'POOTLEPB_DIR' ) ) { return; }
	/** Including PootlePress_API_Manager class */
	require_once POOTLEPB_DIR . 'inc/addon-manager/class-manager.php';
	/** Instantiating PootlePress_API_Manager */
	new Pootle_Page_Builder_Addon_Manager(
		Pootle_Page_Builder_Pro::$token,
		'Pootle Page Builder Pro',
		Pootle_Page_Builder_Pro::$version,
		Pootle_Page_Builder_Pro::$file,
		Pootle_Page_Builder_Pro::$token
	);
}

