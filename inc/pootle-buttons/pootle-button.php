<?php
/**
 * Plugin Name: pootle button
 * Plugin URI: http://pootlepress.com/
 * Description: A cool plugin to add delicious buttons in WordPress editor
 * Version: 1.1.1
 * Author: pootlepress
 * Author URI: http://pootlepress.com/
 * License: GPL version 3
 * @developer http://wpdevelopment.me <shramee@wpdevelopment.me>
 */

add_action( 'admin_head', 'pbtn_l10n' );
add_action( 'wp_head', 'pbtn_l10n' );
function pbtn_l10n() {
	?>
	<script>
		pbtn = {
			dialogUrl : '<?php echo admin_url( 'admin-ajax.php?action=pbtn_dialog' ) ?>'
		};
	</script>
	<?php
}

add_action( 'wp_footer', 'pbtn_script', 16 );
function pbtn_script() {
	?>
	<style id="pbtn-styles">
		.pbtn.pbtn-left{float:left;}
		.pbtn.pbtn-center{}
		.pbtn.pbtn-right{float: right;}
	</style>
	<script>
		jQuery( function ($) {
			$( 'a.pbtn' ).hover(
				function() {
					var $t = $( this );
					if ( ! $t.data( 'hover-color' ) ) {
						$t.css( 'opacity', '0.7' );
						return;
					}
					var background = $t.css( 'background' );
					background = background ? background : $t.css( 'background-color' );
					$t.data( 'background', background );
					$t.css( 'background', $t.data( 'hover-color' ) );
				},
				function() {
					var $t = $( this );
					if ( ! $t.data( 'background' ) ) {
						$t.css( 'opacity', 1 );
						return;
					}
					$t.css( {
						'background' : $t.data( 'background' )
					} );
				}
			);
		} );
	</script>
	<?php
	wp_enqueue_style( 'dashicons' );
}

add_filter( 'mce_buttons', 'pbtn_register_tinymce_button' );
function pbtn_register_tinymce_button( $buttons ) {
	array_push( $buttons, 'pbtn_add_btn' );
	return $buttons;
}

add_filter( 'mce_external_plugins', 'pbtn_add_tinymce_button' );
function pbtn_add_tinymce_button( $plugin_array ) {
	$plugin_array['pbtn_script'] = plugins_url( '/tmce-plgn.js', __FILE__ ) ;
	return $plugin_array;
}

add_filter( 'wp_kses_allowed_html', 'pbtn_kses_allowed_html' );
function pbtn_kses_allowed_html( $tags ) {
	$tags['a']['data-bg-color'] = true;
	$tags['a']['data-bg-color2'] = true;
	$tags['a']['data-hover-color'] = true;

	return $tags;
}

add_filter( 'wp_ajax_pbtn_dialog', 'pbtn_ajax_dialog' );
function pbtn_ajax_dialog() {
	include 'assets/dialog.php';
}
