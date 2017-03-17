<?php
/**
 * Plugin Name: Pootle Pagebuilder
 * Plugin URI: http://pootlepress.com/
 * Description: Pootle Pagebuilder is a front-end, drag and drop page builder that makes it easy to create beautiful WordPress pages and posts.
 * Version: 3.7.2
 * Author: Pootlepress
 * Author URI: http://pootlepress.com/
 * License: GPL version 3
 * @developer http://wpdevelopment.me <shramee@wpdevelopment.me>
 * @fs_premium_only /ppbpro/
 */

if ( ! class_exists( 'Pootle_Page_Builder' ) ) {

	/** Pootle page builder current version */
	define( 'POOTLEPB_VERSION', '3.7.2' );
	/** Pootle page builder __FILE__ */
	define( 'POOTLEPB_BASE_FILE', __FILE__ );
	/** Pootle page builder plugin directory path */
	define( 'POOTLEPB_DIR', dirname( __FILE__ ) . '/' );
	/** Pootle page builder plugin directory url */
	define( 'POOTLEPB_URL', plugin_dir_url( __FILE__ ) );

	/**
	 * Class Pootle_Page_Builder
	 * Pootle Page Builder admin class
	 * @since 0.1.0
	 */
	final class Pootle_Page_Builder {

		/**
		 * @var Pootle_Page_Builder instance of Pootle_Page_Builder
		 * @access protected
		 * @since 0.1.0
		 */
		protected static $instance;

		/**
		 * @var Pootle_Page_Builder_Admin Admin class instance
		 * @access protected
		 * @since 0.1.0
		 */
		protected $admin;

		/**
		 * @var Pootle_Page_Builder_Public Public class instance
		 * @access protected
		 * @since 0.1.0
		 */
		protected $public;

		/**
		 * @var WP_Query Contains ppb posts
		 * @access protected
		 * @since 0.3.0
		 */
		public $ppb_posts;

		/**
		 * Magic __construct
		 * @since 0.1.0
		 */
		public function __construct() {
			$this->includes();
			$this->hooks();

			// Init Freemius and add uninstall hook.
			ppb_fs()->add_action( 'after_uninstall', array( $this, 'uninstall' ) );
			ppb_fs()->add_filter( 'show_trial', '__return_false' );
		}

		/**
		 * Include the required files
		 * @since 0.1.0
		 */
		private function includes() {

			/** Variables used throughout the plugin */
			require_once POOTLEPB_DIR . 'inc/vars.php';
			/** Functions used throughout the plugin */
			require_once POOTLEPB_DIR . 'inc/funcs.php';
			/** Enhancements and fixes */
			require_once POOTLEPB_DIR . 'inc/enhancements-and-fixes.php';
			/** PPB Admin Class */
			require_once POOTLEPB_DIR . 'inc/class-admin.php';
			/** @var Pootle_Page_Builder_Admin PPB Admin Class Instance */
			$this->admin = $GLOBALS['Pootle_Page_Builder_Admin'] = new Pootle_Page_Builder_Admin();

			/** PPB Public Class */
			require_once POOTLEPB_DIR . 'inc/class-public.php';
			/** @var Pootle_Page_Builder_Public PPB Public Class Instance */
			$this->public = $GLOBALS['Pootle_Page_Builder_Public'] = new Pootle_Page_Builder_Public();

			if ( ppb_fs()->is__premium_only() ) {
				if ( ppb_fs()->is_plan( 'ppbpro' ) ) {
					/** PPB Public Class */
					require_once POOTLEPB_DIR . 'ppbpro/ppbpro.php';
				}
			}

			/** PPB Live Editor */
			require_once POOTLEPB_DIR . 'inc/class-live-editor.php';
			/** Intantiating main plugin class */
			Pootle_Page_Builder_Live_Editor::instance( __FILE__ );
		}

		/**
		 * Adds the actions and filter hooks for plugin functioning
		 * @since 0.1.0
		 */
		private function hooks() {
			register_activation_hook( __FILE__, array( $this, 'activate' ) );

			add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
			add_action( 'admin_init', array( $this, 'ppb_compatibility' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'wp_ajax_ppb_ipad_posts', array( $this, 'ipad_app_json' ) );
			add_action( 'wp_ajax_nopriv_ppb_ipad_posts', array( $this, 'ipad_app_json' ) );
			add_action( 'wp_ajax_ppb_ipad_login', array( $this, 'ipad_app_login' ) );
			add_action( 'wp_ajax_nopriv_ppb_ipad_login', array( $this, 'ipad_app_login' ) );
			add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );

			add_action( 'activated_plugin', array( $this, 'activation_redirect' ) );
		}

		public function activation_redirect( $plugin ) {
			if ( $plugin == plugin_basename( __FILE__ ) ) {
				exit( wp_redirect( admin_url( 'admin.php?page=page_builder' ) ) );
			}
		}

		/**
		 * Hook for activation of Page Builder.
		 * @since 0.1.0
		 */
		public function activate() {

			//Updating version
			add_option( 'pootlepb_initial_version', POOTLEPB_VERSION, '', 'no' );

			$current_user = wp_get_current_user();
			//Get first name if set
			$username = '';
			if ( ! empty( $current_user->user_firstname ) ) {
				$username = " {$current_user->user_firstname}";
			}

			//$welcome_message = "<b>Hey{$username}! Welcome to Page builder.</b> You're all set to start building stunning pages!<br><a class='button pootle' href='" . admin_url( '/admin.php?page=page_builder' ) . "'>Get started</a>";
			//pootlepb_add_admin_notice( 'welcome', $welcome_message, 'updated pootle' );
		}

		/**
		 * Return Query with all posts using ppb
		 * @return WP_Query
		 * @since 0.3.0
		 */
		public function ppb_posts() {

			if ( empty( $this->ppb_posts ) ) {
				//Get all posts using page builder
				$args            = array(
					'post_type'      => pootlepb_settings( 'post-types' ),
					'posts_per_page' => - 1,
					'meta_query'     => array(
						array(
							'key'     => 'panels_data',
							'compare' => 'EXISTS',
						),
					)
				);
				$this->ppb_posts = new WP_Query( $args );
			}

			return $this->ppb_posts;
		}

		/**
		 * Return Query with all posts using ppb
		 * @return WP_Query
		 * @since 0.3.0
		 */
		public function ipad_app_json() {
			$query = $this->ppb_posts();
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
			echo json_encode( $json );
			die();
		}

		public function ipad_app_login() {
			if ( 'server' == $_REQUEST['log'] && 'ping' == $_REQUEST['pwd'] ) {
				echo json_encode(
					array(
						'nonce'   => 'PootlePBresponse',
						'message' => __( "Connected to server, type in your username and password to start building!" ),
						'site'    => site_url(),
					)
				);
				exit;
			}
			// Nonce is checked, get the POST data and sign user on
			$user_signon = wp_signon( null, false );
			if ( is_wp_error( $user_signon ) ) {
				if ( username_exists( $_REQUEST['log'] ) ) {
					echo json_encode(
						array(
							'nonce'   => 'Failed',
							'message' => __( "Hi $_REQUEST[log], Your password doesn't match, please try again." ),
						)
					);
				} else {
					echo json_encode(
						array(
							'nonce'   => 'Failed',
							'message' => __( "We don't recognise you $_REQUEST[log], Kindly check your user name." ),
						)
					);
				}
			} else {
				if ( ! empty( $_POST['redirect_to'] ) ) {
					header( "Location: $_POST[redirect_to]" );
					exit;
				}
				$nonce = pootlepb_rand();
				set_transient( 'ppb-ipad-' . filter_input( INPUT_POST, 'log' ), $nonce, 25 * HOUR_IN_SECONDS );
				echo json_encode( array(
					'nonce'   => $nonce,
					'message' => __( 'Login successful, redirecting...' ),
				) );
			}

			exit();
		}

		/**
		 * Return Query with all posts using ppb
		 * @return WP_Query
		 * @since 0.3.0
		 */
		public function ppb_compatibility() {
			update_option( 'pootlepb_version', POOTLEPB_VERSION, 'no' );
		}

		/**
		 * Initialize the language files
		 * @action plugins_loaded
		 * @since 0.1.0
		 */
		public function plugins_loaded() {

			if ( ! function_exists( 'pbtn_script' ) ) {
				/** Functions used throughout the plugin */
				require_once POOTLEPB_DIR . 'inc/pootle-buttons/pootle-button.php';
			}

			load_plugin_textdomain( 'ppb-panels', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
			if ( defined( 'WPSEO_VERSION' ) && 0 > version_compare( WPSEO_VERSION, 3 ) ) {
				add_filter( 'wpseo_pre_analysis_post_content', 'pootlepb_wp_seo_filter', 10, 2 );
			}
		}

		/**
		 * Enqueue admin scripts and styles
		 * @global $pagenow
		 * @action admin_notices
		 * @since 0.1.0
		 */
		public function enqueue() {
			global $pagenow;
			wp_register_script( 'pootlepb-ui', POOTLEPB_URL . 'js/ppb-ui.js', array(
				'jquery-ui-dialog',
				'jquery-ui-tabs'
			), POOTLEPB_VERSION );
			wp_register_style( 'pootlepb-ui-styles', POOTLEPB_URL . 'css/ppb-jq-ui.css', array() );
			wp_enqueue_style( 'pootlepage-main-admin', plugin_dir_url( __FILE__ ) . 'css/main-admin.css', array(), POOTLEPB_VERSION );

			$page = filter_input( INPUT_GET, 'page' );
			if ( $pagenow == 'admin.php' && false !== strpos( $page, 'page_builder' ) ) {
				wp_enqueue_script( 'pootlepb-ui' );
				wp_enqueue_style( 'pootlepb-ui-styles' );
				wp_enqueue_script( 'ppb-settings-script', POOTLEPB_URL . 'js/settings.js', array( 'pootlepb-ui' ) );
				wp_enqueue_style( 'ppb-option-admin', POOTLEPB_URL . 'css/option-admin.css', array(), POOTLEPB_VERSION );

				if ( 'page_builder_modules' == $page ) {
					wp_enqueue_style( 'ppb-option-modules', POOTLEPB_URL . 'css/option-modules.css', array(), POOTLEPB_VERSION );
					wp_enqueue_script( 'ppb-option-modules', POOTLEPB_URL . 'js/option-modules.js', array( 'jquery-ui-sortable', ), POOTLEPB_VERSION );
				}
				add_thickbox();
			}
		}

		/**
		 * Outputs admin notices
		 * @action admin_notices
		 * @since 0.1.0
		 */
		public function admin_notices() {

			$notices = get_option( 'pootlepb_admin_notices', array() );

			delete_option( 'pootlepb_admin_notices' );

			if ( 0 < count( $notices ) ) {
				$html = '';
				foreach ( $notices as $k => $v ) {
					$html .= '<div id="' . esc_attr( $k ) . '" class="fade ' . esc_attr( $v['type'] ) . '">' . wpautop( $v['message'] ) . '</div>' . "\n";
				}
				echo $html;
			}
		}

		/**
		 * Add plugin action links.
		 *
		 * @param $links
		 *
		 * @action plugin_action_links_$file
		 * @return array
		 * @TODO Use this
		 * @since 0.1.0
		 */
		public function plugin_action_links( $links ) {
			return $links;
		}

		/**
		 * Executed by freemius on uninstall
		 */
		public function uninstall() {

			define( 'POOTLEPB_UNINSTALL', true );
			include 'run-on-uninstall.php';
		}
	} //class Pootle_Page_Builder
	global $Pootle_Page_Builder;

	/** @var Pootle_Page_Builder $Pootle_Page_Builder Instance  */
	$Pootle_Page_Builder = new Pootle_Page_Builder();
}