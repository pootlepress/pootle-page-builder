<?php
/**
 * Pootle Page Builder Pro main class
 * @static string $token Plugin token
 * @static string $file Plugin __FILE__
 * @static string $url Plugin root dir url
 * @static string $path Plugin root dir path
 * @static string $version Plugin version
 */
class Pootle_Page_Builder_Pro{

	/**
	 * @var 	Pootle_Page_Builder_Pro Instance
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * @var     string Token
	 * @access  public
	 * @since   1.0.0
	 */
	public static $token;

	/**
	 * @var     string Version
	 * @access  public
	 * @since   1.0.0
	 */
	public static $version;

	/**
	 * @var 	string Plugin main __FILE__
	 * @access  public
	 * @since 	1.0.0
	 */
	public static $file;

	/**
	 * @var 	string Plugin directory url
	 * @access  public
	 * @since 	1.0.0
	 */
	public static $url;

	/**
	 * @var 	string Plugin directory path
	 * @access  public
	 * @since 	1.0.0
	 */
	public static $path;

	/**
	 * @var 	Pootle_Page_Builder_Pro_Admin Instance
	 * @access  public
	 * @since 	1.0.0
	 */
	public $admin;

	/**
	 * @var 	Pootle_Page_Builder_Pro_Public Instance
	 * @access  public
	 * @since 	1.0.0
	 */
	public $public;

	/**
	 * Main Pootle Page Builder Pro Instance
	 *
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @return Pootle_Page_Builder_Pro instance
	 */
	public static function instance( $file ) {
		if ( null == self::$_instance ) {
			self::$_instance = new self( $file );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Constructor function.
	 * @param string $file __FILE__ of the main plugin
	 * @access  private
	 * @since   1.0.0
	 */
	private function __construct( $file ) {

		self::$token   =   'ppbpro';
		self::$file    =   $file;
		self::$url     =   plugin_dir_url( $file );
		self::$path    =   dirname( $file );
		self::$version =   '1.0.0';

		register_activation_hook( $file, array( $this, 'activated' ) );

		add_action( 'plugins_loaded', array( $this, 'init' ) );
	} // End __construct()

	/**
	 * Initiates the plugin
	 * @action init
	 * @since 1.0.0
	 */
	public function init() {

		if ( class_exists( 'Pootle_Page_Builder' ) ) {

			//Initiate admin
			$this->_admin();

			//Initiate public
			$this->_public();

			//Mark this add on as active - Not required with Freemius handling updates
			//add_filter( 'pootlepb_installed_add_ons', array( $this, 'add_on_active' ) );
		} else {

			//Mark this add on as active
			add_action( 'admin_notices', array( $this, 'ppb_required_notice' ) );

		}
	} // End init()

	/**
	 * Adds admin notice for pootle page dependency
	 * @action admin_notices
	 * @since 	1.0.0
	 */
	public function ppb_required_notice() {
		?>
		<div id="message" class="error">
			<p>
				Pootle Page Builder Pro requires pootle page builder,
				<a href="<?php echo admin_url() ?>/plugin-install.php?tab=plugin-information&amp;plugin=pootle-page-builder&amp;TB_iframe=true&amp;width=772&amp;height=429" class="thickbox" aria-label="Install pootle page builder" data-title="pootle page builder">
					Click here
				</a> to install pootle page builder.
			</p>
		</div>
		<?php
	}

	/**
	 * Initiates admin class and adds admin hooks
	 * @since 1.0.0
	 */
	private function _admin() {
		//Instantiating admin class
		$this->admin = Pootle_Page_Builder_Pro_Admin::instance();

		//Adding front end JS and CSS in /assets folder
		add_action( 'admin_enqueue_scripts', array( $this->admin, 'enqueue' ) );
		//Adding front end JS and CSS in /assets folder
		add_action( 'admin_init', array( $this->admin, 'init_settings' ) );
		//Admin settings tab
		add_filter( 'admin_menu', array( $this->admin, 'admin_menu' ), 25 );
		//Make live templates work
		add_filter( 'pootlepb_live_page_template', array( $this->admin, 'filter_template' ), 10, 2 );
		//Adds style field in row
		add_action( 'pootlepb_row_settings_fields', array( $this->admin, 'row_fields' ), 999 );
		//Pro modules
		add_action( 'pootlepb_modules', array( $this->admin, 'modules' ), 25 );
	}

	/**
	 * Initiates public class and adds public hooks
	 * @since 1.0.0
	 */
	private function _public() {
		//Instantiating public class
		$this->public = Pootle_Page_Builder_Pro_Public::instance();

		//Adding front end JS and CSS in /assets folder
		$this->public->init();
		//Add row CSS to row
		add_action( 'pootlepb_before_row', array( $this->public, 'row_css' ), 25 );

	} // End enqueue()

	/**
	 * Activation hook
	 * @since 1.0.0
	 */
	public function activated() {
		$url = admin_url( 'admin.php?page=page_builder_pro' );
		$welcome = 'Welcome to pootle page builder pro. Go to pootle page builder pro %saddons page%s to toggle addons.';
		$msg =
			'<h3>' . __( 'Hi there!' ) . '</h3>' .
			sprintf( __( $welcome ), "<a href='$url'>", '</a>' ) .
			"\n\n<a class='button pootle' href='$url'>" . __( 'Add ons Page' ) . '</a>';


		$notices = get_option( 'pootlepb_admin_notices', array() );

		$notices[ 'welcome-to-ppbpro' ] = array(
			'type'    => 'updated pootle',
			'message' => $msg,
		);

		update_option( 'pootlepb_admin_notices', $notices );
	}

	/**
	 * Marks this add on as active on
	 * @param array $active Active add ons
	 * @return array Active add ons
	 * @since 1.0.0
	 */
	public function add_on_active( $active ) {

		// To allows ppb add ons page to fetch name, description etc.
		$active[ self::$token ] = self::$file;

		return $active;
	}
}