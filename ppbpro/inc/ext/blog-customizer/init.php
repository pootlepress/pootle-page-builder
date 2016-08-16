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
