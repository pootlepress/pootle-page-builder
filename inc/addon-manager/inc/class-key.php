<?php
/**
 * Contains Pootle_Page_Builder_Addon_Manager_Key class
 * @author pootlepress
 * @since 0.3.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Pootle_Page_Builder_Addon_Manager_Key' ) ) {
	/**
	 * Class Pootle_Page_Builder_Addon_Manager_Key
	 * Makes requests to update server for activation, deactivation and update check
	 * @usedby Pootle_Page_Builder_Addon_Manager
	 * @package Update API Manager/Admin
	 */
	class Pootle_Page_Builder_Addon_Manager_Key {

		/**
		 * Sets required properties for making requests to the server
		 * @param string $product_id Add-on product id
		 * @param string $instance_id Add-on instance id
		 * @param string $software_version Add-on software version
		 * @param string $upgrade_url Add-on upgrade url
		 * @param string $domain Add-on domain
		 */
		public function __construct( $product_id, $instance_id, $software_version, $upgrade_url, $domain ) {
			$this->product_id       = $product_id;
			$this->instance_id      = $instance_id;
			$this->software_version = $software_version;
			$this->upgrade_url      = $upgrade_url;
			$this->domain           = $domain;
		}

		/**
		 * API Key URL
		 * @param mixed $args Url query args
		 * @return string API key url
		 */
		public function create_software_api_url( $args ) {

			$api_url = add_query_arg( 'wc-api', 'am-software-api', $this->upgrade_url );

			return $api_url . '&' . http_build_query( $args );
		}

		/**
		 * Checks if the software is activated or deactivated
		 * @param  array $args
		 * @uses Pootle_Page_Builder_Addon_Manager_Key::request
		 * @return bool|array
		 */
		public function activate( $args ) {

			return $this->request( 'activation', $args );
		}

		/**
		 * Checks if the software is activated or deactivated
		 * @param  array $args
		 * @uses Pootle_Page_Builder_Addon_Manager_Key::request
		 * @return bool|array
		 */
		public function deactivate( $args ) {

			return $this->request( 'deactivation', $args );
		}

		/**
		 * Checks if the software is activated or deactivated
		 * @param  array $args
		 * @uses Pootle_Page_Builder_Addon_Manager_Key::request
		 * @return bool|array
		 */
		public function status( $args ) {

			return $this->request( 'status', $args );
		}

		/**
		 * Makes the actual request to the server
		 * @param string $request Request to make, status|activation|deactivation
		 * @param array $args Request arguments
		 * @return bool|string Response from server
		 */
		private function request( $request, $args ) {

			$defaults = array(
				'request'          => $request,
				'product_id'       => $this->product_id,
				'instance'         => $this->instance_id,
				'platform'         => $this->domain,
				'software_version' => $this->software_version,
			);

			$args = wp_parse_args( $defaults, $args );

			$target_url = esc_url_raw( $this->create_software_api_url( $args ) );

			$request = wp_remote_get( $target_url );

			if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
				// Request failed
				return false;
			}

			$response = wp_remote_retrieve_body( $request );

			return $response;
		}

	}
}// Class is instantiated as an object by other classes on-demand
