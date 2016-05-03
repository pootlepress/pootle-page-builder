<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 30/9/15
 * Time: 10:19 PM
 */

/**
 * Customize Setting class.
 *
 * Handles saving and sanitizing of settings.
 *
 * @since 3.4.0
 *
 * @see WP_Customize_Manager
 */
class Lib_Customize_Setting extends WP_Customize_Setting {

	/**
	 * @access public
	 * @var string
	 */
	public $type = 'post_meta';

	/**
	 * Handle previewing the setting.
	 *
	 * @since 3.4.0
	 */
	public function preview() {

		if ( empty( $_GET['post_id'] ) ) { return; }

		if ( ! isset( $this->_original_value ) ) {
			$this->_original_value = $this->value();
		}

		if ( ! isset( $this->_previewed_blog_id ) ) {
			$this->_previewed_blog_id = get_current_blog_id();
		}

		add_filter( "get_post_metadata", array( $this, 'filter_post_metadata' ), 10, 4 );

		if ( $_GET['post_id'] != get_option( 'lib_current_post_meta' ) ) {
			update_option( 'lib_current_post_meta', $_GET['post_id'] );
		}
	}

	public function filter_post_metadata( $set, $object_id, $meta_key ) {

		if ( empty( $_GET['post_id'] ) || $object_id != $_GET['post_id'] || $meta_key != $this->id_data[ 'base' ] ) {
			return $set;
		}

		if ( is_array( $set ) ) {
			$set[0] = wp_parse_args(  $this->_preview_filter( $set[0] ), $set[0] );
		} else {
			$set = array(
				0 => $this->_preview_filter( $set[0] )
			);
		}
		return $set;
	}

	/**
	 * Save the value of the setting, using the related API.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $value The value to update.
	 * @return mixed The result of saving the value.
	 */

	protected function update( $value ) {

		$post_id = get_option( 'lib_current_post_meta' );

		$options = get_post_meta( $post_id, $this->id_data['base'], true );

		if ( empty( $options ) ) {
			$options = array();
		}

		if ( is_array( $options ) ) {

			$options = $this->multidimensional_replace( $options, $this->id_data[ 'keys' ], $value );

		}

		return update_post_meta( $post_id, $this->id_data['base'], $options );
	}

	/**
	 * Fetch the value of the setting.
	 *
	 * @since 3.4.0
	 *
	 * @return mixed The value.
	 */
	public function value() {
		global $wp_filter;

		if ( empty( $_GET['post_id'] ) ) { return; }

		if ( empty( $wp_filter['get_post_metadata'] ) ) {
			$wp_filter['get_post_metadata'] = array();
		}

		$post_meta_filters = $wp_filter['get_post_metadata'];

		$wp_filter['get_post_metadata'] = array();

		$values = get_post_meta(
			$_GET['post_id'],
			$this->id_data[ 'base' ],
			true
		);

		$wp_filter['get_post_metadata'] = $post_meta_filters;

		return $this->multidimensional_get( $values, $this->id_data[ 'keys' ], $this->default );
	}
}
