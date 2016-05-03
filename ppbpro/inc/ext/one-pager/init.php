<?php
/*
Plugin Name: pootle page builder one pager
Plugin URI: http://pootlepress.com/
Description: pootle page builder one pager helps you to create beautiful one page websites with any theme.
Version: 1.1.0
Author: PootlePress
Author URI: http://pootlepress.com/
*/

/** Plugin admin class */
require 'inc/class-admin.php';
/** Plugin public class */
require 'inc/class-public.php';
/** Including Main Plugin class */
require 'class-ppb-one-pager.php';
/** Intantiating main plugin class */
pootle_page_builder_one_pager::instance( __FILE__ );

/** Addon update API */
add_action( 'plugins_loaded', 'pootle_page_builder_one_pager_api_init' );

/**
 * Instantiates Pootle_Page_Builder_Addon_Manager with current add-on data
 * @action plugins_loaded
 */
function pootle_page_builder_one_pager_api_init() {
	//Return if POOTLEPB_DIR not defined
	if ( ! defined( 'POOTLEPB_DIR' ) ) { return; }
	/** Including PootlePress_API_Manager class */
	require_once POOTLEPB_DIR . 'inc/addon-manager/class-manager.php';
	/** Instantiating PootlePress_API_Manager */
	new Pootle_Page_Builder_Addon_Manager(
		pootle_page_builder_one_pager::$token,
		'pootle page builder one pager',
		pootle_page_builder_one_pager::$version,
		pootle_page_builder_one_pager::$file,
		pootle_page_builder_one_pager::$token
	);
}
