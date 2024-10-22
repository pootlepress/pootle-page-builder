<?php

/**
 * Pootle Slider public class
 * @property string $token Plugin token
 * @property string $url Plugin root dir url
 * @property string $path Plugin root dir path
 * @property string $version Plugin version
 */
class pootle_page_builder_for_WooCommerce_Public {

	private $i = 1;
	private $id = '';
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

	public string $token;
	public string $url;
	public string $path;
	public string $version;

	/**
	 * Main Pootle Slider Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @since 1.0.0
	 * @return self instance
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
		wp_enqueue_style( 'owl-carousel-css', 'https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css' );
		wp_enqueue_script( 'owl-carousel-js', 'https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js', array( 'jquery' ) );
	} // End enqueue()

	public function maybe_clear_shop_content() {
		// @TODO check if shop page uses ppb
		if ( get_option( 'ppb_wc_shop' ) && function_exists( 'is_shop' ) && is_shop() ) {
			?>
			<style>
				.woocommerce-products-header {
					padding: 0 !important;
				}
				.woocommerce-products-header ~ * {
					display: none;
				}
			</style>
			<?php
			$GLOBALS['woocommerce_loop']['total'] = 0;
			remove_all_actions( 'woocommerce_before_main_content' );
			remove_all_actions( 'woocommerce_archive_description' );
			remove_all_actions( 'woocommerce_before_shop_loop' );
			remove_all_actions( 'woocommerce_shop_loop' );
			remove_all_actions( 'woocommerce_after_shop_loop' );
			remove_all_actions( 'woocommerce_no_products_found' );
			remove_all_actions( 'woocommerce_after_main_content' );
			add_action( 'woocommerce_before_main_content', 'storefront_before_content', 10 );
			add_action( 'woocommerce_after_main_content', 'storefront_after_content', 10 );
//			remove_all_actions( 'woocommerce_sidebar' );
		}
	}

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

		return apply_filters( 'the_content', $tab_content );

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

			$this->id = 'ppb-wc-addon-' . $this->i++;
			echo '<!--' . $short_code . '-->' . "<div id='$this->id'>" . do_shortcode( $short_code );
			$this->display_scripts( $set );
			echo '</div>';
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

	/**
	 * Renders js for display type
	 * @param array $set Content block settings
	 */
	private function display_scripts( $set ) {
		if ( ! empty( $set['wc_prods-display'] ) ) {
			$method = 'display_' . $set['wc_prods-display'] . '_scripts';
			if ( method_exists( $this, $method ) )
				$this->$method( $set );
		}
	}

	/**
	 * Renders js for carousel
	 * @param array $set Content block settings
	 */
	private function display_carousel_scripts( $set ) {
		$items = empty( $set['wc_prods-columns'] ) ? 4 : $set['wc_prods-columns'];
		?>
		<style>
			#<?php echo $this->id ?> ul.products li.product.type-product {
				width: auto;
				margin: 0 auto !important;
				float: none;
				padding: 0 2px !important;
			}
		</style>
		<script>
			( function( $ ) {
				var $car = $( '#<?php echo $this->id ?> ul.products' ),
					acros = <?php echo $items ?>,
					acrosBy2 = <?php echo ( $items - ( $items % 2 ) ) / 2 ?>;
				$car.addClass( 'owl-carousel' ).owlCarousel( {
					navigation: true,
					items : acros,
					itemsDesktop : false,
					itemsDesktopSmall : [ 1060, Math.max( 1, acros - 1, acrosBy2 ) ],
					itemsTablet: [ 790, Math.max( 1, acrosBy2 ) ],
					itemsMobile : [ 430, 1 ] // itemsMobile disabled - inherit from itemsTablet option
				} );
			} )( jQuery );
		</script>
		<?php
	}
}