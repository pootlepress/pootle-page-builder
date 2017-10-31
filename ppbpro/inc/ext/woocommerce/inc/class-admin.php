<?php

/**
 * Pootle Slider Admin class
 * @property string $token Plugin token
 * @property string $url Plugin root dir url
 * @property string $path Plugin root dir path
 * @property string $version Plugin version
 */
class pootle_page_builder_for_WooCommerce_Admin {
	/**
	 * @var  pootle_page_builder_for_WooCommerce_Admin Instance
	 * @access  private
	 * @since  1.0.0
	 */
	private static $_instance = null;
	/** Plugin token */
	public $token;
	/** Plugin url */
	public $url;
	/** Plugin path */
	public $path;
	/** Plugin version */
	public $version;
	/** Plugin version */
	public $wc_attributes;

	/**
	 * Constructor function.
	 * @access  private
	 * @since  1.0.0
	 */
	private function __construct() {
		$this->token   = pootle_page_builder_for_WooCommerce::$token;
		$this->url     = pootle_page_builder_for_WooCommerce::$url;
		$this->path    = pootle_page_builder_for_WooCommerce::$path;
		$this->version = pootle_page_builder_for_WooCommerce::$version;
	} // End instance()

	/**
	 * Main Pootle Slider Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @return pootle_page_builder_for_WooCommerce instance
	 * @since  1.0.0
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	} // End __construct()

	public function admin_enqueue() {
		wp_enqueue_script( $this->token . '-admin-js', $this->url . '/assets/admin.js', array( 'jquery' ) );

		//Getting attributes and filters
		$this->wc_attributes = array( '' => __( 'Please Choose...', 'pootle-page-builder' ) );
		$filters             = array();
		foreach ( get_object_taxonomies( 'product', 'object' ) as $tax ) {
			if ( 0 === strpos( $tax->name, 'pa_' ) ) {

				$this->wc_attributes[ $tax->name ] = $tax->labels->name;
				foreach ( get_categories( array( 'type'       => 'product',
																				 'taxonomy'   => $tax->name,
																				 'hide_empty' => false
				) ) as $term
				) {
					$filters[ $tax->name ][ $term->slug ] = $term->name;
				}
			}
		}

		//Passing attributes and filters to js
		wp_localize_script( $this->token . '-admin-js', 'ppb_wc_filters', $filters );

	}

	/**
	 * Adds wc post types to pb supported post types
	 *
	 * @param $posts
	 *
	 * @action pootlepb_builder_post_types
	 * @return array
	 */
	public function add_wc_posts( $posts ) {
		if ( post_type_exists( 'product' ) ) {
			$posts[] = 'product';
		}

		if ( post_type_exists( 'wc_product_tab' ) ) {
			$posts[] = 'wc_product_tab';
		}

		return $posts;
	}

	/**
	 * Adds Product tab
	 * @action pootlepb_content_block_tabs
	 */
	public function add_tab( $tabs ) {
		$tabs['wc_prods'] = array(
			'label'    => __( 'Products', 'pootle-page-builder' ),
			'priority' => 2,
			'icon'     => 'dashicons-cart',
		);

		return $tabs;
	}

	/**
	 * Adds wc_prods content block fields
	 *
	 * @param array $f Fields
	 *
	 * @return array Tabs
	 */
	public function content_block_fields( $f ) {

		if ( ! class_exists( 'WooCommerce' ) ) {
			return $f;
		}

		$p_cats = array();
		$c_cats = array();

		foreach ( get_categories( array( 'type' => 'product', 'taxonomy' => 'product_cat' ) ) as $cat ) {
			$p_cats[ $cat->slug ]    = $cat->name;
			$c_cats[ $cat->term_id ] = $cat->name;
		}

		$products = array();

		$query = new WP_Query( array(
			'post_type'      => 'product',
			'posts_per_page' => - 1,
		) );
		if ( $query->have_posts() ) {
			foreach ( $query->posts as $post ) {
				$products[ $post->ID ] = $post->post_title;
			}
		} else {
			$products[''] = __( 'No products found', 'pootle-page-builder' );
		}
		wp_reset_postdata();

		$f['wc_prods-add']              = array(
			'name'     => __( 'Add', 'pootle-page-builder' ),
			'tab'      => 'wc_prods',
			'type'     => 'select',
			'priority' => 5,
			'options'  => array(
				''                      => __( 'Please choose...', 'pootle-page-builder' ),
				'product_categories'    => __( 'Product categories', 'pootle-page-builder' ),
				'products'              => __( 'Individual products', 'pootle-page-builder' ),
				'product_category'      => __( 'Products by category', 'pootle-page-builder' ),
				'sale_products'         => __( 'Products on sale', 'pootle-page-builder' ),
				'best_selling_products' => __( 'Best selling products', 'pootle-page-builder' ),
				'featured_products'     => __( 'Featured products', 'pootle-page-builder' ),
				'top_rated_products'    => __( 'Top rated products', 'pootle-page-builder' ),
				'product_attribute'     => __( 'Products by attribute', 'pootle-page-builder' ),
			),
			'default'  => '',
		);
		$f['wc_prods-display']          = array(
			'name'     => __( 'Display as', 'ppb-woocommerce' ),
			'tab'      => 'wc_prods',
			'type'     => 'select',
			'priority' => 10,
			'options'  => array(
				''         => __( 'Grid', 'pootle-page-builder' ),
				'carousel' => __( 'Carousel', 'pootle-page-builder' ),
			),
			'default'  => '',
		);
		$f['wc_prods-category']         = array(
			'name'     => __( 'Category', 'vantage' ),
			'tab'      => 'wc_prods',
			'type'     => 'multi-select',
			'options'  => $p_cats,
			'priority' => 15,
			'default'  => '',
		);
		$f['wc_prods-catids']           = array(
			'name'     => __( 'Category', 'vantage' ),
			'tab'      => 'wc_prods',
			'type'     => 'multi-select',
			'options'  => $c_cats,
			'priority' => 20,
			'default'  => '',
		);
		$f['wc_prods-attribute']        = array(
			'name'     => __( 'Attribute', 'vantage' ),
			'tab'      => 'wc_prods',
			'type'     => 'select',
			'options'  => $this->wc_attributes,
			'priority' => 25,
			'default'  => '',
		);
		$f['wc_prods-filter']           = array(
			'name'     => __( 'Filters', 'vantage' ),
			'tab'      => 'wc_prods',
			'type'     => 'multi-select',
			'options'  => array(),
			'priority' => 30,
			'default'  => '',
		);
		$f['wc_prods-ids']              = array(
			'name'     => __( 'Product IDs', 'vantage' ),
			'tab'      => 'wc_prods',
			'type'     => 'multi-select',
			'options'  => $products,
			'priority' => 35,
			'default'  => '',
		);
		$f['wc_prods-per_page']         = array(
			'name'     => __( 'Number of products', 'ppb-woocommerce' ),
			'tab'      => 'wc_prods',
			'type'     => 'number',
			'priority' => 40,
			'min'      => '1',
			'max'      => '25',
			'step'     => '1',
		);
		$f['wc_prods-columns']          = array(
			'name'     => __( 'Number of columns', 'ppb-woocommerce' ),
			'tab'      => 'wc_prods',
			'type'     => 'number',
			'priority' => 45,
			'min'      => '1',
			'max'      => '4',
			'step'     => '1',
		);
		$f['wc_prods-orderby']          = array(
			'name'     => __( 'Order By', 'ppb-woocommerce' ),
			'tab'      => 'wc_prods',
			'type'     => 'select',
			'priority' => 50,
			'options'  => array(
				''           => __( 'Default', 'pootle-page-builder' ),
				'title'      => __( 'Title', 'pootle-page-builder' ),
				'date'       => __( 'Date', 'pootle-page-builder' ),
				'menu_order' => __( 'Menu Order', 'pootle-page-builder' ),
				'id'         => __( 'ID', 'pootle-page-builder' ),
				'rand'       => __( 'Random', 'pootle-page-builder' ),
			),
			'default'  => '',
		);
		$f['wc_prods-order']            = array(
			'name'     => __( 'Order', 'ppb-woocommerce' ),
			'tab'      => 'wc_prods',
			'type'     => 'select',
			'priority' => 55,
			'options'  => array(
				''     => __( 'Default', 'pootle-page-builder' ),
				'asc'  => __( 'Ascending', 'pootle-page-builder' ),
				'desc' => __( 'Descending', 'pootle-page-builder' ),
			),
			'default'  => '',
		);
		$f['wc_prods-hide_price']       = array(
			'name'     => __( 'Hide price', 'ppb-woocommerce' ),
			'tab'      => 'wc_prods',
			'selector' => 'li.product .price',
			'css'      => 'display:none;ignore',
			'type'     => 'checkbox',
			'priority' => 60,
		);
		$f['wc_prods-hide_title']       = array(
			'name'     => __( 'Hide product name', 'ppb-woocommerce' ),
			'tab'      => 'wc_prods',
			'selector' => 'li.product h3, li.product h2',
			'css'      => 'display:none!important;ignore',
			'type'     => 'checkbox',
			'priority' => 65,
		);
		$f['wc_prods-star_rating']      = array(
			'name'     => __( 'Hide ratings', 'ppb-woocommerce' ),
			'tab'      => 'wc_prods',
			'selector' => 'li.product .star-rating',
			'css'      => 'display:none;ignore',
			'type'     => 'checkbox',
			'priority' => 70,
		);
		$f['wc_prods-hide_add_to_cart'] = array(
			'name'     => __( 'Hide add to cart', 'ppb-woocommerce' ),
			'tab'      => 'wc_prods',
			'selector' => 'li.product .add_to_cart_button',
			'css'      => 'display:none;ignore',
			'type'     => 'checkbox',
			'priority' => 75,
		);

		return $f;
	}

	/**
	 * Adds the products to the content block
	 * @action pootlepb_content_block_wc_prods_tab_after_fields
	 */
	public function wc_required_notice() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			?>
			<div class="field">
				<b>
					<?php _e( 'WooCommerce needs to be installed and activated for pootle page builder add on to work.', 'pootle-page-builder' ); ?>
				</b>
			</div>
			<?php
			return;
		}
	}
}