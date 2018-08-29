<?php

/** Loop methods */
require 'class-public-loop.php';

/**
 * pootle page builder blog customizer public class
 */
class pootle_page_builder_blog_customizer_Public extends pootle_page_builder_blog_customizer_Public_Loop {
	/**
	 * @var  pootle_page_builder_blog_customizer_Public Instance
	 * @access  private
	 * @since  1.0.0
	 */
	private static $_instance = null;
	/**
	 * @var int $loop_id Current loop id
	 */
	public $loop_id = 0;
	/**
	 * @var int $loop_css_id Current loop css id
	 */
	public $loop_css_id;
	/** @var int ID of post to exclude */
	private $exclude_post = 0;

	/**
	 * Constructor function.
	 * @access  private
	 * @since   1.0.0
	 */
	private function __construct() {
		$this->token   = pootle_page_builder_blog_customizer::$token;
		$this->url     = pootle_page_builder_blog_customizer::$url;
		$this->path    = pootle_page_builder_blog_customizer::$path;
		$this->version = pootle_page_builder_blog_customizer::$version;
	} // End instance()

	/**
	 * Main pootle page builder blog customizer Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @since 1.0.0
	 * @return pootle_page_builder_blog_customizer instance
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	} // End __construct()

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 * @since 1.0.0
	 */
	public function enqueue() {
		$token = $this->token;
		$url   = $this->url;

		$this->id = get_the_ID();

		wp_enqueue_style( $token . '-css', $url . '/assets/front-end.css' );
		wp_enqueue_script( $token . '-js', $url . '/assets/front-end.js', array( 'jquery' ) );
	}

	/**
	 * Sets the number of words in excerpt
	 * @return int 25
	 * @since 1.0.0
	 */
	public function excerpt_length() {
		return 70;
		//Later reduce the number of characters to
	}

	/**
	 * Adds or modifies the row attributes
	 *
	 * @param array $attr Row html attributes
	 * @param array $settings Row settings
	 *
	 * @return array Row html attributes
	 * @filter pootlepb_row_style_attributes
	 * @since 1.0.0
	 */
	public function show_posts( $info ) {
		if ( 'post' == get_post_type() ) {
			$this->exclude_post = get_the_ID();
		}
		$set     = json_decode( $info['info']['style'], true ); //Decode settings in JSON
		$pc_sets = $this->get_custom_posts_settings( $set ); //Grab blog customizer settings
		if ( ! empty( $pc_sets['down'] ) && ! empty( $pc_sets['across'] ) ) {
			$query_args = $this->query_args( $pc_sets );
			$query      = new WP_Query( $query_args );

			if ( $query->have_posts() ) {
				//Increment loop ID
				$this->loop_id ++;
				//Get 25 words in excerpt
				add_filter( 'excerpt_length', array( $this, 'excerpt_length' ), 999 );
				//Create unique css id for current loop
				$this->loop_css_id = 'ppb-posts-' . $this->id . '-' . $this->loop_id;
				//Run the loop
				$this->loop( $query, $pc_sets );
				if ( $query->max_num_pages > 1 && $pc_sets['pagination'] ) {
					$page = isset( $_GET['ppb_posts_page'] ) ? $_GET['ppb_posts_page'] : 1;
					?>
					<nav class="ppb-posts-prev-next-posts">
						<?php if ( $query->max_num_pages > $page ) {
							?>
							<div class="prev-posts-link">
								<a href="<?php echo add_query_arg( 'ppb_posts_page', $page + 1 ) ?>">
									<?php _e( 'Previous', 'ppb-panels' ) ?>
								</a>
							</div>
							<?php
						} ?>
						<?php if ( $page > 1 ) {
							?>
							<div class="next-posts-link">
								<a href="<?php echo add_query_arg( 'ppb_posts_page', $page - 1 ) ?>">
									<?php _e( 'Next', 'ppb-panels' ) ?>
								</a>
							</div>
							<?php
						} ?>
					</nav>
				<?php }
				//Set excerpt back to normal
				remove_filter( 'excerpt_length', array( $this, 'excerpt_length' ), 999 );
			}

			wp_reset_query();
		}
	}

	/**
	 * Gets blog customizer settings from ppb content block settings
	 *
	 * @param array $set Content block settings
	 *
	 * @return array blog customizer settings
	 * @since 1.0.0
	 */
	private function get_custom_posts_settings( $set ) {

		$settings = array();

		$fields = pootle_page_builder_blog_customizer_Admin::instance()->content_block_fields( array() );

		foreach ( $fields as $k => $f ) {
			if ( 'border' == $f['type'] ) {
				$this->add_setting( $settings, $set, $k, array( '-width', '-color' ) );
			}
			if ( 'post-display' == $f['type'] ) {
				$this->add_setting( $settings, $set, $k, array( '-across', '-down' ) );
			}
			$this->add_setting( $settings, $set, $k );
		}

		return $settings;
	}

	/**
	 * Adds post custo setting from $set to $settings
	 *
	 * @param array $settings Post custo settings array, passed by reference
	 * @param array $set Content block settings
	 * @param string $k Settings key
	 * @param array $multi Suffixes for multi options
	 */
	private function add_setting( &$settings, $set, $k, $multi = array( '' ) ) {
		foreach ( $multi as $suffix ) {
			$settings[ str_replace( $this->token . '-', '', $k . $suffix ) ] = pootlepb_array_key_value( $set, $k . $suffix );
		}
	}

	/**
	 * Creates query args from settings
	 *
	 * @param array $set
	 *
	 * @return array WP_Query Args
	 */
	private function query_args( $set ) {
		$ppp = $set['down'] * $set['across']; //Posts Per Page

		$paged = 1;

		if ( isset( $_GET['ppb_posts_page'] ) ) {
			$paged = $_GET['ppb_posts_page'];
		}

		$args = array(
			'posts_per_page' => $ppp,
			'orderby'        => $set['orderby'],
			'paged'          => $paged,
			'post__not_in'   => [ $this->exclude_post ],
		);

		if ( is_array( $set['cat'] ) ) {
			$args['cat'] = implode( ',', $set['cat'] );
		}

		if ( ! empty( $set['image-posts-only'] ) ) {
			$args['meta_key'] = '_thumbnail_id';
		}

		return $args;
	}

}