<?php
/**
 * Contains Pootle_Page_Builder_Admin class
 * @author pootlepress
 * @since 0.1.0
 */

/**
 * Class Pootle_Page_Builder_Admin
 * Handles admin filter and action
 */
final class Pootle_Page_Builder_Admin {
	/**
	 * @var Pootle_Page_Builder_Admin Instance
	 * @since 0.1.0
	 */
	protected static $instance;

	/** @var array Post types supported by ppb */
	protected $post_types;

	/**
	 * Magic __construct
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->includes();
		$this->include_modules();
		$this->actions();
	}

	/**
	 * Include the reqd. admin files
	 * @since 0.1.0
	 */
	protected function includes() {

		/** Pootle Page Builder user interface */
		require_once POOTLEPB_DIR . 'inc/class-panels-ui.php';
		/** Content block - Editor panel and output */
		require_once POOTLEPB_DIR . 'inc/class-content-blocks.php';
		/** Take care of styling fields */
		require_once POOTLEPB_DIR . 'inc/styles-fields.php';
		/** Handles PPB meta data *Revisions * */
		require_once POOTLEPB_DIR . 'inc/revisions.php';
		/** More styling */
		require_once POOTLEPB_DIR . 'inc/vantage-extra.php';
	}


	protected function include_modules() {
		require_once POOTLEPB_DIR . 'inc/modules/init.php';
	}

	/**
	 * Adds the actions anf filter hooks for plugin functioning
	 * @access protected
	 * @since 0.1.0
	 */
	protected function actions() {
		//Adding page builder help tab
		add_action( 'load-page.php', array( $this, 'add_help_tab' ), 12 );
		add_action( 'load-post-new.php', array( $this, 'add_help_tab' ), 12 );

		//Save panel data on post save
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

		// Post actions
		add_action( 'page_row_actions', array( $this, 'post_row_actions' ), 10, 2 );
		add_action( 'post_row_actions', array( $this, 'post_row_actions' ), 10, 2 );

		//Allow the save post to save panels data
		add_filter( 'pootlepb_save_post_pass', array( $this, 'save_post_or_not' ), 10, 2 );
		add_filter( 'wp_insert_post_empty_content', array( $this, 'is_pb_post_empty' ), 25, 2 );
		//Settings
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'init' ) );

		ppb_fs()->add_filter('connect_message', array( $this, 'fs_message' ), 10, 6);
	}

	function fs_message (
		$message,
		$user_first_name,
		$plugin_title,
		$user_login,
		$site_link,
		$freemius_link
	) {
		return sprintf(
			__fs( 'hey-x' ) . '<br>' .
			__( 'Never miss an important update - Opt-in to our security and feature updates notifications, and non-sensitive diagnostic tracking with freemius.com', 'pootle-page-builder' ),
			$user_first_name,
			'<b>' . $plugin_title . '</b>',
			'<b>' . $user_login . '</b>',
			$site_link,
			$freemius_link
		);
	}

	public function init() {
		$this->post_types = pootlepb_posts();
		$this->options_init();
		$this->add_new();
	}

	/**
	 * Add a help tab to pages with panels.
	 * @action load-post-new.php, load-page.php
	 * @since 0.1.0
	 */
	public function add_help_tab() {
		$screen = get_current_screen();
		if ( 'post' == $screen->base && in_array( $screen->id, pootlepb_settings( 'post-types' ) ) ) {
			$screen->add_help_tab( array(
				'id'       => 'panels-help-tab', //unique id for the tab
				'title'    => __( 'Page Builder', 'ppb-panels' ), //unique visible title for the tab
				'callback' => array( $this, 'render_help_tab' )
			) );
		}
	}

	/**
	 * Display the content for the help tab.
	 * @TODO Make it more useful
	 * @since 0.1.0
	 */
	public function render_help_tab() {
		echo '<p>';
		_e( 'You can use Pootle Page Builder to create amazing pages, use addons to extend functionality.', 'siteorigin-panels' );
		_e( 'The page layouts are responsive and fully customizable.', 'siteorigin-panels' );
		echo '</p>';
	}

	/**
	 * Save the panels data
	 *
	 * @param $post_id
	 * @param $post
	 *
	 * @action save_post
	 * @since 0.1.0
	 */
	public function save_post( $post_id, $post ) {

		$pass = apply_filters( 'pootlepb_save_post_pass', true, $post );

		if ( empty( $pass ) ) {
			return;
		}

		$panels_data = pootlepb_get_panels_data_from_post();

		if ( empty( $panels_data['grids'] ) ) {
			delete_post_meta( $post_id, 'panels_data' );
			return;
		}

		if ( function_exists( 'wp_slash' ) ) {
			$panels_data = wp_slash( $panels_data );
		}

		/**
		 * Fired before saving pootle page builder post meta
		 * @param array $ppb_data Page builder data
		 * @param Int $post_id Post ID
		 * @param WP_Post $post Post object
		 */
		do_action( 'pootlepb_save_post', $panels_data, $post_id, $post );

		update_post_meta( $post_id, 'panels_data', $panels_data );
	}

	/**
	 * If pb rows are set, returns false false
	 * @param bool $maybe_empty Is post empty
	 * @param array $postarr Post data
	 * @return bool Post is empty or not
	 */
	public function is_pb_post_empty( $maybe_empty, $postarr ) {
		if ( ! empty( $postarr['grids'] ) ) {
			return false;
		} else {
			return $maybe_empty;
		}
	}

	/**
	 * Checks whether to save post or not
	 * @param bool|null $pass
	 * @param WP_Post $post
	 * @return bool
	 */
	public function save_post_or_not( $pass, $post ) {

		//Check nonce
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, 'pootlepb_nonce' ), 'pootlepb_save' ) ) {
			return false;
		}

		//Check if js was properly loaded
		if ( ! filter_input( INPUT_POST, 'panels_js_complete' ) ) {
			return false;
		}

		//User capability
		if ( ! current_user_can( 'edit_post', $post->id ) ) {
			return false;
		}

		if ( ! empty( $_POST['pootlepb_noPB'] ) ) {
			delete_post_meta( $post->ID, 'panels_data' );
			return false;
		}

		return $pass;

	}

	/**
	 * Add the options page
	 * @since 0.1.0
	 */
	public function admin_menu() {
		add_menu_page( 'Home', 'Page Builder', 'manage_options', 'page_builder', array(
			$this,
			'menu_page',
		), 'dashicons-screenoptions', '20.509' );
		add_submenu_page( 'page_builder', 'Settings', 'Settings', 'manage_options', 'page_builder_settings', array(
			$this,
			'menu_page',
		) );
		add_submenu_page( 'page_builder', 'Modules', 'Modules', 'manage_options', 'page_builder_modules', array(
			$this,
			'menu_page',
		) );
/*		add_submenu_page( 'page_builder', 'Add-ons', 'Add-ons', 'manage_options', 'page_builder_addons', array(
			$this,
			'menu_page',
		) );
*/	}

	/**
	 * Register all the settings fields.
	 * @since 0.1.0
	 */
	public function options_init() {
		register_setting( 'pootlepage-add-ons', 'pootlepb_add_ons' );
		register_setting( 'pootlepage-display', 'pootlepb_display', array(
			$this,
			'pootlepb_options_sanitize_display',
		) );
		register_setting( 'pootlepage-display', 'pootlepb-hard-uninstall' );
		register_setting( 'ppbpro_modules', 'ppb_enabled_addons' );
		register_setting( 'ppbpro_modules', 'ppb_disabled_addons' );

		add_settings_section( 'display', __( 'Display', 'ppb-panels' ), '__return_false', 'pootlepage-display' );

		// The display fields
		add_settings_field( 'responsive', __( 'Responsive', 'ppb-panels' ), array(
			$this,
			'options_field_generic',
		), 'pootlepage-display', 'display', array( 'type' => 'responsive' ) );
		//Mobile width
		add_settings_field( 'mobile-width', __( 'Mobile Width', 'ppb-panels' ), array(
			$this,
			'options_field_generic',
		), 'pootlepage-display', 'display', array( 'type' => 'mobile-width' ) );
		// Module panel position
		add_settings_field( 'modules-position', __( 'Modules insert panel position', 'ppb-panels' ), array(
			$this,
			'options_field_generic',
		), 'pootlepage-display', 'display', array( 'type' => 'modules-position' ) );
		// The display fields
		add_settings_field( 'hard-uninstall', __( 'Delete ALL data on uninstall', 'ppb-panels' ), array(
			$this,
			'options_field_generic',
		), 'pootlepage-display', 'display', array( 'type' => 'hard-uninstall' ) );
	}

	/**
	 * Display the admin page.
	 * @since 0.1.0
	 */
	public function menu_page() {

		//Replace prefix for submenu pages
		$inc_file = str_replace( 'page_builder_', '', filter_input( INPUT_GET, 'page' ) );

		//Replace main menu page with welcome
		$inc_file = str_replace( 'page_builder', 'welcome', $inc_file );

		include POOTLEPB_DIR . "tpl/$inc_file.php";
	}

	/**
	 * Redirecting for Page Builder > Add New option
	 * @since 0.1.0
	 */
	public function add_new() {
		global $pagenow;

		if ( 'admin.php' == $pagenow && 'page_builder_add' == filter_input( INPUT_GET, 'page' ) ) {
			header( 'Location: ' . admin_url( '/post-new.php?post_type=page&page_builder=pootle' ) );
			die();
		}
	}

	/**
	 * Output settings field
	 *
	 * @param array $args
	 * @param string $groupName
	 *
	 * @since 0.1.0
	 */
	public function options_field_generic( $args, $groupName = 'pootlepb_display' ) {
		$settings = pootlepb_settings();
		$name = 'name="' . esc_attr( $groupName ) . '[' . esc_attr( $args['type'] ) . ']"';
		$value = isset( $settings[ $args['type'] ] ) ? $settings[ $args['type'] ] : '';
		switch ( $args['type'] ) {
			case 'hard-uninstall' :
				?><label><input type="checkbox" name="pootlepb-hard-uninstall" id="pootlepb-hard-uninstall"
				                <?php checked( get_option( 'pootlepb-hard-uninstall' ) ) ?>
				                value="1"/> <?php _e( 'Enabled', 'ppb-panels' ) ?></label>
				<?php
				break;
			case 'responsive' :
				?><label><input type="checkbox"
				                <?php echo $name ?> <?php checked( $value ) ?>
				                value="1"/> <?php _e( 'Enabled', 'ppb-panels' ) ?></label><?php
				break;
			case 'modules-position' :
				?>
				<label>
					<input type="radio" <?php echo $name ?> <?php checked( 'left', $value ) ?> value="left"/>
					<?php _e( 'Left', 'ppb-panels' ) ?>
				</label>
				<label>
					<input type="radio" <?php echo $name ?> <?php checked( 'right', $value ) ?> value="right"/>
					<?php _e( 'Right', 'ppb-panels' ) ?>
				</label>
				<?php
				break;
			case 'mobile-width' :
				?><input type="text" <?php echo $name ?>
				         value="<?php echo esc_attr( $value ) ?>"
				         class="small-text" /> <?php _e( 'px', 'ppb-panels' ) ?><?php
				break;
		}

		if ( ! empty( $args['description'] ) ) {
			?><p class="description"><?php echo esc_html( $args['description'] ) ?></p><?php
		}
	}

	/**
	 * Sanitize display options
	 *
	 * @param $vals
	 *
	 * @return mixed
	 * @since 0.1.0
	 */
	public function pootlepb_options_sanitize_display( $vals ) {
		//Enable Responsive media queries
		$vals['responsive']      = ! empty( $vals['responsive'] );
		return $vals;
	}

	/**
	 * Filters the row actions
	 * @filter post_row_actions
	 */
	public function post_row_actions( $actions, $post ) {

		if( in_array( $post->post_type, $this->post_types ) && pootlepb_uses_pb( $post ) ) {
			
			$nonce_url = wp_nonce_url( get_the_permalink( $post->ID ), 'ppb-live-edit-nonce', 'ppbLiveEditor' );
			$actions['live-edit'] = '<a href="' . $nonce_url . '" aria-label="Edit “Home”">Live Edit</a>';
		}
		return $actions;
	}
}