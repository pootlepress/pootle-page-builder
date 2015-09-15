<?php
/**
 * Contains Pootle_Page_Builder_Public class
 * @author pootlepress
 * @since 0.1.0
 */

/**
 * Class Pootle_Page_Builder_Public
 * Pootle Page Builder public class
 * @since 0.1.0
 */
final class Pootle_Page_Builder_Public {
	/**
	 * @var Pootle_Page_Builder_Public Instance
	 * @since 0.1.0
	 */
	protected static $instance;

	/**
	 * Magic __construct
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->includes();
		$this->actions();
	}

	/**
	 * Includes files required for public end rendering
	 * @since 0.1.0
	 */
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
	 * @param $classes
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