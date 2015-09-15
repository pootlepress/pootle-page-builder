<?php
/**
 * Handles live preview
 * @author pootlepress
 * @since 0.1.0
 */

/**
 * Handles creating the preview.
 * @since 0.1.0
 */
function pootlepb_preview() {
	if ( null !== filter_input( INPUT_GET, 'pootlepb_preview' ) && wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce' ), 'ppb-panels-preview' ) ) {
		global $pootlepb_is_preview;
		$pootlepb_is_preview = true;
		// Set the panels home state to true
		$post_id = filter_input( INPUT_POST, 'post_id' );
		if ( empty( $post_id ) ) {
			$GLOBALS['pootlepb_is_panels_home'] = true;
		}
		locate_template( pootlepb_settings( 'home-template' ), true );
		exit();
	}
}

add_action( 'template_redirect', 'pootlepb_preview' );

/**
 * Is this a preview.
 *
 * @return bool
 * @since 0.1.0
 */
function pootlepb_is_preview() {
	global $pootlepb_is_preview;

	return (bool) $pootlepb_is_preview;
}

/**
 * This is a way to show previews of panels, especially for the home page.
 *
 * @param $val
 *
 * @return array
 * @since 0.1.0
 */
function pootlepb_preview_load_data( $val ) {
	if ( isset( $_GET['pootlepb_preview'] ) ) {

		$val = pootlepb_get_panels_data_from_post();
	}

	return $val;
}
