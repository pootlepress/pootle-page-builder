<?php
/**
 * Contains Pootle_Page_Builder_Rest_API class
 * @author pootlepress
 * @since 0.1.0
 */

/**
 * Class Pootle_Page_Builder_Rest_API
 * Register REST API endpoints
 */
final class Pootle_Page_Builder_Rest_API {

	function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	function register_routes() {
		register_rest_route( 'ppb/v1', '/pages', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_pages' ),
		) );
	}

	function get_pages() {
		global $Pootle_Page_Builder;

		$query = $Pootle_Page_Builder->ppb_posts();

		$json  = array(
			'site_url' => site_url(),
			'posts'    => array(),
		);

		foreach ( $query->posts as $post ) {
			$json['posts'][] = array(
				'title'  => $post->post_title,
				'link'   => get_permalink( $post ),
				'type'   => $post->post_type,
				'status' => $post->post_status,
			);
		}
		return $json;
	}
}

$GLOBALS['Pootle_Page_Builder_Rest_API'] = new Pootle_Page_Builder_Rest_API();