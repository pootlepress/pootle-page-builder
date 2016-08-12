<?php

/**
 * Pootle Slider public class
 * @property string $token Plugin token
 * @property string $url Plugin root dir url
 * @property string $path Plugin root dir path
 * @property string $version Plugin version
 */
class pootle_page_builder_for_WooCommerce_Public{

	private $parameters = array(
		'category',
		'ids',
		'attribute',
		'filter',
		'per_page',
		'columns',
		'orderby',
		'order',
		'catids',
	);
	/**
	 * @var 	pootle_page_builder_for_WooCommerce_Public Instance
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Main Pootle Slider Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @since 1.0.0
	 * @return pootle_page_builder_for_WooCommerce instance
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
		$this->token   =   pootle_page_builder_for_WooCommerce::$token;
		$this->url     =   pootle_page_builder_for_WooCommerce::$url;
		$this->path    =   pootle_page_builder_for_WooCommerce::$path;
		$this->version =   pootle_page_builder_for_WooCommerce::$version;
	} // End __construct()

	/**
	 * Adds front end stylesheet and js
	 * @since 0.1.0
	 */
	public function enqueue() {
		$token = $this->token;
		$url = $this->url;

		wp_enqueue_style( $token . '-css', $url . '/assets/front-end.css' );
		wp_enqueue_script( $token . '-js', $url . '/assets/front-end.js', array( 'jquery' ) );
	} // End enqueue()

	/**
	 * Handles the rendering of the tabs
	 * @param $tab_content
	 * @param $tab
	 * @return string Tab content
	 * @action woocommerce_tab_manager_tab_panel_content
	 */
	public function wc_tabs_filter( $tab_content, $tab ) {

		global $Pootle_Page_Builder_Render_Layout;
		$post = get_post( $tab['id'] );
		$tab_content = $post->post_content;

		$pootlepb = $Pootle_Page_Builder_Render_Layout->panels_render( $tab['id'] );

		if ( ! empty( $pootlepb ) ) {
			$tab_content = $pootlepb;
		}

		return $tab_content;

	}

	/**
	 * Adds the products to the content block
	 * @action pootlepb_render_content_block
	 * @param $info
	 */
	public function render_products( $info ) {
		$set = json_decode( $info['info']['style'], true );

		if ( ! empty( $set['wc_prods-add'] ) ) {

			$short_code = "[{$set['wc_prods-add']}";

			if ( 'products' == $set['wc_prods-add'] ) {
				//$set['wc_prods-per_page'] = 999;
			}

			$this->set_parameters( $short_code, $set );

			$short_code .= ']';
			echo '<!--' . $short_code . '-->' . do_shortcode( $short_code );
		}
	}

	/**
	 * Sets product ids or categories parameters
	 * @param string $short_code The short code
	 * @param array $set Content block settings
	 */
	private function set_parameters( &$short_code, $set ) {
		foreach ( $this->parameters as $param ) {
			if ( !empty( $set[ 'wc_prods-' . $param ] ) ) {
				$this->add_to_shortcode( $short_code, $param, $set[ 'wc_prods-' . $param ] );
			}
		}
	}

	/**
	 * Sets attribute parameters
	 * @param string $short_code The short code
	 * @param array $set Content block settings
	 */
	private function add_to_shortcode( &$short_code, $param, $value ) {
		if ( $param == 'catids' ) {
			$short_code .= ' ids="' . implode( ',', $value ) . '"';
			return;
		}
		if ( is_array( $value ) ) {
			$short_code .= ' ' . $param . '="' . implode( ',', $value ) . '"';
		} else {
			$short_code .= ' ' . $param . '="' . $value . '"';
		}
	}
}