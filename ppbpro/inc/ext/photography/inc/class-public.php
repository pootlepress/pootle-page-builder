<?php

/**
 * Pootle page builder Photography add on public class
 * @property string $token Plugin token
 * @property string $url Plugin root dir url
 * @property string $path Plugin root dir path
 * @property string $version Plugin version
 */
class page_builder_photo_addon_Public{

	/**
	 * @var 	page_builder_photo_addon_Public Instance
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;
	protected $all_sets;
	protected $set;
	protected $attr;
	protected $method;
	protected $id = 0;

	/**
	 * Main Pootle page builder Photography add on Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @since 1.0.0
	 * @return page_builder_photo_addon_Public instance
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
		$this->token   =   page_builder_photo_addon::$token;
		$this->url     =   page_builder_photo_addon::$url;
		$this->path    =   page_builder_photo_addon::$path;
		$this->version =   page_builder_photo_addon::$version;
	} // End __construct()

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 * @since 1.0.0
	 */
	public function enqueue() {
		$token = $this->token;
		$url = $this->url;
		wp_enqueue_script( 'ppb-masonry', $url . '/assets/masonry.pkgd.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'ppb-imgloaded', $url . '/assets/imagesloaded.pkgd.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'ppb-flex-slider', $url . '/assets/jquery.flexslider.min.js', array( 'jquery' ) );

		wp_enqueue_style( $token . '-css', $url . '/assets/front-end.css' );
		wp_enqueue_script( $token . '-js', $url . '/assets/front-end.js',
			array( 'ppb-masonry', 'ppb-flex-slider', 'ppb-imgloaded', 'jquery' ) );
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
		if ( ! empty( $settings[ $this->token . '_sample_color' ] ) ) {
			$attr['style'] .= 'outline: 12px solid ' . $settings[ $this->token . '_sample_color' ] . ';';
		}
		return $attr;
	}

	public function gallery_or_slider( $info ) {

		$this->all_sets = $set = json_decode( $info['info']['style'], true );

		if ( ! empty( $set[ $this->token . '_show' ] ) ) {
			if ( method_exists( $this, $method = 'show_' . $set[ $this->token . '_show' ] ) ) {

				$this->id ++;
				$this->method = $method;

				$defaults = array(
					'max' => get_option( 'posts_per_page' ),
					'size' => 'medium',
				);

				$set = $this->set = wp_parse_args( $this->get_photo_settings( $set, $set[ $this->token . '_show' ] ), $defaults );
				echo '<div ' . $this->attr() . ' class="ppb-photo ppb-photo-' . $set['show'] . '">';

				if ( empty( str_replace( 'unsplash', '', $set['source_type'] ) ) ) {
					$this->img_from_links( json_decode( $set['source_data'], true ) );
				} elseif ( method_exists( $this, $get_img_func = 'img_from_' . $set['source_type'] ) ) {
					$this->$get_img_func( $set );
				}
				echo '</div>';
			}
		}
	}

	/**
	 * Gets blog customizer settings from ppb content block settings
	 * @param array $set Content block settings
	 * @return array blog customizer settings
	 * @since 1.0.0
	 */
	private function get_photo_settings( $set, $show ) {
		$settings = array();
		$attr = array();
		foreach ( $set as $k => $v ) {
			if ( 0 === strpos( $k, $this->token ) ) {
				$nk = str_replace( $this->token . '_', '', $k );
				$settings[ $nk ] = $v;
				if ( strpos( $k, $show . '_attr' ) ) {
					$attr[ str_replace( $show . '_attr_', '', $nk ) ] = $v;
				}
			}
		}
		$this->attr = $attr;
		return $settings;
	}

	public function attr() {
		$attrs = $this->attr;
		$ret = '';
		foreach ( $attrs as $p => $v ) {
			$ret .= "data-$p='$v'";
		}
		return $ret;
	}

	public function img_from_links( $images ) {
		$img_data = array();
		foreach ( $images as $img ) {
			$id = $this->att_id( $img );
			$title= '';
			if ( $id ) {
				$title = get_the_title( $id );
			}
			$img_data[] = array(
				'post_id' => $id,
				'id' => $id,
				'img' => $img,
				'title' => $title,
			);
		}
		$method = $this->method;
		$this->$method( $img_data );
	}

	public function img_from_cat( $set ) {
		$args = array(
			'cat'      => implode( ',', $set['source_cats'] ),
			'meta_key' => '_thumbnail_id',
		);
		$this->query_slides( $args );
	}

	public function img_from_tax( $set ) {
		$args = array(
			'tax_query' => array(
				array(
					'taxonomy' => $set['source_taxes'],
					'field'    => 'term_id',
					'terms'    => $set[ 'source_' . $set['source_taxes'] ],
				),
			),
		);
		$this->query_slides( $args );
	}

	public function img_from_rcnt_posts( $set ) {
		$args = array(
			'post_type'			=> 'post',
		);
		$this->query_slides( $args );
	}

	protected function query_slides( $args = array(), $message = 'Sorry, no posts found...' ) {
		$this->filter_query_args( $args );
		$query  = new WP_Query( $args );
		$images = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) : $query->the_post();
				$id = get_post_thumbnail_id();
				$img = wp_get_attachment_image_src( $id, $this->set['size'] );
				$images[] = array(
					'post_id' => get_the_ID(),
					'id' => $id,
					'img' => $img[0],
					'title' => get_the_title(),
				);
			endwhile;
			wp_reset_postdata();
			$method = $this->method;
			$this->$method( $images );
		} else {
			echo '<p>' . $message . '</p>';
		}
	}

	private function att_id( $url ) {
		global $wpdb;
		$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $url ));

		if ( ! count( $attachment ) ) {
			return false;
		}

		return $attachment[0];
	}

	private function filter_query_args( &$args ) {
		$args = wp_parse_args( $args, array(
			'meta_key'			=> '_thumbnail_id',
			'post_type'			=> 'any',
			'posts_per_page'	=> $this->set['max'],
		) );
	}

	private function show_slider( $images ) {
		if ( count( $images ) ) {
			if ( ! empty( $this->set['max'] ) && $this->set['max'] <= count( $images ) ) {
				$images = array_slice( $images, 0, $this->set['max'] );
			}
			echo '<ul class="slides">';
			foreach ( $images as $img ) {
				$caption = '';
				if ( ! empty( $img['title'] ) ) {
					$caption = '<p class="ppb-photo-caption">' . $img['title'] . '</p>';
				}
				//Output image and caption
				echo "<li><div class='ppb-photo-hd-ratio'><img class='{$this->attr['animation']}' src='{$img['img']}'></div>$caption</li>";
			}
			echo '</ul>';
		}
	}

	public function show_gallery( $images ) {
		if ( count( $images ) ) {
			if ( ! empty( $this->set['max'] ) && $this->set['max'] <= count( $images ) ) {
				$images = array_slice( $images, 0, $this->set['max'] );
			}
			if ( 'photo-listing' == $this->attr['type'] ) {
				echo
					'<div class="controls">' .
					'<span class="control control-full"></span>' .
					'<span class="control control-blocks active"></span>' .
					'</div>';
			}
			list( $pre, $suff ) = $this->get_gallery_prefix_suffix( $this->set );
			echo '<div class="ppb-photo-gallery-items">';
			foreach ( $images as $img ) {
				$caption = '';
				$url = wp_get_attachment_image_src( $img['id'], $this->set['size'] );
				$url = $url ? $url[0] : $img['img'];
				if ( ! empty( $img['title'] ) ) {
					$caption = '<p class="ppb-photo-caption">' . $img['title'] . '</p>';
				}
				//Output image and caption
				if ( empty( $this->attr['type'] ) ) {
					$img_tag = "<div class='img' style='background-image: url($url);'></div>";
				} else if ( 'photo-listing' == $this->attr['type'] ) {
					$img_tag = "<img src='$url'>";
				} else {
					$img_tag = "<img src='$url'>";
				}

				$pre = str_replace( '%full_img_url%', $img['img'], $pre );

				$permalink = get_permalink( $img['post_id'] );
				$pre = str_replace( '%permalink%', $permalink, $pre );

				echo "<div class='ppb-photo-gallery-item-wrap'>$pre<div class='ppb-photo-gallery-item'>{$img_tag}{$caption}</div>$suff</div>";

				$pre = str_replace( $img['img'], '%full_img_url%', $pre );
				$pre = str_replace( $permalink, '%permalink%', $pre );
			}
			echo '</div>';
		}
	}

	protected function get_gallery_prefix_suffix( $set ) {
		$prefix = '';
		$suffix = '';
		$link = $set['gallery_link'];

		if ( $link ) {
			$prefix = '<a';
			$suffix = '</a>';
			$prefix .= $this->add_gallery_link( $link );
			if ( $set['gallery_link_target'] ) {
				$prefix .= ' target="_blank"';
			}
			$prefix .= '>';
		}

		return array( $prefix, $suffix, );
	}

	protected function add_gallery_link( $set ) {
		$ret = '';
		switch ( $set ) {
			case 'img':
				$ret = ' href="%full_img_url%"';
				break;
			case 'lightbox':
				add_thickbox();
				$ret = " href='%full_img_url%' class='thickbox' rel='ppb-photo-gallery-$this->id'";
				break;
			case 'post':
				$ret = " href='%permalink%'";
				break;
		}
		return $ret;
	}

	/**
	 * Adds or modifies the row attributes
	 * @param array $attr Row html attributes
	 * @param array $settings Row settings
	 * @return array Row html attributes
	 * @filter pootlepb_row_style_attributes
	 * @since 1.0.0
	 */
	public function content_block_attr( $attr, $settings ) {
		if ( ! empty( $settings[ $this->token . '_sample_number' ] ) ) {
			$attr['style'] .= 'position: relative;';
			$attr['style'] .= 'left: ' . $settings[ $this->token . '_sample_number' ] . 'em;';
			$attr['style'] .= 'top: ' . $settings[ $this->token . '_sample_number' ] . 'em;';
		}
		return $attr;
	}
}