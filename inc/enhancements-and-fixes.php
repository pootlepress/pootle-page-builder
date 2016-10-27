<?php
/**
 * Filters and actions for enhancements and bug fixes
 * @author pootlepress
 * @since 0.1.0
 */

/**
 * Makes page builder content visible to WP SEO
 *
 * @param string $content Post content
 * @param object $post Post object
 *
 * @return string Post content
 * @since 0.1.0
 */
function pootlepb_text_content( $content, $post = null ) {
	if ( empty( $post ) ) global $post;

	if ( empty( $post->ID ) ) return $content;

	if( 'content' == get_option( 'pootlepb-content-deactivation' ) ) {
		return pootlepb_get_text_content( $post->ID );
	} else {
		return Pootle_Page_Builder_Render_Layout::render( $post->ID );
	}
}

add_filter( 'content_save_pre', 'pootlepb_text_content', 10, 2 );

/**
 * Makes page builder content visible to WP SEO
 *
 * @param integer $post Post ID
 *
 * @return string Post content
 * @since 0.1.0
 */
function pootlepb_get_text_content( $post ) {

	$content = '';
	$panels_data = get_post_meta( $post, 'panels_data', true );
	if ( ! empty( $panels_data['widgets'] ) ) {
		foreach ( $panels_data['widgets'] as $widget ) {
			if ( ! empty( $widget['text'] ) ) {
				$content .= $widget['text'] . "\n\n";
			}
		}
	}

	return $content;
}

/**
 * No admin notices on our settings page
 *
 * @since 0.1.0
 */
function pootlepb_no_admin_notices() {
	global $pagenow;

	if ( 'options-general.php' == $pagenow && 'page_builder' == filter_input( INPUT_GET, 'page' ) ) {
		remove_all_actions( 'admin_notices' );
	}
}

add_action( 'admin_notices', 'pootlepb_no_admin_notices', 0 );


/**
 * Add a filter to import panels_data meta key. This fixes serialized PHP.
 * @param array $post_meta Post meta data
 * @return array
 * @filter wp_import_post_meta
 * @since 0.1.0
 */
function pootlepb_wp_import_post_meta( $post_meta ) {
	foreach ( $post_meta as $i => $meta ) {
		if ( 'panels_data' == $meta['key'] ) {
			$value = $meta['value'];
			$value = preg_replace( "/[\r\n]/", '<<<br>>>', $value );
			$value = preg_replace( '!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $value );
			$value = unserialize( $value );
			$value = array_map( 'pootlepb_wp_import_post_meta_map', $value );

			$post_meta[ $i ]['value'] = $value;
		}
	}

	return $post_meta;
}

add_filter( 'wp_import_post_meta', 'pootlepb_wp_import_post_meta' );

// Create a helper function for easy SDK access.
function ppb_fs() {
	global $ppb_fs;

	if ( ! isset( $ppb_fs ) ) {
		// Include Freemius SDK.
		require_once POOTLEPB_DIR . '/wp-sdk/start.php';

		$ppb_fs = fs_dynamic_init( array(
			'id'             => '269',
			'slug'           => 'pootle-page-builder',
			'public_key'     => 'pk_cb4e7b7932169240ac86c3fb01dd5',
			'is_premium'     => true,
			'has_paid_plans' => true,
			'hide_trial'     => true,
			'menu'           => array(
				'slug'       => 'page_builder',
				'first-path' => 'admin.php?page=page_builder',
			),
			// Set the SDK to work in a sandbox mode (for development & testing).
			// IMPORTANT: MAKE SURE TO REMOVE SECRET KEY BEFORE DEPLOYMENT.
			'secret_key'     => 'sk_Ps6;5x%cnDM:eoqhd2~<*vvr[}OoZ',
		) );
	}

	return $ppb_fs;
}
