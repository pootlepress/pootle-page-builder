<?php

/**
 * WooCommerce API Manager API Key Class
 *
 * @package Update API Manager/Key Handler
 * @author Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @since 1.3
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pootle_Page_Builder_Addon_Manager_Key {

	public function __construct( $product_id, $instance_id, $software_version, $upgrade_url, $domain ) {
		$this->product_id = $product_id;
		$this->instance_id = $instance_id;
		$this->software_version = $software_version;
		$this->upgrade_url = $upgrade_url;
		$this->domain = $domain;
	}

	// API Key URL
	public function create_software_api_url( $args ) {

		$api_url = add_query_arg( 'wc-api', 'am-software-api', $this->upgrade_url );

		return $api_url . '&' . http_build_query( $args );
	}

	/**
	 * Checks if the software is activated or deactivated
	 * @param  array $args
	 * @return bool|array
	 */
	public function activate( $args ) {

		return $this->request( 'activation', $args );
	}

	/**
	 * Checks if the software is activated or deactivated
	 * @param  array $args
	 * @return bool|array
	 */
	public function deactivate( $args ) {

		return $this->request( 'deactivation', $args );
	}

	/**
	 * Checks if the software is activated or deactivated
	 * @param  array $args
	 * @return bool|array
	 */
	public function status( $args ) {

		return $this->request( 'status', $args );
	}

	private function request( $request, $args ) {

		$defaults = array(
			'request' => $request,
			'product_id' => $this->product_id,
			'instance' => $this->instance_id,
			'platform' => $this->domain,
			'software_version' => $this->software_version,
		);

		$args = wp_parse_args( $defaults, $args );

		$target_url = esc_url_raw( $this->create_software_api_url( $args ) );

		$request = wp_remote_get( $target_url );

		if( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			// Request failed
			return false;
		}

		$response = wp_remote_retrieve_body( $request );

		return $response;
	}

}

// Class is instantiated as an object by other classes on-demand
