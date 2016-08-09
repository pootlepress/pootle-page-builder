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
