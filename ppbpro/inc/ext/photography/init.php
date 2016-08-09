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
