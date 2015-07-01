<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 26/6/15
 * Time: 6:41 PM
 * @since 0.1.0
 */
abstract class Pootle_Page_Builder_Abstract{

	/**
	 * Get instance of Pootle Page Builder
	 * @return object Called class instance
	 * @since 0.1.0
	 */
	public static function instance() {

		$class = get_called_class();

		if ( empty( $class::$instance ) ) {
			static::$instance = new $class();
		}
		return static::$instance;
	}

	/**
	 * Magic __construct
	 * @access private
	 * @since 0.1.0
	 */
	protected function __construct() {}

}