<?php
/**
 * Contains Pootle_Page_Builder_Addon_Update_Check class
 * @author pootlepress
 * @since 0.3.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Pootle_Page_Builder_Addon_Update_Check' ) ) {
	/**
	 * Class Pootle_Page_Builder_Addon_Update_Check
	 * Checks Pooltepress add-on updates
	 * @usedby Pootle_Page_Builder_Addon_Manager
	 * @package Update API Manager/Admin
	 */
	class Pootle_Page_Builder_Addon_Update_Check {


		/** @var string URL to access the Update API Manager. */
		private $upgrade_url;

		/** @var string Add-on name */
		private $plugin_name;

		/** @var string Software Title */
		private $product_id;

		/** @var string API License Key */
		private $api_key;

		/** @var string License Email */
		private $activation_email;

		/** @var string URL to renew a license */
		private $renew_license_url;

		/** @var string Instance ID (unique to each blog activation) */
		private $instance;

		/** @var string blog domain name */
		private $domain;

		/** @var string Add-on version */
		private $software_version;

		/** @var string 'theme' or 'plugin' */
		private $plugin_or_theme;

		/** @var string localization for translation */
		private $text_domain;

		/** @var string Used to send any extra information. */
		private $extra;

		/**
		 * Constructor.
		 * @access public
		 * @param string $upgrade_url Add-on upgrade url
		 * @param string $plugin_name Add-on plugin name
		 * @param string $product_id Add-on product id
		 * @param string $api_key Add-on api key
		 * @param string $activation_email Add-on user activation email
		 * @param string $renew_license_url Add-on renew license url
		 * @param string $instance WP installation instance id
		 * @param string $domain wp blog url
		 * @param string $software_version Add-on version
		 * @param string $plugin_or_theme set to plugin
		 * @param string $text_domain Add-on text_domain
		 * @param string $extra any extra information
		 */
		public function __construct( $upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra ) {
			// API data
			$this->upgrade_url       = $upgrade_url;
			$this->plugin_name       = $plugin_name;
			$this->product_id        = $product_id;
			$this->api_key           = $api_key;
			$this->activation_email  = $activation_email;
			$this->renew_license_url = $renew_license_url;
			$this->instance          = $instance;
			$this->domain            = $domain;
			$this->software_version  = $software_version;
			$this->text_domain       = $text_domain;
			$this->extra             = $extra;

			// Slug should be the same as the plugin/theme directory name
			if ( strpos( $this->plugin_name, '.php' ) !== 0 ) {
				$this->slug = dirname( $this->plugin_name );
			} else {
				$this->slug = $this->plugin_name;
			}

			/**
			 * Flag for plugin or theme updates
			 * @access public
			 * @since  1.0.0
			 *
			 * @param string , plugin or theme
			 */
			$this->plugin_or_theme = $plugin_or_theme; // 'theme' or 'plugin'

			/*********************************************************************
			 * The plugin and theme filters should not be active at the same time
			 *********************************************************************/

			/**
			 * More info:
			 * function set_site_transient moved from wp-includes/functions.php
			 * to wp-includes/option.php in WordPress 3.4
			 *
			 * set_site_transient() contains the pre_set_site_transient_{$transient} filter
			 * {$transient} is either update_plugins or update_themes
			 *
			 * Transient data for plugins and themes exist in the Options table:
			 * _site_transient_update_themes
			 * _site_transient_update_plugins
			 */

			// Check For Plugin Updates
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_check' ) );

			// Check For Plugin Information to display on the update details page
			add_filter( 'plugins_api', array( $this, 'request' ), 10, 3 );
		}

		/**
		 * Upgrade API URL
		 * @param mixed $args Upgrade url args
		 * @return string Upgrade url
		 */
		private function create_upgrade_api_url( $args ) {
			$upgrade_url = add_query_arg( 'wc-api', 'upgrade-api', $this->upgrade_url );

			return $upgrade_url . '&' . http_build_query( $args );
		}

		/**
		 * Check for updates against the remote server.
		 * @access public
		 * @since  1.0.0
		 * @param  object $transient
		 * @return object $transient
		 */
		public function update_check( $transient ) {

			if ( empty( $transient->checked ) ) {
				return $transient;
			}

			$args = array(
				'request'          => 'pluginupdatecheck',
				'slug'             => $this->slug,
				'plugin_name'      => $this->plugin_name,
				'version'          => $this->software_version,
				'product_id'       => $this->product_id,
				'api_key'          => $this->api_key,
				'activation_email' => $this->activation_email,
				'instance'         => $this->instance,
				'domain'           => $this->domain,
				'software_version' => $this->software_version,
				'extra'            => $this->extra,
			);

			// Check for a plugin update
			$response = $this->plugin_information( $args );

			// Displays an admin error message in the WordPress dashboard
			ppb_am_check_response_for_errors( $response, $this );

			// Set version variables
			if ( isset( $response ) && is_object( $response ) && $response !== false ) {
				// New plugin version from the API
				$new_ver = $response->new_version;
				// Current installed plugin version
				$curr_ver = $this->software_version;

				if ( version_compare( $new_ver, $curr_ver, '>' ) ) {

					$transient->response[ $this->plugin_name ]              = $response;
					$transient->response[ $this->plugin_name ]->slug        = $this->slug;
					$transient->response[ $this->plugin_name ]->plugin_name = $this->plugin_name;
				}
			}

			return $transient;
		}

		/**
		 * Sends and receives data to and from the server API
		 * @param mixed $args Upgrade url args
		 * @return object $response
		 */
		public function plugin_information( $args ) {

			$target_url = esc_url_raw( $this->create_upgrade_api_url( $args ) );

			$request = wp_remote_get( $target_url );

			//$request = wp_remote_post( $this->upgrade_url . 'wc-api/upgrade-api/', array( 'body' => $args ) );

			if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
				return false;
			}

			$response = unserialize( wp_remote_retrieve_body( $request ) );

			/**
			 * For debugging errors from the API
			 * For errors like: unserialize(): Error at offset 0 of 170 bytes
			 * Comment out $response above first
			 */
			// $response = wp_remote_retrieve_body( $request );
			// print_r($response); exit;


			if ( is_object( $response ) ) {
				return $response;
			} else {
				return false;
			}
		}

		/**
		 * Generic request helper.
		 * @access public
		 * @param bool $false
		 * @param string $action
		 * @param  array $args
		 * @return object $response or boolean false
		 */
		public function request( $false, $action, $args ) {

			// Check if this plugins API is about this plugin
			if ( isset( $args->slug ) ) {
				if ( ! in_array( $args->slug, array( $this->slug, $this->plugin_name ) ) ) {
					return $false;
				}
			} else {
				return $false;
			}

			$args = array(
				'request'          => 'plugininformation',
				'plugin_name'      => $this->plugin_name,
				'version'          => $this->software_version,
				'product_id'       => $this->product_id,
				'api_key'          => $this->api_key,
				'activation_email' => $this->activation_email,
				'instance'         => $this->instance,
				'domain'           => $this->domain,
				'software_version' => $this->software_version,
				'extra'            => $this->extra,
			);

			$response = $this->plugin_information( $args );

			// If everything is okay return the $response
			if ( isset( $response ) && is_object( $response ) && $response !== false ) {
				$response->slug = $this->slug;

				return $response;
			}

		}

		/**
		 * Display license expired error notice
		 * @param  string $message
		 */
		public function expired_license_error_notice( $message ) {

			echo sprintf( '<div id="message" class="error"><p>' . __( 'The license key for %s has expired. You can reactivate or purchase a license key from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', $this->product_id, $this->renew_license_url );

		}

		/**
		 * Display subscription on-hold error notice
		 * @param  string $message
		 * @return void
		 */
		public function on_hold_subscription_error_notice( $message ) {

			echo sprintf( '<div id="message" class="error"><p>' . __( 'The subscription for %s is on-hold. You can reactivate the subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', $this->product_id, $this->renew_license_url );

		}

		/**
		 * Display subscription cancelled error notice
		 * @param  string $message
		 * @return void
		 */
		public function cancelled_subscription_error_notice( $message ) {

			echo sprintf( '<div id="message" class="error"><p>' . __( 'The subscription for %s has been cancelled. You can renew the subscription from your account <a href="%s" target="_blank">dashboard</a>. A new license key will be emailed to you after your order has been completed.', $this->text_domain ) . '</p></div>', $this->product_id, $this->renew_license_url );

		}

		/**
		 * Display subscription expired error notice
		 * @param  string $message
		 * @return void
		 */
		public function expired_subscription_error_notice( $message ) {

			echo sprintf( '<div id="message" class="error"><p>' . __( 'The subscription for %s has expired. You can reactivate the subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', $this->product_id, $this->renew_license_url );

		}

		/**
		 * Display subscription expired error notice
		 * @param  string $message
		 * @return void
		 */
		public function suspended_subscription_error_notice( $message ) {

			echo sprintf( '<div id="message" class="error"><p>' . __( 'The subscription for %s has been suspended. You can reactivate the subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', $this->product_id, $this->renew_license_url );

		}

		/**
		 * Display subscription expired error notice
		 * @param  string $message
		 * @return void
		 */
		public function pending_subscription_error_notice( $message ) {

			echo sprintf( '<div id="message" class="error"><p>' . __( 'The subscription for %s is still pending. You can check on the status of the subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', $this->product_id, $this->renew_license_url );

		}

		/**
		 * Display subscription expired error notice
		 * @param  string $message
		 * @return void
		 */
		public function trash_subscription_error_notice( $message ) {

			echo sprintf( '<div id="message" class="error"><p>' . __( 'The subscription for %s has been placed in the trash and will be deleted soon. You can purchase a new subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', $this->product_id, $this->renew_license_url );

		}

		/**
		 * Display subscription expired error notice
		 * @param  string $message
		 * @return void
		 */
		public function no_subscription_error_notice( $message ) {

			echo sprintf( '<div id="message" class="error"><p>' . __( 'A subscription for %s could not be found. You can purchase a subscription from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', $this->product_id, $this->renew_license_url );

		}

		/**
		 * Display missing key error notice
		 * @param  string $message
		 * @return void
		 */
		public function no_key_error_notice( $message ) {

			echo sprintf( '<div id="message" class="error"><p>' . __( 'A license key for %s could not be found. Maybe you forgot to enter a license key when setting up %s, or the key was deactivated in your account. You can reactivate or purchase a license key from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', $this->product_id, $this->product_id, $this->renew_license_url );

		}

		/**
		 * Display missing download permission revoked error notice
		 * @param  string $message
		 * @return void
		 */
		public function download_revoked_error_notice( $message ) {

			echo sprintf( '<div id="message" class="error"><p>' . __( 'Download permission for %s has been revoked possibly due to a license key or subscription expiring. You can reactivate or purchase a license key from your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', $this->product_id, $this->renew_license_url );

		}

		/**
		 * Display no activation error notice
		 * @param  string $message
		 * @return void
		 */
		public function no_activation_error_notice( $message ) {

			echo sprintf( '<div id="message" class="error"><p>' . __( '%s has not been activated. Go to the settings page and enter the license key and license email to activate %s.', $this->text_domain ) . '</p></div>', $this->product_id, $this->product_id );

		}

		/**
		 * Display switched activation error notice
		 * @param  string $message
		 * @return void
		 */
		public function switched_subscription_error_notice( $message ) {

			echo sprintf( '<div id="message" class="error"><p>' . __( 'You changed the subscription for %s, so you will need to enter your new API License Key in the settings page. The License Key should have arrived in your email inbox, if not you can get it by logging into your account <a href="%s" target="_blank">dashboard</a>.', $this->text_domain ) . '</p></div>', $this->product_id, $this->renew_license_url );

		}

	} // End of class
}
