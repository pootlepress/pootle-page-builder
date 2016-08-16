<?php

/**
 * pootle page builder one pager public class
 * @property string $token Plugin token
 * @property string $url Plugin root dir url
 * @property string $path Plugin root dir path
 * @property string $version Plugin version
 */
class pootle_page_builder_one_pager_Public{

	/**
	 * @var 	pootle_page_builder_one_pager_Public Instance
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * @var 	array 1 pager sections on this page
	 * @access  private
	 * @since 	1.0.0
	 */
	private $sections = array();

	/**
	 * Main pootle page builder one pager Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @since 1.0.0
	 * @return pootle_page_builder_one_pager_Public instance
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
	 * @since   1.0.0
	 */
	private function __construct() {
		$this->token   =   pootle_page_builder_one_pager::$token;
		$this->url     =   pootle_page_builder_one_pager::$url;
		$this->path    =   pootle_page_builder_one_pager::$path;
		$this->version =   pootle_page_builder_one_pager::$version;
	} // End __construct()

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 * @since 1.0.0
	 */
	public function enqueue() {
		$token = $this->token;
		$url = $this->url;

		wp_enqueue_style( $token . '-css', $url . '/assets/front-end.css' );
		wp_enqueue_script( 'waypoints', $url . '/assets/waypoints.min.js', array( 'jquery' ) );
		wp_enqueue_script( $token . '-js', $url . '/assets/front-end.js', array( 'jquery', 'waypoints' ) );
	}

	/**
	 * Adds 1 pager anchor
	 * @param array $info The widget info
	 * @since 0.1.0
	 */
	public function pager_anchor( $info ) {

		$set = json_decode( $info['info']['style'], true );

		if ( !empty( $set[ $this->token . '-section_name' ] ) ) {

			$id = preg_replace( '/[^a-zA-Z0-9]/', '-', $set[ $this->token . '-section_name' ] );
			$this->sections[ $id ] = $set[ $this->token . '-section_name' ];

			$offset = empty( $set[ $this->token . '-offset' ] ) ? 0 : $set[ $this->token . '-offset' ];

			echo "<div class='one-pager-section-marker' data-offset='$offset' id='$id'></div>";
		}
	}

	/**
	 * Adds 1 pager anchor
	 * @param array $info The widget info
	 * @filter pootlepb_render
	 * @since 0.1.0
	 */
	public function nav() {

		if ( !empty( $this->sections ) ) {
			echo '<div id="one-pager-nav-container">';
			echo '<div id="one-pager-nav">';
			foreach ( $this->sections as $id => $name ) {
				echo "<a class='one-pager-menu-item' href='#{$id}' title='{$name}'></a>";
			}
			echo "<a class='ppb-one-pager-to-top' href='#'></a>";
			echo '</div>';
			echo '</div>';
		}
	}
}