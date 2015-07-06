<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 26/6/15
 * Time: 6:46 PM
 * @since 0.1.0
 */

/**
 * Pootle Page Builder admin class
 * Class Pootle_Page_Builder_Public
 * Use Pootle_Page_Builder_Public::instance() to get an instance
 * @since 0.1.0
 */
final class Pootle_Page_Builder_Public extends Pootle_Page_Builder_Abstract {
	/**
	 * @var Pootle_Page_Builder_Public
	 * @since 0.1.0
	 */
	protected static $instance;

	/**
	 * Magic __construct
	 * $since 1.0.0
	 * @since 0.1.0
	 */
	protected function __construct() {
		$this->includes();
		$this->actions();
	}

	protected function includes() {
		require_once POOTLEPB_DIR . 'inc/class-render-layout.php';
		require_once POOTLEPB_DIR . 'inc/class-front-css-js.php';
	}

	/**
	 * Adds the actions anf filter hooks for plugin
	 * @since 0.1.0
	 */
	protected function actions() {

		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	/**
	 * Add all the necessary body classes.
	 *
	 * @param $classes
	 *
	 * @return array
	 * @since 0.1.0
	 */
	public function body_class( $classes ) {

		if ( pootlepb_is_panel() ) {
			$classes[] = 'ppb-panels';
		}

		return $classes;
	}
}