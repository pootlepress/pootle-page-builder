<?php
/*
Plugin Name: Pootle page builder Photography add on
Plugin URI: http://pootlepress.com/
Description: Create stunning slideshows and galleries in minutes.
Author: pootlepress
Version: 1.0.1
Author URI: http://pootlepress.com/
@developer shramee <shramee.srivastav@gmail.com>
*/

/** Plugin admin class */
require 'inc/class-admin.php';
/** Plugin public class */
require 'inc/class-public.php';
/** Including Main Plugin class */
require 'class-ppb-photo-addon.php';
/** Intantiating main plugin class */
page_builder_photo_addon::instance( __FILE__ );

/** Addon update API */
add_action( 'plugins_loaded', 'page_builder_photo_addon_api_init' );

/**
 * Instantiates Pootle_Page_Builder_Addon_Manager with current add-on data
 * @action plugins_loaded
 */
function page_builder_photo_addon_api_init() {
	//Return if POOTLEPB_DIR not defined
	if ( ! defined( 'POOTLEPB_DIR' ) ) { return; }
	/** Including PootlePress_API_Manager class */
	require_once POOTLEPB_DIR . 'inc/addon-manager/class-manager.php';
	/** Instantiating PootlePress_API_Manager */
	new Pootle_Page_Builder_Addon_Manager(
		page_builder_photo_addon::$token,
		'Pootle page builder Photography add on',
		page_builder_photo_addon::$version,
		page_builder_photo_addon::$file,
		page_builder_photo_addon::$token
	);
}
