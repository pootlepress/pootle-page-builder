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
function pootlepb_wp_seo_filter( $content, $post ) {

	$id          = $post->ID;
	$panels_data = get_post_meta( $id, 'panels_data', true );
	if ( ! empty( $panels_data['widgets'] ) ) {
		foreach ( $panels_data['widgets'] as $widget ) {
			if ( ! empty( $widget['text'] ) ) {
				$content .= $widget['text'] . "\n\n";
			}
		}
	}

	return $content;
}

add_filter( 'wpseo_pre_analysis_post_content', 'pootlepb_wp_seo_filter', 10, 2 );

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
