<?php
/*
Plugin Name: pootle page builder for WooCommerce
Plugin URI: http://pootlepress.com/
Description: pootle page builder for WooCommerce brings powerful WooCommerce features into page builder. Create stunning pages featuring your products by id, category, attribute, best-selling, top rated and on-sale, plus use pootle page builder on product pages and with WooCommerce Tab Manager.
Author: pootlepress
Version: 1.1.0
Author URI: http://pootlepress.com/
Text Domain: ppb-woocommerce
*/

/** Plugin admin class */
require 'inc/class-admin.php';
/** Plugin public class */
require 'inc/class-public.php';
/** Including Main Plugin class */
require_once 'class-ppb-for-woocommerce.php';
/** Intantiating main plugin class */
pootle_page_builder_for_WooCommerce::instance( __FILE__ );

/** Addon update API */
add_action( 'plugins_loaded', 'pootle_page_builder_for_WooCommerce_api_init' );

/**
 * Instantiates Pootle_Page_Builder_Addon_Manager with current add-on data
 * @action plugins_loaded
 */
function pootle_page_builder_for_WooCommerce_api_init() {
	//Return if POOTLEPB_DIR not defined
	if ( ! defined( 'POOTLEPB_DIR' ) ) { return; }
	/** Including PootlePress_API_Manager class */
	require_once POOTLEPB_DIR . 'inc/addon-manager/class-manager.php';
	/** Instantiating PootlePress_API_Manager */
	new Pootle_Page_Builder_Addon_Manager(
		pootle_page_builder_for_WooCommerce::$token,
		'pootle page builder for WooCommerce',
		pootle_page_builder_for_WooCommerce::$version,
		pootle_page_builder_for_WooCommerce::$file,
		pootle_page_builder_for_WooCommerce::$token
	);
}
