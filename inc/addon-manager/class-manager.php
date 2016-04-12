<?php
/**
 * Contains Pootle_Page_Builder_Addon_Manager class
 * @author pootlepress
 * @since 0.3.2
 */

if ( ! class_exists( 'Pootle_Page_Builder_Addon_Manager' ) ) {
	/** class Pootle_Page_Builder_Addon_Manager_Menu */
	require plugin_dir_path( __FILE__ ) . 'inc/class-menu.php';
	/** Add on key manager functions */
	require plugin_dir_path( __FILE__ ) . 'inc/funcs.php';

	/**
	 * Class Pootle_Page_Builder_Addon_Manager
	 * Add-on key manager
	 * @uses Pootle_Page_Builder_Addon_Manager_Menu
	 */
	class Pootle_Page_Builder_Addon_Manager extends Pootle_Page_Builder_Addon_Manager_Menu {

		/**
		 * @var string Software product id
		 * @access private
		 */
		private $software_product_id;

		/**
		 * Used to send any extra information.
		 * @var mixed array, object, string, etc.
		 */
		public $extra;

		/**
		 * pootle page builder add-on key manager initiate
		 * @param string $token Add-on token
		 * @param string $name Add-on name
		 * @param string $version Add-on version
		 * @param string $file Add-on main file __FILE__
		 * @param null   $text_domain Add-on text_domain
		 * @param string $upgrade_url Add-on upgrade_url, default http://pootlepress.com/
		 */
		public function __construct( $token, $name, $version, $file, $text_domain = null, $upgrade_url = 'http://pootlepress.com/' ) {

			$this->upgrade_url = $upgrade_url;
			$this->version     = $version;
			$this->token       = $token;
			$this->name        = $name;
			$this->file        = $file;

			if ( $text_domain ) {
				$this->text_domain = $text_domain;
			} else {
				$this->text_domain = str_replace( '_', '-', $token );
			}

			/**
			 * Displays an inactive message if the API License Key has not yet been activated
			 */
			if ( get_option( $this->token . '_activated' ) != 'Activated' ) {
				add_action( 'admin_notices', array( $this, 'inactive_notice' ) );
			}

			// Run the activation function
			add_action( 'admin_init', array( $this, 'activation' ) );

			if ( is_admin() ) {

				// Check for external connection blocking
				add_action( 'admin_notices', array( $this, 'check_external_blocking' ) );

				/**
				 * Software Product ID is the product title string
				 * This value must be unique, and it must match the API tab for the product in WooCommerce
				 */
				$this->software_product_id = $this->name;

				/**
				 * Set all data defaults here
				 */
				$this->data_key                = $this->token;
				$this->instance_key            = $this->token . '_instance';
				$this->deactivate_checkbox_key = $this->token . '_deactivate_checkbox';
				$this->activated_key           = $this->token . '_activated';

				/**
				 * Set all admin menu data
				 */
				$this->deactivate_checkbox         = $this->token . '_deactivate_license_checkbox';
				$this->activation_tab_key          = $this->token . '_dashboard';
				$this->deactivation_tab_key        = $this->token . '_deactivation';
				$this->settings_menu_title         = $this->name;
				$this->settings_title              = $this->name;
				$this->menu_tab_activation_title   = __( 'Activation', $this->text_domain );
				$this->menu_tab_deactivation_title = __( 'Deactivation', $this->text_domain );

				/**
				 * Set all software update data here
				 */
				$this->options           = get_option( $this->data_key );
				$this->plugin_name       = untrailingslashit( plugin_basename( $this->file ) ); // same as plugin slug. if a theme use a theme name like 'twentyeleven'
				$this->product_id        = get_option( $this->token . '_product_id' ); // Software Title
				$this->renew_license_url = 'http://localhost/toddlahman/my-account'; // URL to renew a license. Trailing slash in the upgrade_url is required.
				$this->instance_id       = get_option( $this->instance_key ); // Instance ID (unique to each blog activation)
				/**
				 * Some web hosts have security policies that block the : (colon) and // (slashes) in http://,
				 * so only the host portion of the URL can be sent. For example the host portion might be
				 * www.example.com or example.com. http://www.example.com includes the scheme http,
				 * and the host www.example.com.
				 * Sending only the host also eliminates issues when a client site changes from http to https,
				 * but their activation still uses the original scheme.
				 * To send only the host, use a line like the one below:
				 *
				 * $this->domain = str_ireplace( array( 'http://', 'https://' ), '', home_url() ); // blog domain name
				 */
				$this->domain           = str_ireplace( array(
					'http://',
					'https://'
				), '', home_url() ); // blog domain name
				$this->software_version = $this->version; // The software version
				$this->plugin_or_theme  = 'plugin'; // 'theme' or 'plugin'

				// Performs activation and deactivation of API License Keys
				require( plugin_dir_path( __FILE__ ) . 'inc/class-key.php' );

				// Checks for software updates
				require( plugin_dir_path( __FILE__ ) . 'inc/class-plugin-update.php' );

				$options = get_option( $this->data_key );

				/**
				 * Check for software updates
				 */
				if ( ! empty( $options ) && $options !== false ) {

					$this->update_check(
						$this->upgrade_url,
						$this->plugin_name,
						$this->product_id,
						$this->options['api_key'],
						$this->options['activation_email'],
						$this->renew_license_url,
						$this->instance_id,
						$this->domain,
						$this->software_version,
						$this->plugin_or_theme,
						$this->text_domain
					);
				}

				$this->set_key();
			}

			//Setup menu
			$this->init_menu();

			/**
			 * Deletes all data if plugin deactivated
			 */
			register_deactivation_hook( $this->file, array( $this, 'uninstall' ) );

		}

		/** Load Shared Classes as on-demand Instances **********************************************/

		/**
		 * API Key Class.
		 *
		 * @return Pootle_Page_Builder_Addon_Manager_Update_Check
		 */
		public function set_key() {
			$this->key_class = new Pootle_Page_Builder_Addon_Manager_Key( $this->product_id, $this->instance_id, $this->software_version, $this->upgrade_url, $this->domain );
		}

		/**
		 * Returns update check class instance
		 * @param string $upgrade_url Add-on upgrade_url
		 * @param string $plugin_name Add-on name
		 * @param string $product_id Add-on product_id (name)
		 * @param string $api_key Add-on api key
		 * @param string $activation_email Add-on activation email
		 * @param string $renew_license_url Add-on renew license url
		 * @param string $instance Add-on instance
		 * @param string $domain Add-on domain
		 * @param string $software_version Add-on software version
		 * @param string $plugin_or_theme Add-on plugin or theme
		 * @param string $text_domain Add-on text domain
		 * @param string $extra Add-on extra
		 * @return Pootle_Page_Builder_Addon_Update_Check Instance
		 */
		public function update_check( $upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra = '' ) {

			return new Pootle_Page_Builder_Addon_Update_Check( $upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra );
		}

		/**
		 * Returns plugin url
		 * @return string
		 */
		public function plugin_url() {
			if ( empty( $this->plugin_url ) ) {
				$this->plugin_url = plugins_url( '/', $this->file );
			}

			return $this->plugin_url;
		}

		/**
		 * Generate the default data arrays
		 */
		public function activation() {

			if ( get_option( $this->token . '_product_id' ) ) { return; }

			$global_options = array(
				'api_key'          => '',
				'activation_email' => '',
			);

			update_option( $this->data_key, $global_options );

			// Generate a unique installation $instance id
			$instance = $this->generate_password();

			$single_options = array(
				$this->token . '_product_id'   => $this->software_product_id,
				$this->instance_key            => $instance,
				$this->deactivate_checkbox_key => 'on',
				$this->activated_key           => 'Deactivated',
			);

			foreach ( $single_options as $key => $value ) {
				update_option( $key, $value );
			}

			$curr_ver = get_option( 'pootle_' . $this->token . '_version' );

			// checks if the current plugin version is lower than the version being installed
			if ( version_compare( $this->version, $curr_ver, '>' ) ) {
				// update the version
				update_option( 'pootle_' . $this->token . '_version', $this->version );
			}

		}

		/**
		 * Deletes all data if plugin deactivated
		 * @return void
		 */
		public function uninstall() {
			global $wpdb, $blog_id;

			$this->license_key_deactivation();

			// Remove options
			if ( is_multisite() ) {

				switch_to_blog( $blog_id );

				$this->remove_options();

				restore_current_blog();

			} else {

				$this->remove_options();

			}
		}

		/**
		 * Removes add-on key options
		 */
		private function remove_options() {

			foreach (
				array(
					$this->data_key,
					$this->token . '_product_id',
					$this->instance_key,
					$this->deactivate_checkbox_key,
					$this->activated_key,
				) as $option
			) {

				delete_option( $option );
			}
		}

		/**
		 * Deactivates the license on the API server
		 * @return void
		 */
		public function license_key_deactivation() {

			$activation_status = get_option( $this->activated_key );

			$api_email = $this->options['activation_email'];
			$api_key   = $this->options['api_key'];

			$args = array(
				'email'       => $api_email,
				'licence_key' => $api_key,
			);

			if ( $activation_status == 'Activated' && $api_key != '' && $api_email != '' ) {
				$this->key_class->deactivate( $args ); // reset license key activation
			}
		}

		/**
		 * Displays an inactive notice when the software is inactive.
		 */
		public function inactive_notice() { ?>
			<?php if ( ! current_user_can( 'manage_options' ) ) {
				return;
			} ?>
			<?php if ( ! in_array( filter_input( INPUT_GET, 'page' ), array( 'page_builder_settings', 'page_builder_addons', ) ) ) {
				return;
			} ?>
			<div id="message" class="notice notice-warning is-dismissible">
				<p><?php printf( __( 'Your ' . $this->name . ' license is not active. %sClick here%s to activate the license key.<br>Don\'t worry if the licence doesn\'t activate, your software will still work, this is just to receive automatic updates.', $this->text_domain ), '<a href="' . esc_url( admin_url( 'admin.php?page=page_builder_settings&tab=addons&addon=' . $this->token ) ) . '">', '</a>' ); ?></p>
			</div>
		<?php
		}

		/**
		 * Check for external blocking contstant
		 * @return string
		 */
		public function check_external_blocking() {
			// show notice if external requests are blocked through the WP_HTTP_BLOCK_EXTERNAL constant
			if ( defined( 'WP_HTTP_BLOCK_EXTERNAL' ) && WP_HTTP_BLOCK_EXTERNAL === true ) {

				// check if our API endpoint is in the allowed hosts
				$host = parse_url( $this->upgrade_url, PHP_URL_HOST );

				if ( ! defined( 'WP_ACCESSIBLE_HOSTS' ) || stristr( WP_ACCESSIBLE_HOSTS, $host ) === false ) {
					?>
					<div class="error">
						<p><?php printf( __( '<b>Warning!</b> You\'re blocking external requests which means you won\'t be able to get %s updates. Please add %s to %s.', $this->text_domain ), $this->software_product_id, '<strong>' . $host . '</strong>', '<code>WP_ACCESSIBLE_HOSTS</code>' ); ?></p>
					</div>
				<?php
				}

			}
		}

		/**
		 * Creates a random instance ID
		 * @param int $length
		 * @return string Random alphanumeric string
		 */
		public function generate_password( $length = 12 ) {
			$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

			$password = '';
			for ( $i = 0; $i < $length; $i ++ ) {
				$password .= substr( $chars, rand( 0, strlen( $chars ) - 1 ), 1 );
			}

			return $password;
		}

	} // End of class
}