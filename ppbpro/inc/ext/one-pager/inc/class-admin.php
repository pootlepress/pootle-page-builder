<?php
/**
 * pootle page builder one pager Admin class
 * @property string token Plugin token
 * @property string $url Plugin root dir url
 * @property string $path Plugin root dir path
 * @property string $version Plugin version
 */
class pootle_page_builder_one_pager_Admin{

	/** @var pootle_page_builder_one_pager_Admin Instance */
	private static $_instance = null;

	/**
	 * Main pootle page builder one pager Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @return pootle_page_builder_one_pager_Admin instance
	 * @since 	1.0.0
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Constructor function.
	 * @access  private
	 * @since 	1.0.0
	 */
	private function __construct() {
		$this->token   =   pootle_page_builder_one_pager::$token;
		$this->url     =   pootle_page_builder_one_pager::$url;
		$this->path    =   pootle_page_builder_one_pager::$path;
		$this->version =   pootle_page_builder_one_pager::$version;
	} // End __construct()

	/**
	 * Adds editor panel tab
	 * @param array $tabs The array of tabs
	 * @return array Tabs
	 * @filter pootlepb_content_block_tabs
	 * @since 	1.0.0
	 */
	public function content_block_tabs( $tabs ) {
		$tabs[ $this->token ] = array(
			'label' => '1 Pager',
			'priority' => 5,
		);
		return $tabs;
	}

	/**
	 * Adds custom menu type metabox
	 * @action admin_init
	 * @since 	1.0.0
	 */
	public function admin_init() {
		add_meta_box( 'sfxtp-menu-items', __( 'One pager menu item' ), array( $this, 'menu_meta_box' ), 'nav-menus', 'side', 'default' );
		add_action( 'delete_post', array( $this, 'remove_old_sections' ) );
	}

	/**
	 * Renders menu item meta box
	 */
	public function menu_meta_box(){
		global $_nav_menu_placeholder, $nav_menu_selected_id;
		$_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder : -1;
?>
		<div class="ppb-1pager-div" id="ppb-1pager-div">
			<?php
			foreach( get_option( 'pootlepb_1pager_sections', array() ) as $id => $sex ) {
				if ( 'publish' != get_post_status( $id ) ) {
					continue;
				}
				foreach ( $sex as $sec => $secName ) {
					$_nav_menu_placeholder--;
					?>
					<p>
						<label class="menu-item-title">
							<input type="hidden" class="ppb-1pager-title"
								name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]"
								value="<?php echo $secName; ?>" />
							<input type="checkbox" class="ppb-1pager-url"
								name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]"
								data-index="<?php echo $_nav_menu_placeholder; ?>"
								value="<?php echo get_the_permalink( $id ) . '#' . $sec; ?>" />
							<?php echo $secName; ?>
						</label>
					</p>
					<?php
				}
			}
			?>
			<p class="button-controls">
			<span class="add-to-menu">
				<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-custom-menu-item" id="submit-ppb-1pager" />
				<span class="spinner"></span>
			</span>
			</p>

		</div><!-- /.customlinkdiv -->
	<?php
	}

	/**
	 * Enqueue CSS and custom styles.
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue() {
		global $pagenow;
		if ( 'nav-menus.php' == $pagenow ) {
			wp_enqueue_script( $this->token . '-admin-js', $this->url . '/assets/admin-menu.js', array( 'jquery' ) );
		}
	}

	/**
	 * Saves one pager sections data
	 * @param array $data Panels data
	 * @filter pootlepb_panels_data_from_post
	 * @since    1.0.0
	 * @return array Panels data
	 */
	public function save_post( $data ) {

		//Return panels data if post_ID not set
		if ( empty( $_POST['post_ID'] ) ) {
			return $data;
		}

		$sections = array();

		foreach ( $data['widgets'] as $widget ) {
			$this->sections_in_page( $sections, $widget );
		}

		$this->save_sections_data( $sections );

		return $data;
	}

	/**
	 * Gets the section from widget
	 * @param array $sections Sections in current page
	 * @param array $widget The widget data
	 * @return array Panels data
	 * @filter pootlepb_panels_data_from_post
	 * @since    1.0.0
	 */
	private function sections_in_page( &$sections, $widget ) {
		if ( ! empty( $widget['info']['style'] ) ) {

			$wInfo = json_decode( $widget['info']['style'], true );

			if ( ! empty( $wInfo[ $this->token . '-section_name' ] ) ) {

				$secName    = preg_replace( '/[^a-zA-Z0-9]/', '-', $wInfo[ $this->token . '-section_name' ] );
				$sections[ $secName ] = $wInfo[ $this->token . '-section_name' ];
			}
		}
	}

	/**
	 * Remove sections from old ID
	 * @param $id
	 */
	public function remove_old_sections( $id ) {
		//Get current sections
		$allSections = get_option( 'pootlepb_1pager_sections', array() );
		//Add/Modify current post ID sections
		unset( $allSections[ $id ] );
		//Updating the option
		update_option( 'pootlepb_1pager_sections', $allSections );
	}

	/**
	 * Gets the section from widget
	 * @param array $sections Sections in current page
	 * @param array $widget The widget data
	 * @return array Panels data
	 * @filter pootlepb_panels_data_from_post
	 * @since    1.0.0
	 */
	private function save_sections_data( $sections ) {
		if ( ! empty( $sections ) ) {

			//Get current sections
			$allSections = get_option( 'pootlepb_1pager_sections', array() );
			//Add/Modify current post ID sections
			$allSections[ $_POST['post_ID'] ] = $sections;
			//Updating the option
			update_option( 'pootlepb_1pager_sections', $allSections );
		}
	}

	/**
	 * Adds content block panel fields
	 * @param array $fields Fields to output in content block panel
	 * @return array Tabs
	 * @filter pootlepb_content_block_fields
	 * @since 	1.0.0
	 */
	public function content_block_fields( $fields ) {
		$fields[ $this->token . '-section_name' ] = array(
			'name' => 'Section name',
			'placeholder' => 'Name your section',
			'type' => 'text',
			'priority' => 1,
			'tab' => $this->token,
		);
		$fields[ $this->token . '-offset' ] = array(
			'name' => 'Navigation offset',
			'type' => 'number',
			'priority' => 2,
			'tab' => $this->token,
		);
		return $fields;
	}

}