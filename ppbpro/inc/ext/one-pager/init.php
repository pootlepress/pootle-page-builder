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
