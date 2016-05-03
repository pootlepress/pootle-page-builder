<?php
/*
Plugin Name: pootle page builder blog customizer
Plugin URI: http://pootlepress.com/
Description: Blog customizer for pootle page builder helps you create a beautiful blog page
Author: pootlepress
Version: 1.0.0
Author URI: http://pootlepress.com/
*/

/** Plugin admin class */
require 'inc/class-admin.php';
/** Plugin public class */
require 'inc/class-public.php';
/** Including Main Plugin class */
require 'class-ppb-blog-customizer.php';
/** Intantiating main plugin class */
pootle_page_builder_blog_customizer::instance( __FILE__ );

/** Addon update API */
add_action( 'plugins_loaded', 'pootle_page_builder_blog_customizer_api_init' );

/**
 * Instantiates Pootle_Page_Builder_Addon_Manager with current add-on data
 * @action plugins_loaded
 */
function pootle_page_builder_blog_customizer_api_init() {
	//Return if POOTLEPB_DIR not defined
	if ( ! defined( 'POOTLEPB_DIR' ) ) { return; }
	/** Including PootlePress_API_Manager class */
	require_once POOTLEPB_DIR . 'inc/addon-manager/class-manager.php';
	/** Instantiating PootlePress_API_Manager */
	new Pootle_Page_Builder_Addon_Manager(
		pootle_page_builder_blog_customizer::$token,
		'pootle page builder blog customizer',
		pootle_page_builder_blog_customizer::$version,
		pootle_page_builder_blog_customizer::$file,
		pootle_page_builder_blog_customizer::$token
	);
}

