<?php
/*
Plugin Name: Pootle Post Builder Addon
Plugin URI: http://pootlepress.com/
Description: Enables you to use the pootle page builder to build awesome posts
Author: pootlepress
Version: 1.0.0
Author URI: http://pootlepress.com/
@developer shramee <shramee.srivastav@gmail.com>
*/
/** Including Main Plugin class */
require 'class-ppb-post-builder.php';
/** Intantiating main plugin class */
PPB_Post_Builder_Addon::instance( __FILE__ );
