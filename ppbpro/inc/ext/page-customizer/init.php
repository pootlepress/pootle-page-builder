<?php
/**
 * Plugin Name: Page Customizer
 * Plugin URI:  http://pootlepress.com/
 * Description: Page customizer adds options for individual pages. Add a fullscreen background video, change page background image and color, change header background image and color. Hide header, titles, breadcrumbs, sidebar and footer. Mobile options to change background image and color for phones and tablets.
 * Version:     1.0.0
 * Author:      PootlePress
 * Author URI:  http://pootlepress.com/
 * Requires at least: 4.0.0
 * Tested up to: 4.1.1
 *
 * Text Domain: pootle-page-customizer
 * Domain Path: /languages/
 *
 * @package Pootle_Page_Customizer
 * @category Core
 * @author PootlePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

//Variables
require_once( dirname( __FILE__ ) . '/includes/vars.php' );

//Post meta customizer
require_once( dirname( __FILE__ ) . '/includes/class-customizer-postmeta.php' );

/**
 * Returns the main instance of Pootle_Page_Customizer to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Pootle_Page_Customizer
 */
function Pootle_Page_Customizer() {
	return Pootle_Page_Customizer::instance();
} // End Pootle_Page_Customizer()

$Pootle_Page_Customizer_Instance = Pootle_Page_Customizer();

/**
 * Main Pootle_Page_Customizer Class
 *
 * @class Pootle_Page_Customizer
 * @version    1.0.0
 * @since 1.0.0
 * @package    Pootle_Page_Customizer
 * @author PootlePress
 */
final class Pootle_Page_Customizer {
	/**
	 * Pootle_Page_Customizer The single instance of Pootle_Page_Customizer.
	 * @var    object
	 * @access  private
	 * @since    1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	/**
	 * The plugin directory URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_url;

	/**
	 * The plugin directory path.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_path;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * The settings object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings;

	/**
	 * The post types we support.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $supported_post_types = array();

	/**
	 * The taxonomies we support.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $supported_taxonomies = array();

	/**
	 * All the post metas to populate.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $fields = array();

	/**
	 * Array of classes to be put in body
	 * @var array
	 */
	public $body_classes = array();

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 */
	public function __construct() {
		$this->token       = 'pootle-page-customizer';
		$this->plugin_url  = plugin_dir_url( __FILE__ );
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->version     = '1.0.0';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'setup' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_links' ) );
	}

	/**
	 * Main Pootle_Page_Customizer Instance
	 *
	 * Ensures only one instance of Pootle_Page_Customizer is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Pootle_Page_Customizer()
	 * @return Pootle_Page_Customizer instance
	 */
	public static function instance() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'pootle-page-customizer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Plugin page links
	 *
	 * @since  1.0.0
	 */
	public function plugin_links( $links ) {
		$plugin_links = array(
			'<a href="http://support.woothemes.com/">' . __( 'Support', 'pootle-page-customizer' ) . '</a>',
			'<a href="http://docs.woothemes.com/document/pootle-page-customizer/">' . __( 'Docs', 'pootle-page-customizer' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Installation.
	 * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
	}

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

	/**
	 * Setup all the things.
	 * @return void
	 */
	public function setup() {
		$this->load_plugin_textdomain();

		$this->get_supported_post_types();
		$this->get_meta_fields();

		new Lib_Customizer_Postmeta( $this->token, 'Page Customizer', $this->fields );

		add_action( 'admin_init', array( $this, 'register_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );

		add_action( 'admin_print_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'public_scripts' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customizer_script' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_action( 'admin_notices', array( $this, 'customizer_notice' ) );
		add_action( 'admin_bar_menu', array( $this, 'add_item' ), 999 );
	}

	/**
	 * @param $admin_bar
	 */
	function add_item( $admin_bar ) {
		global $post;
		if ( is_page() ) {
			$args = array(
				'id'    => 'page-custo-link',
				'title' => 'Customize Page',
				'href'  => admin_url( "customize.php?post_id={$post->ID}&autofocus[panel]=lib-pootle-page-customizer&url=" . get_permalink( $post->ID ) . "?post_id={$post->ID}" ),
				'meta'  => array(
					'title' => __( 'Customize this page in customizer' ), // Text will be shown on hovering
				),
			);
			$admin_bar->add_menu( $args );
		}
	}

	/**
	 * Admin notice
	 * Checks the notice setup in install(). If it exists display it then delete the option so it's not displayed again.
	 * @since   1.0.0
	 * @return  void
	 */
	public function customizer_notice() {
		if ( $notices = get_option( 'page_custo_activation_notice' ) ) {

			foreach ( $notices as $notice ) {
				echo '<div class="updated">' . $notice . '</div>';
			}

			delete_option( 'page_custo_activation_notice' );
		}
	}

	public function register_meta_box() {
		foreach ( $this->supported_post_types as $post_type ) {
			add_meta_box( 'ppc-meta-box', 'Page Customizer settings', array( $this, 'custom_fields' ), $post_type );
		}
	}

	public function custom_fields( $id ) {
		global $post;
		wp_localize_script( 'ppc-admin-script', 'ppc_metadata', get_post_meta( $id, $this->token, true ) );
		$fields          = $this->fields;
		$field_structure = array();
		foreach ( $fields as $key => $field ) {
			$field_structure[ $field['section'] ][] = $field;
		}
		echo "<div id='ppc-tabs-wrapper'>";
		echo "<ul class='ppc-sections-nav nav-tab-wrapper'>";
		foreach ( $field_structure as $sec => $fields ) {
			echo ""
			     . "<li>"
			     . "<a class='nav-tab' href='#ppc-section-{$sec}'> $sec </a>"
			     . "</li>";
		}
		echo "</ul>";
		foreach ( $field_structure as $sec => $fields ) {
			echo "<div class='ppc-section' id='ppc-section-{$sec}'>";
			foreach ( $fields as $fld ) {
				$this->render_field( $fld );
			}
			echo "</div>";
		}
		echo '</div>';
		echo '<a ' .
			 'style="margin: 10px auto 0 auto; display: block; width: 169px; text-align: center; padding: 0;"' .
		     'href="' . admin_url( "customize.php?post_id={$post->ID}&autofocus[panel]=lib-pootle-page-customizer&url=" . get_permalink( $post->ID ). "?post_id={$post->ID}" ) . '" ' .
		     'class="button button-primary">' .
		     'Customize in Customizer' .
		     '</a>';
	}

	/**
	 * Adds control scripts to WP_Customize_Manager
	 * @since 1.0.0
	 */
	public function customizer_script() {
		wp_enqueue_script( 'pppc-customize-controls', plugin_dir_url( __FILE__ ) . 'assets/js/customizer.js', array( 'jquery' ), false, true );
		wp_enqueue_style( 'pppc-customize-controls-styles', plugin_dir_url( __FILE__ ) . 'assets/css/customizer.css' );
	}

	public function save_post( $postID ) {
		$post = get_post( $postID );

		//check if post type is post,page or product
		if ( ! in_array( $post->post_type, $this->supported_post_types ) ) {
			return;
		}

		if ( isset( $_REQUEST[ $this->token ] ) && is_array( $_REQUEST[ $this->token ] ) ) {
			$PPCValues = $_REQUEST[ $this->token ];
			update_post_meta( $postID, $this->token, $PPCValues );
		}
	}

	private function get_supported_post_types() {
		$this->supported_post_types = get_post_types( '', 'names' );
	}

	private function get_meta_fields() {
		global $page_customizer_fields;
		$this->fields = $page_customizer_fields;
	}

	/**
	 * Gets value of post meta
	 * @global WP_Post $post
	 *
	 * @param string $section
	 * @param string $id
	 * @param mixed $default
	 * @param int|bool $post_id
	 *
	 * @return string
	 */
	protected function get_value( $section, $id, $default = null, $post_id = false ) {
		//Getting post id if not set
		if ( ! $post_id ) {
			global $post;
			$post_id = $post->ID;
		}

		$ret = get_post_meta( $post_id, $this->token, true );
		if ( ! empty( $ret[ $id ] ) ) {
			return $ret[ $id ];
		} else {
			return $default;
		}
	}

	private function get_meta_key( $section, $id ) {
		return '_' . $this->token . '-' . $section . '-' . $id;
	}

	private function get_field_key( $id ) {
		return $this->token . '[' . $id . ']';
	}

	/**
	 * Enqueue CSS and custom styles.
	 * @since   1.0.0
	 * @return  bool|void
	 */
	public function public_scripts() {

		if ( ! is_single() && ! is_page() ) { return false; }
		wp_enqueue_style( 'ppc-styles', plugins_url( '/assets/css/style.css', __FILE__ ) );
		wp_enqueue_script( 'page-custo-script', plugins_url( '/assets/js/public.js', __FILE__ ) );
		$bodyBgType = $this->get_value( 'Background', 'background-type', false );
		$videoUrl = $this->get_value( 'Background', 'background-video', false );
		if ( 'video' == $bodyBgType && ! empty( $videoUrl ) ) {
			echo '<script> window.pageCustoVideoUrl = "' . $videoUrl . '";</script>';
			?>
			<video id="page-customizer-bg-video" style="display: none;"
			       preload="auto" autoplay="true" loop="loop" muted="muted" volume="0">
				<?php
				echo "<source src='{$videoUrl}' type='video/mp4'>";
				echo "<source src='{$videoUrl}' type='video/webm'>";
				?>
				Sorry, your browser does not support HTML5 video.
			</video>
			<?php
		}

		//Header options
		$hideHeader    = $this->get_value( 'Header', 'hide-header', false );
		$headerBgColor = $this->get_value( 'Header', 'header-background-color', null );
		$headerBgImage = $this->get_value( 'Header', 'header-background-image', null );

		//Background options
		$bgColor   = $this->get_value( 'Background', 'background-color', null );
		$bgImage   = $this->get_value( 'Background', 'background-image', null );
		if ( 'video' == $bodyBgType ) {
			$bgImage = $this->get_value( 'Background', 'background-responsive-image', null );
		}
		$BgOptions = ' no-repeat ' . $this->get_value( 'Background', 'background-attachment', null ) . ' center/cover';

		//Content
		$hideBread = $this->get_value( 'Content', 'hide-breadcrumbs', null );
		$hideTitle = $this->get_value( 'Content', 'hide-title', null );
		$hideSidebar = $this->get_value( 'Content', 'hide-sidebar', null );
		//Footer options
		$hideFooter = $this->get_value( 'Footer', 'hide-footer', false );
		$footerBgColor = $this->get_value( 'Footer', 'footer-background-color', null );
		//Init $css
		$css = '/*Pootle Pagebuilder Page Customizer*/';
		//Header styles
		$css .= '#main-header, #masthead, #header, #site-header, .site-header, .tc-header{';
		if ( $hideHeader ) {
			$css .= "display : none !important;";
		}
		if ( $headerBgColor ) {
			$css .= "background-color : {$headerBgColor} !important;";
		}
		if ( $headerBgImage ) {
			$css .= "background-image : url({$headerBgImage}) !important;";
			$css .= "background-size : cover !important;";
		}
		//Header styles END
		$css .= "}\n";

		//Background styles
		$css .= 'body.pootle-page-customizer-active {';
		if ( 'color' == $bodyBgType && $bgColor ) {
			$css .= "background: {$bgColor} !important;";
		} else {
			$css .= "background : url({$bgImage}){$BgOptions} !important;";
			$css .= "background-size : cover";
		}
		//Background styles END
		$css .= "}\n";

		//Content
		if ( $hideBread ) {
			$css .= "#breadcrumbs, #breadcrumb, .breadcrumbs, .breadcrumb, .breadcrumbs-trail, .wc-breadcrumbs, .wc-breadcrumb, .woocommerce-breadcrumb, .woocommerce-breadcrumbs {\n" .
			        "display : none !important;\n" .
			        "}\n";
		}
		if ( $hideTitle ) {
			$css .= ".main_title, .entry-title {display : none !important;}\n";
		}
		if ( $hideSidebar ) {
			$css .= "#sidebar, .sidebar, .side-bar {display : none !important;}\n";
			$css .= "#content, .content, .content-area { width : 100% !important;}\n";
		}

		//Footer style
		$css .= '.colophon, .pootle-page-customizer-active #footer, .pootle-page-customizer-active #main-footer,' .
		        ' .pootle-page-customizer-active #site-footer, .pootle-page-customizer-active .site-footer{';
		if ( $hideFooter ) {
			$css .= "display : none !important;";
		}
		if ( $footerBgColor ) {
			$css .= "background-color : $footerBgColor !important;";
		}
		//Footer styles END
		$css .= "}\n";

		$css .= $this->fix_ux();
		$css .= '@media only screen and (max-width:768px) {';
		$css .= $this->mobile_styles();
		$css .= '}';
		wp_add_inline_style( 'ppc-styles', $css );

	}

	/**
	 * Outputs mobile styles
	 * @return string Mobile styles
	 */
	public function fix_ux() {
		$css     = '';
		$theme = (string) wp_get_theme();

		if ( in_array( $theme, array( 'Espied', 'Divi' ) ) ) {
			$css .= "#page, #main-content { background-color: transparent; }";
		}

		return $css;
	}

	/**
	 * Outputs mobile styles
	 * @return string Mobile styles
	 */
	public function mobile_styles() {
		$css     = '';
		$bgColor = $this->get_value( 'Mobile', 'mob-background-color', null );
		$bgImage = $this->get_value( 'Mobile', 'mob-background-image', null );

		$css .= "body.pootle-page-customizer-active {\n" .
		        "background-color : {$bgColor} !important;\n";
		if ( ! empty( $bgImage ) ) {
			$css .= "background-image : url({$bgImage}) !important;\n";
		}
		$css .= "}\n";

		if ( $this->get_value( 'Mobile', 'mob-hide-footer', false ) ) {
			$css .= "#footer, #main-footer, #site-footer, .site-footer{ display : none !important; }";
		}

		if ( $this->get_value( 'Mobile', 'mob-hide-header', false ) ) {
			$css .= "#main-header, #masthead, #header, #site-header, .site-header, .tc-header{ display : none !important; }";
		}

		if ( $this->get_value( 'Mobile', 'mob-hide-sidebar', null ) ) {
			$css .= "aside, .sidebar, .side-bar {display : none !important;}\n";
			$css .= "#content, .content, .content-area { width : 100% !important;}\n";
		}

		return $css;
	}

	/**
	 * Enqueue Js
	 * @global type $pagenow
	 * @return null
	 */
	public function admin_scripts() {
		global $pagenow;

		if (
			( ! isset( $pagenow ) || ! ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) )
			OR
			( isset( $_REQUEST['post-type'] ) && strtolower( $_REQUEST['post_type'] ) != 'page' )
		) {
			return;
		}

		// only in post and page create and edit screen

		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'ppc-admin-script', trailingslashit( $this->plugin_url ) . 'assets/js/admin/admin.js', array(
			'wp-color-picker',
			'jquery',
			'thickbox',
			'jquery-ui-tabs'
		) );
		wp_enqueue_script(
			'lib-alpha-color-picker',
			plugin_dir_url( __FILE__ ) . '/assets/alpha-color-picker.js',
			array( 'jquery', 'wp-color-picker' )
		);

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_style( 'ppc-admin-style', trailingslashit( $this->plugin_url ) . 'assets/css/admin/admin.css' );
	}

	/**
	 * SFX Page Customizer Body Class
	 * Adds a class based on the extension name and any relevant settings.
	 */
	public function body_class( $classes ) {
		$this->body_classes[] = 'pootle-page-customizer-active';

		return array_merge( $classes, $this->body_classes );
	}

	/**
	 * Render a field of a given type.
	 * @access public
	 * @since 1.0.0
	 *
	 * @param array $args The field parameters.
	 * @param string $output_format = ( post || termEdit || termAdd )
	 * @param array $tax_data - Taxonomy data if rendering for taxonomy
	 *
	 * @return string
	 */
	public function render_field( $args, $output_format = 'post', $tax_data = null ) {
		$html = '';

		// Make sure we have some kind of default, if the key isn't set.
		if ( ! isset( $args['default'] ) ) {
			$args['default'] = '';
		}

		$method = 'render_field_' . $args['type'];

		if ( ! method_exists( $this, $method ) ) {
			$method = 'render_field_text';
		}

		// Construct the key.
		$key = $this->get_field_key( $args['id'] );
		$id  = $this->get_meta_key( $args['section'], $args['id'] );

		//Prefix to field
		$html_prefix = ''
		               . "<div class='field ppc-field field-section-{$args['section']} {$args['id']}'>"
		               . '<label class="label" for="' . esc_attr( $key ) . '">' . esc_html( $args['label'] ) . '</label>'
		               . '<div class="control">';

		//Getting current value
		$current_val = $this->get_value( $args['section'], $args['id'], $args['default'] );

		//Suffix to field
		$html_suffix = ''
		               . '</div>'
		               . '</div>';

		//Prefix
		$html .= $html_prefix;

		//Adding id
		$args['id'] = $id;

		//Output the field
		$method_output = $this->$method( $key, $args, $current_val );
		$html .= $method_output;

		// Output the description
		if ( isset( $args['description'] ) ) {
			$description = '<p class="description">' . wp_kses_post( $args['description'] ) . '</p>' . "\n";
			if ( in_array( $args['type'], (array) apply_filters( 'wf_newline_description_fields', array(
				'textarea',
				'select'
			) ) ) ) {
				$description = wpautop( $description );
			}
			$html .= $description;
		}

		//Suffix
		$html .= $html_suffix;

		echo $html;
	}

	/**
	 * Render HTML markup for the "text" field type.
	 * @access  protected
	 * @since   1.0
	 *
	 * @param   string $key The unique ID of this field.
	 * @param   array $args Arguments used to construct this field.
	 *
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_text( $key, $args, $current_val = null ) {
		$html = '<input id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( $key ) . '" size="40" type="text" value="' . esc_attr( $current_val ) . '" />' . "\n";

		return $html;
	}

	/**
	 * Render HTML markup for the "radio" field type.
	 * @access  protected
	 * @since   1.0
	 *
	 * @param   string $key The unique ID of this field.
	 * @param   array $args Arguments used to construct this field.
	 *
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_radio( $key, $args, $current_val = null ) {
		$html = '';
		if ( isset( $args['choices'] ) && ( 0 < count( (array) $args['choices'] ) ) ) {
			foreach ( $args['choices'] as $k => $v ) {
				$html .= '<label for="' . esc_attr( $key ) . '"><input type="radio" name="' . esc_attr( $key ) .
				         '" value="' . esc_attr( $k ) . '"' . checked( esc_attr( $current_val ), $k, false ) . ' /> '
				         . $v . '</label><br>' . "\n";
			}
		}

		return $html;
	}

	/**
	 * Render HTML markup for the "textarea" field type.
	 * @access  protected
	 * @since   1.0
	 *
	 * @param   string $key The unique ID of this field.
	 * @param   array $args Arguments used to construct this field.
	 *
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_textarea( $key, $args, $current_val = null ) {
		$html = '<textarea id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( $key ) . '" cols="42" rows="5">' . $current_val . '</textarea>' . "\n";

		return $html;
	}

	/**
	 * Render HTML markup for the "checkbox" field type.
	 * @access  protected
	 * @since   1.0
	 *
	 * @param   string $key The unique ID of this field.
	 * @param   array $args Arguments used to construct this field.
	 *
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_checkbox( $key, $args, $current_val = null ) {
		$html = '<input id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( $key ) . '" type="checkbox" value="1" ' . checked( $current_val, '1', false ) . ' />';

		return $html;
	}

	/**
	 * Render HTML markup for the "select" field type.
	 * @access  protected
	 * @since   1.0
	 *
	 * @param   string $key The unique ID of this field.
	 * @param   array $args Arguments used to construct this field.
	 *
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_select( $key, $args, $current_val = null ) {
		$html = '';
		if ( isset( $args['choices'] ) && ( 0 < count( (array) $args['choices'] ) ) ) {
			$html .= '<select id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( $key ) . '">' . "\n";
			foreach ( $args['choices'] as $k => $v ) {
				$html .= '<option value="' . esc_attr( $k ) . '"' . selected( esc_attr( $current_val ), $k, false ) . '>' . esc_html( $v ) . '</option>' . "\n";
			}
			$html .= '</select>' . "\n";
		}

		return $html;
	}

	/**
	 * Render HTML markup for the "color" field type.
	 * @access  protected
	 * @since   1.0
	 *
	 * @param   string $key The unique ID of this field.
	 * @param   array $args Arguments used to construct this field.
	 *
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_lib_color( $key, $args, $current_val = null ) {
		$html = '<input class="color-picker-hex" data-alpha="true" id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( $key ) . '" type="text" value="' . esc_attr( $current_val ) . '" />';

		return $html;
	}

	/**
	 * Render HTML markup for the "image" field type.
	 * @access  protected
	 * @since   1.0
	 *
	 * @param   string $key The unique ID of this field.
	 * @param   array $args Arguments used to construct this field.
	 *
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_image( $key, $args, $current_val = null ) {
		$html = '<input class="image-upload-path" type="text" id="' . esc_attr( $args['id'] ) . '" style="width: 200px; max-width: 100%;" name="' . esc_attr( $key ) . '" value="' . esc_attr( $current_val ) . '" /><button class="button upload-button">Upload</button>';

		return $html;
	}

	/**
	 * Render HTML markup for the "image" field type.
	 * @access  protected
	 * @since   1.0
	 *
	 * @param   string $key The unique ID of this field.
	 * @param   array $args Arguments used to construct this field.
	 *
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_upload( $key, $args, $current_val = null ) {
		$html = '<input class="video-upload-path" type="text" id="' . esc_attr( $args['id'] ) . '" style="width: 200px; max-width: 100%;" name="' . esc_attr( $key ) . '" value="' . esc_attr( $current_val ) . '" /><button class="button video-upload-button">Upload Video</button>';

		return $html;
	}

} // End Class