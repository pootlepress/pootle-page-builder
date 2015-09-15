<?php
/**
 * Contains Pootle_Page_Builder_Addon_Manager_Menu class
 * @author pootlepress
 * @since 0.3.2
 */


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Pootle_Page_Builder_Addon_Manager_Menu' ) ) {
	/**
	 * Class Pootle_Page_Builder_Addon_Manager_Menu
	 * Pooltepress add-on key management settings menu tabs
	 * @usedby Pootle_Page_Builder_Addon_Manager
	 * @package Update API Manager/Admin
	 */
	class Pootle_Page_Builder_Addon_Manager_Menu {

		/** @var string Plugin token */
		public $data_key;

		/** @var string This wp installation instance key */
		public $instance_key;

		/** @var string Deactivate checkbox options key */
		public $deactivate_checkbox_key;

		/** @var string Settings activate checkbox key */
		public $activated_key;

		/** @var string Settings deactivate checkbox key */
		public $deactivate_checkbox;

		/** @var string Activation tab key */
		public $activation_tab_key;

		/** @var string Activation tab key */
		public $deactivation_tab_key;

		/** @var string Add-on key manager tab title */
		public $settings_menu_title;

		/** @var string Add-on key manager section title */
		public $settings_title;

		/** @var string Add-on key manager activation tab title */
		public $menu_tab_activation_title;

		/** @var string Add-on key manager tab deactivation title */
		public $menu_tab_deactivation_title;

		/** @var string Base URL to the remote upgrade API Manager server */
		public $upgrade_url;

		/** @var string Version */
		public $version;

		/** @var string Token for this plugin */
		public $token;

		/** @var string Plugin name */
		public $name;

		/** @var string Plugin name */
		public $file;

		/** @var string Plugin textdomain */
		public $text_domain;

		/** @var string Current location url */
		public $plugin_url;

		/** @var Pootle_Page_Builder_Addon_Manager_Key Instance */
		protected $key_class;

		/** @var array Add-on key options */
		public $options;

		/** @var array Add-on name */
		public $plugin_name;

		/** @var string Product id ( name, should precisely match on server) */
		public $product_id;

		/** @var string Renew license url */
		public $renew_license_url;

		/** @var string WP installation id */
		public $instance_id;

		/** @var string Add-on Text domain */
		public $domain;

		/** @var string Add-on current version */
		public $software_version;

		/** @var string Set to plugin */
		public $plugin_or_theme;

		/** @var string Add-on version on server */
		public $update_version;

		/**
		 * Load admin menu
		 */
		public function init_menu() {

			add_action( 'pootle_pb_addon_key_tabs', array( $this, 'add_menu' ) );
			add_action( 'pootle_pb_addon_key_' . $this->token . '_tab', array( $this, 'menu_config_page' ) );
			add_action( 'admin_init', array( $this, 'menu_load_settings' ) );
		}

		/**
		 * Add option page menu
		 * @param array $tabs Tabs to render
		 * @return mixed
		 */
		public function add_menu( $tabs ) {


			$page = add_options_page(
				__( $this->settings_menu_title, $this->text_domain ),
				__( $this->settings_menu_title, $this->text_domain ),
				'manage_options',
				$this->activation_tab_key,
				array( $this, 'menu_config_page' )
			);
			add_action( 'admin_print_styles-' . $page, array( $this, 'menu_css_scripts' ) );

			$tabs[ $this->token ] = $this->name;

			return $tabs;
		}

		/**
		 * Render option page
		 * @param string $url_base
		 */
		public function menu_config_page( $url_base ) {

			$settings_tabs = array(
				$this->activation_tab_key   => __( $this->menu_tab_activation_title, $this->text_domain ),
				$this->deactivation_tab_key => __( $this->menu_tab_deactivation_title, $this->text_domain )
			);
			$current_tab   = isset( $_GET['section'] ) ? $_GET['section'] : $this->activation_tab_key;
			?>
			<div class='wrap' style="max-width:700px;">
				<ul class="subsubsub">
					<?php
					foreach ( $settings_tabs as $tab_page => $tab_name ) {
						$active_tab = $current_tab == $tab_page ? 'nav-tab-active' : '';
						echo '<li><a class=" ' . $active_tab . '" href="' . $url_base . '&section=' . $tab_page . '">' . $tab_name . '</a></li>';
					}
					?>
				</ul>

				<div class="main">
					<form action='options.php' method='post'>
						<?php
						if ( $current_tab == $this->activation_tab_key ) {
							settings_fields( $this->data_key );
							do_settings_sections( $this->activation_tab_key );
							submit_button( __( 'Save Changes', $this->text_domain ) );
						} else {
							settings_fields( $this->deactivate_checkbox );
							do_settings_sections( $this->deactivation_tab_key );
							submit_button( __( 'Save Changes', $this->text_domain ) );
						}
						?>
					</form>
				</div>
			</div>
		<?php
		}

		/**
		 * Register settings
		 */
		public function menu_load_settings() {

			register_setting( $this->data_key, $this->data_key, array( $this, 'menu_validate_options' ) );

			// API Key
			add_settings_section( 'api_key', __( 'API License Activation', $this->text_domain ), '__return_false', $this->activation_tab_key );
			add_settings_field( 'status', __( 'API License Key Status', $this->text_domain ), array(
				$this,
				'menu_wc_am_api_key_status'
			), $this->activation_tab_key, 'api_key' );
			add_settings_field( 'api_key', __( 'API License Key', $this->text_domain ), array(
				$this,
				'menu_wc_am_api_key_field'
			), $this->activation_tab_key, 'api_key' );
			add_settings_field( 'activation_email', __( 'API License email', $this->text_domain ), array(
				$this,
				'menu_wc_am_api_email_field'
			), $this->activation_tab_key, 'api_key' );

			// Activation settings
			register_setting( $this->deactivate_checkbox, $this->deactivate_checkbox, array(
				$this,
				'menu_wc_am_license_key_deactivation'
			) );
			add_settings_section( 'deactivate_button', __( 'API License Deactivation', $this->text_domain ), '__return_false', $this->deactivation_tab_key );
			add_settings_field( 'deactivate_button', __( 'Deactivate API License Key', $this->text_domain ), array(
				$this,
				'menu_wc_am_deactivate_textarea'
			), $this->deactivation_tab_key, 'deactivate_button' );

		}

		/**
		 * Returns the API License Key status from the WooCommerce API Manager on the server
		 */
		public function menu_wc_am_api_key_status() {
			$license_status       = $this->menu_license_key_status();
			$license_status_check = ( ! empty( $license_status['status_check'] ) && $license_status['status_check'] == 'active' ) ? 'Activated' : 'Deactivated';
			if ( ! empty( $license_status_check ) ) {
				echo $license_status_check;
			}
		}

		/**
		 * Returns API License text field
		 */
		public function menu_wc_am_api_key_field() {

			$this->menu_wc_am_api_field_render( 'api_key' );

		}

		/**
		 * Returns API License email text field
		 */
		public function menu_wc_am_api_email_field() {

			$this->menu_wc_am_api_field_render( 'activation_email' );

		}

		/**
		 * Renders settings input fields
		 * @param string $key The key for the field
		 */
		private function menu_wc_am_api_field_render( $key ) {

			//Outputting the field
			echo "<input id='$key' name='" . $this->data_key . "[$key]' size='25' type='text' value='" . $this->options[ $key ] . "' />";

			//Adding icon
			if ( $this->options[ $key ] ) {

				echo "<span class='icon-pos'><img src='" . plugin_dir_url( __FILE__ ) . "../assets/images/complete.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";

			} else {

				echo "<span class='icon-pos'><img src='" . plugin_dir_url( __FILE__ ) . "../assets/images/warn.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";

			}
		}

		/**
		 * Sanitizes and validates all input and output for Dashboard
		 * @param array $input Input from settings form
		 * @return array
		 */
		public function menu_validate_options( $input ) {

			// Load existing options, validate, and update with changes from input before returning
			$options = $this->options;

			$options[ 'api_key' ]          = trim( $input[ 'api_key' ] );
			$options[ 'activation_email' ] = trim( $input[ 'activation_email' ] );

			$current_api_key = $this->options['api_key'];

			// Should match the settings_fields() value
			if ( $_REQUEST['option_page'] != $this->deactivate_checkbox ) {

				//If this is a new key, and an existing key already exists in the database,
				//deactivate the existing key before activating the new key.
				if ( $current_api_key != $options[ 'api_key' ] ) {
					$this->menu_replace_license_key( $current_api_key );
				}

				$args = array(
					'email'       => $options[ 'activation_email' ],
					'licence_key' => $options[ 'api_key' ],
				);

				$activate_results = json_decode( $this->key_class->activate( $args ), true );

				if ( $activate_results['activated'] === true ) {
					add_settings_error( 'activate_text', 'activate_msg', __( 'Plugin activated. ', $this->text_domain ) . "{$activate_results['message']}.", 'updated' );
					update_option( $this->activated_key, 'Activated' );
					update_option( $this->deactivate_checkbox, 'off' );
				}

				if ( $activate_results == false ) {
					add_settings_error( 'api_key_check_text', 'api_key_check_error', __( 'Connection failed to the License Key API server. Try again later.', $this->text_domain ), 'error' );
					$options['api_key']                 = '';
					$options[ 'activation_email' ] = '';
					update_option( $this->options[ $this->activated_key ], 'Deactivated' );
				}

				//Test the results for error
				$this->check_error( $activate_results );

			}

			return $options;
		}

		/**
		 * Checks for error in response from server
		 * @param array $activate_results
		 */
		private function check_error( $activate_results ) {

			if ( ! empty( $activate_results['code'] ) ) {

				//Get error info and set error
				$error_info = ppb_am_error_info( $activate_results['code'] );
				add_settings_error( $error_info[0], $error_info[1], "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );

				//Get the options empty
				$options[ 'activation_email' ] = '';
				$options['api_key'] = '';

				//Set activation status Deactivated
				update_option( $this->options[ $this->activated_key ], 'Deactivated' );
			}
		}

		/**
		 * Returns the API License Key status from the WooCommerce API Manager on the server
		 */
		public function menu_license_key_status() {
			$activation_status = get_option( $this->activated_key );

			$args = array(
				'email'       => $this->options[ 'activation_email' ],
				'licence_key' => $this->options['api_key'],
			);

			return json_decode( $this->key_class->status( $args ), true );
		}

		/**
		 * Deactivate the current license key before activating the new license key
		 * @param string $current_api_key new api key to activate
		 * @return bool|void
		 */
		public function menu_replace_license_key( $current_api_key ) {

			$args = array(
				'email'       => $this->options[ 'activation_email' ],
				'licence_key' => $current_api_key,
			);

			$reset = $this->key_class->deactivate( $args ); // reset license key activation

			if ( $reset == true ) {
				return true;
			}

			return add_settings_error( 'not_deactivated_text', 'not_deactivated_error', __( 'The license could not be deactivated. Use the License Deactivation tab to manually deactivate the license before activating a new license.', $this->text_domain ), 'updated' );
		}

		/**
		 * Deactivates the license key to allow key to be used on another blog
		 * @param string $input Deactivation checkbox input
		 * @return string
		 */
		public function menu_wc_am_license_key_deactivation( $input ) {

			$activation_status = get_option( $this->activated_key );

			$args = array(
				'email'       => $this->options[ 'activation_email' ],
				'licence_key' => $this->options[ 'api_key' ],
			);

			if ( 'on' == $input && $activation_status == 'Activated' ) {

				$deactivate_results = json_decode( $this->key_class->deactivate( $args ), true );

				if ( $deactivate_results['deactivated'] === true ) {
					$update = array(
						'api_key' => '',
						'activation_email' => ''
					);

					$merge_options = array_merge( $this->options, $update );

					update_option( $this->data_key, $merge_options );

					update_option( $this->activated_key, 'Deactivated' );

					add_settings_error( 'wc_am_deactivate_text', 'deactivate_msg', __( 'Plugin license deactivated. ', $this->text_domain ) . "{$deactivate_results['activations_remaining']}.", 'updated' );

					return 'on';
				}

				$this->check_error( $deactivate_results );
			} else {
				return 'off';
			}
		}

		/**
		 * Renders deactivates checkbox
		 */
		public function menu_wc_am_deactivate_textarea() {

			echo '<input type="checkbox" id="' . $this->deactivate_checkbox . '" name="' . $this->deactivate_checkbox . '" value="on"';
			echo checked( get_option( $this->deactivate_checkbox ), 'on' );
			echo '/>';
			?><span
				class="description"><?php _e( 'Deactivates an API License Key so it can be used on another blog.', $this->text_domain ); ?></span>
		<?php
		}

		/**
		 * Loads admin style sheets
		 */
		public function menu_css_scripts() {

			wp_enqueue_style( $this->data_key . '-css', $this->plugin_url() . 'pp-api/assets/css/admin-settings.css', array(), $this->version, 'all' );

		}
	}
}