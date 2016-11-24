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
class Shramee_Post_Meta_Customize_Setting extends WP_Customize_Setting {

	/**
	 * @access public
	 * @var string
	 */
	public $type = 'post_meta';

	/**
	 * Add filters to supply the setting's value when accessed.
	 *
	 * If the setting already has a pre-existing value and there is no incoming
	 * post value for the setting, then this method will short-circuit since
	 * there is no change to preview.
	 *
	 * @since 3.4.0
	 * @since 4.4.0 Added boolean return value.
	 * @access public
	 *
	 * @return bool False when preview short-circuits due no change needing to be previewed.
	 */
	public function preview() {

		if ( ! isset( $this->_previewed_blog_id ) ) {
			$this->_previewed_blog_id = get_current_blog_id();
		}

		// Prevent re-previewing an already-previewed setting.
		if ( $this->is_previewed ) {
			return true;
		}

		$id_base = $this->id_data['base'];
		$is_multidimensional = ! empty( $this->id_data['keys'] );
		$multidimensional_filter = array( $this, '_multidimensional_preview_filter' );

		/*
		 * Check if the setting has a pre-existing value (an isset check),
		 * and if doesn't have any incoming post value. If both checks are true,
		 * then the preview short-circuits because there is nothing that needs
		 * to be previewed.
		 */
		$undefined = new stdClass();
		$needs_preview = ( $undefined !== $this->post_value( $undefined ) );
		$value = null;

		// Since no post value was defined, check if we have an initial value set.
		if ( ! $needs_preview ) {
			if ( $this->is_multidimensional_aggregated ) {
				$root = self::$aggregated_multidimensionals[ $this->type ][ $id_base ]['root_value'];
				$value = $this->multidimensional_get( $root, $this->id_data['keys'], $undefined );
			} else {
				$default = $this->default;
				$this->default = $undefined; // Temporarily set default to undefined so we can detect if existing value is set.
				$value = $this->value();
				$this->default = $default;
			}
			$needs_preview = ( $undefined === $value ); // Because the default needs to be supplied.
		}

		// If the setting does not need previewing now, defer to when it has a value to preview.
		if ( ! $needs_preview ) {
			if ( ! has_action( "customize_post_value_set_{$this->id}", array( $this, 'preview' ) ) ) {
				add_action( "customize_post_value_set_{$this->id}", array( $this, 'preview' ) );
			}
			return false;
		}

		switch ( $this->type ) {
			case 'post_meta' :
				add_filter( "post_meta_customize_setting_$this->id", array( $this, '_preview_filter' ) );
				break;
			default :

				/**
				 * Fires when the WP_Customize_Setting::preview() method is called for settings
				 * not handled as theme_mods or options.
				 *
				 * The dynamic portion of the hook name, `$this->id`, refers to the setting ID.
				 *
				 * @since 3.4.0
				 *
				 * @param WP_Customize_Setting $this WP_Customize_Setting instance.
				 */
				do_action( "customize_preview_{$this->id}", $this );

				/**
				 * Fires when the WP_Customize_Setting::preview() method is called for settings
				 * not handled as theme_mods or options.
				 *
				 * The dynamic portion of the hook name, `$this->type`, refers to the setting type.
				 *
				 * @since 4.1.0
				 *
				 * @param WP_Customize_Setting $this WP_Customize_Setting instance.
				 */
				do_action( "customize_preview_{$this->type}", $this );
		}

		$this->is_previewed = true;

		return true;
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

		$post_id = empty( $_GET['post_id'] ) ? $_COOKIE['shramee_post_meta_customize_setting_post_id'] : $_GET['post_id'];

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
		if ( empty( $_GET['post_id'] ) ) {
			return '';
		}

		if ( ! headers_sent() ) {
			setcookie( 'shramee_post_meta_customize_setting_post_id', $_GET['post_id'] );
		}

		$values = get_post_meta(
			$_GET['post_id'],
			$this->id_data['base'],
			true
		);

		return $this->multidimensional_get( $values, $this->id_data['keys'], $this->default );
	}
}
