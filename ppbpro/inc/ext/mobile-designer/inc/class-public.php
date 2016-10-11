<?php

/**
 * Pootle Page Builder Mobile Designer public class
 * @property string $token Plugin token
 * @property string $url Plugin root dir url
 * @property string $path Plugin root dir path
 * @property string $version Plugin version
 */
class PPB_Mobile_Designer_Public{

	/**
	 * @var 	PPB_Mobile_Designer_Public Instance
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Main Pootle Page Builder Mobile Designer Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @since 1.0.0
	 * @return PPB_Mobile_Designer_Public instance
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
		$this->token   =   PPB_Mobile_Designer::$token;
		$this->url     =   PPB_Mobile_Designer::$url;
		$this->path    =   PPB_Mobile_Designer::$path;
		$this->version =   PPB_Mobile_Designer::$version;
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
		wp_enqueue_script( $token . '-js', $url . '/assets/front-end.js', array( 'jquery' ) );
	}

	/**
	 * Adds or modifies the row attributes
	 * @param array $attr Row html attributes
	 * @param array $settings Row settings
	 * @return array Row html attributes
	 * @filter pootlepb_row_style_attributes
	 * @since 1.0.0
	 */
	public function row_attr( $attr, $settings ) {
		if ( ! empty( $settings[ $this->token . '_hide' ] ) ) {
			$attr['class'][] = implode( ' ', $settings[ $this->token . '_hide' ] );
		}
		return $attr;
	}

	/**
	 * Adds or modifies the row attributes
	 * @param array $attr Row html attributes
	 * @param array $settings Row settings
	 * @return array Row html attributes
	 * @filter pootlepb_row_style_attributes
	 * @since 1.0.0
	 */
	public function content_block_styles( $attr, $settings, $id ) {
		$style_data = array(
			'mob' => "@media screen and (max-width:700px){ #$id {",
			'tab' => "@media screen and (min-width:701px) and (max-width:1024px){ #$id {",
			'dsk' => "@media screen and (min-width:1025px){ #$id {",
		);
		foreach ( $settings as $k => $v ) {
			if ( 0 === strpos( $k, $this->token ) && $v ) {
				$keys = explode( '|', $k );
				if ( 3 == count( $keys ) ) {
					$v = is_numeric( $v ) ? $v . 'px' : $v;
					$style_data[ $keys[1] ] .= "{$keys[2]}: $v;";
				}
			}
		}
		$style_data['mob'] .= '}}';
		$style_data['tab'] .= '}}';
		$style_data['dsk'] .= '}}';

		return $attr . implode( '', $style_data );
	}
}