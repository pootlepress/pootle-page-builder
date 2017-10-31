<?php

/**
 * Pootle page builder Photography add on Admin class
 * @property string token Plugin token
 * @property string $url Plugin root dir url
 * @property string $path Plugin root dir path
 * @property string $version Plugin version
 */
class page_builder_photo_addon_Admin {

	/**
	 * @var  page_builder_photo_addon_Admin Instance
	 * @access  private
	 * @since  1.0.0
	 */
	private static $_instance = null;

	/**
	 * Constructor function.
	 * @access  private
	 * @since  1.0.0
	 */
	private function __construct() {
		$this->token   = page_builder_photo_addon::$token;
		$this->url     = page_builder_photo_addon::$url;
		$this->path    = page_builder_photo_addon::$path;
		$this->version = page_builder_photo_addon::$version;
	} // End instance()

	/**
	 * Main Pootle page builder Photography add on Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @return page_builder_photo_addon instance
	 * @since  1.0.0
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	} // End __construct()

	/**
	 * Adds editor panel tab
	 *
	 * @param array $tabs The array of tabs
	 *
	 * @return array Tabs
	 * @filter pootlepb_content_block_tabs
	 * @since  1.0.0
	 */
	public function content_block_tabs( $tabs ) {
		$tabs[ $this->token ] = array(
			'label'    => __( 'Photos', 'pootle-page-builder' ),
			'priority' => 5,
		);

		return $tabs;
	}

	/**
	 * Adds content block panel fields
	 *
	 * @param array $fields Fields to output in content block panel
	 *
	 * @return array Tabs
	 * @filter pootlepb_content_block_fields
	 * @since  1.0.0
	 */
	public function content_block_fields( $fields ) {
		$prefix = $this->token . '_';

		$fields[ $prefix . 'show' ]                        = array(
			'name'     => __( 'Show', 'pootle-page-builder' ),
			'type'     => 'select',
			'options'  => array(
				''        => __( 'Please choose', 'pootle-page-builder' ),
				'slider'  => __( 'Slider', 'pootle-page-builder' ),
				'gallery' => __( 'Gallery', 'pootle-page-builder' ),
			),
			'priority' => 1,
			'tab'      => $this->token,
		);
		$fields[ $prefix . 'source' ]                      = array(
			'name'     => __( 'Source', 'pootle-page-builder' ),
			'type'     => 'slider_source',
			'options'  => array(
				''           => __( 'Media library', 'pootle-page-builder' ),
				'unsplash'   => __( 'Unsplash', 'pootle-page-builder' ),
				'rcnt_posts' => __( 'Recent Posts', 'pootle-page-builder' ),
				'cat'        => __( 'Categories', 'pootle-page-builder' ),
				'tax'        => __( 'Taxonomy', 'pootle-page-builder' ),
			),
			'priority' => 2,
			'tab'      => $this->token,
		);
		$fields[ $prefix . 'max' ]                         = array(
			'name'     => __( 'Max slides', 'pootle-page-builder' ),
			'type'     => 'number',
			'priority' => 3,
			'tab'      => $this->token,
		);
		$fields[ $prefix . 'size' ]                        = array(
			'name'     => __( 'Image size', 'pootle-page-builder' ),
			'type'     => 'select',
			'options'  => array(
				'thumbnail' => __( 'Thumbnail', 'pootle-page-builder' ),
				''          => __( 'Medium', 'pootle-page-builder' ),
				'large'     => __( 'Large', 'pootle-page-builder' ),
			),
			'priority' => 3,
			'tab'      => $this->token,
		);
		$fields[ $prefix . 'slider_attr_animation' ]       = array(
			'name'     => __( 'Slider animation', 'pootle-page-builder' ),
			'type'     => 'select',
			'options'  => array(
				''       => __( 'Fade', 'pootle-page-builder' ),
				'slide'  => __( 'Slide', 'pootle-page-builder' ),
				'kb'     => __( 'Ken Burns', 'pootle-page-builder' ),
				'ribbon' => __( 'Ribbon', 'pootle-page-builder' ),
			),
			'priority' => 4,
			'tab'      => $this->token,
		);
		$fields[ $prefix . 'slider_attr_full_width' ]      = array(
			'name'     => __( 'Stretch to full width of screen', 'pootle-page-builder' ),
			'type'     => 'checkbox',
			'priority' => 5,
			'tab'      => $this->token,
		);
		$fields[ $prefix . 'slider_attr_arrows' ]          = array(
			'name'     => __( 'Slider navigation arrows', 'pootle-page-builder' ),
			'type'     => 'checkbox',
			'priority' => 7,
			'tab'      => $this->token,
		);
		$fields[ $prefix . 'slider_attr_pagination' ]      = array(
			'name'     => __( 'Slider pagination', 'pootle-page-builder' ),
			'type'     => 'checkbox',
			'priority' => 7,
			'tab'      => $this->token,
		);
		$fields[ $prefix . 'slider_attr_title' ]           = array(
			'name'     => __( 'Show title', 'pootle-page-builder' ),
			'type'     => 'checkbox',
			'priority' => 7,
			'tab'      => $this->token,
		);
		$fields[ $prefix . 'slider_attr_autoplay' ]        = array(
			'name'      => __( 'Autoplay speed', 'pootle-page-builder' ),
			'type'      => 'number',
			'unit'      => 'ms (1/1000 second)',
			'help-text' => __( 'Default 5000ms.', 'pootle-page-builder' ),
			'priority'  => 8,
			'tab'       => $this->token,
		);
		$fields[ $prefix . 'slider_attr_animation_speed' ] = array(
			'name'      => __( 'Animation speed', 'pootle-page-builder' ),
			'type'      => 'number',
			'unit'      => 'ms (1/1000 second)',
			'help-text' => __( 'Default 500ms.', 'pootle-page-builder' ),
			'priority'  => 9,
			'tab'       => $this->token,
		);
		$fields[ $prefix . 'gallery_attr_type' ]           = array(
			'name'     => __( 'Gallery type', 'pootle-page-builder' ),
			'type'     => 'select',
			'options'  => array(
				''              => __( 'Normal Grid', 'pootle-page-builder' ),
				'masonry'       => __( 'Masonry', 'pootle-page-builder' ),
				'photo-listing' => __( 'Photo Listing', 'pootle-page-builder' ),
			),
			'priority' => 3,
			'tab'      => $this->token,
		);
		$fields[ $prefix . 'gallery_attr_cols' ]           = array(
			'name'     => __( 'Columns', 'pootle-page-builder' ),
			'type'     => 'select',
			'options'  => array(
				'' => __( 'Default', 'pootle-page-builder' ),
				1  => __( 1, 'pootle-page-builder' ),
				2  => __( 2, 'pootle-page-builder' ),
				3  => __( 3, 'pootle-page-builder' ),
				4  => __( 4, 'pootle-page-builder' ),
				5  => __( 5, 'pootle-page-builder' ),
				6  => __( 6, 'pootle-page-builder' ),
				7  => __( 7, 'pootle-page-builder' ),
				8  => __( 8, 'pootle-page-builder' ),
				9  => __( 9, 'pootle-page-builder' ),
				10 => __( 10, 'pootle-page-builder' ),
				11 => __( 11, 'pootle-page-builder' ),
				12 => __( 12, 'pootle-page-builder' ),
			),
			'priority' => 4,
			'tab'      => $this->token,
		);
		$fields[ $prefix . 'gallery_attr_full_width' ]     = array(
			'name'     => __( 'Stretch to full width of screen', 'pootle-page-builder' ),
			'type'     => 'checkbox',
			'priority' => 5,
			'tab'      => $this->token,
		);
		$fields[ $prefix . 'gallery_attr_title' ]          = array(
			'name'     => __( 'Show title', 'pootle-page-builder' ),
			'type'     => 'select',
			'options'  => array(
				''  => __( 'Never', 'pootle-page-builder' ),
				'1' => __( 'Always', 'pootle-page-builder' ),
				'2' => __( 'On mouse over', 'pootle-page-builder' ),
			),
			'priority' => 5,
			'tab'      => $this->token,
		);
		/*
				$fields[ $prefix . 'gallery_attr_title_hide_mobile' ] = array(
					'name' => 'Hide title on mobile devices',
					'type' => 'checkbox',
					'priority' => 6,
					'tab' => $this->token,
				);
		*/
		$fields[ $prefix . 'gallery_link' ]        = array(
			'name'     => __( 'Link', 'pootle-page-builder' ),
			'type'     => 'select',
			'options'  => array(
				''         => __( 'No link', 'pootle-page-builder' ),
				'img'      => __( 'Large image', 'pootle-page-builder' ),
				'lightbox' => __( 'Lightbox', 'pootle-page-builder' ),
				'post'     => __( 'Post page', 'pootle-page-builder' ),
			),
			'priority' => 7,
			'tab'      => $this->token,
		);
		$fields[ $prefix . 'gallery_link_target' ] = array(
			'name'     => __( 'Link Target', 'pootle-page-builder' ),
			'type'     => 'select',
			'options'  => array(
				''       => __( 'Same window', 'pootle-page-builder' ),
				'_blank' => __( 'New window', 'pootle-page-builder' ),
			),
			'priority' => 8,
			'tab'      => $this->token,
		);

		return $fields;
	}


	/**
	 * Enqueue admin scripts
	 * @filter pootlepb_enqueue_admin_scripts
	 * @since  1.0.0
	 */
	public function enqueue() {
		$token = $this->token;
		$url   = $this->url;

		wp_enqueue_script( $token . '-admin-js', $url . '/assets/admin.js', array( 'jquery' ) );
		wp_enqueue_style( $token . '-admin-css', $url . '/assets/admin.css' );
	}

	/**
	 *
	 */
	public function source_field_render( $key, $field ) {
		$field['type'] = 'select';
		$key           = esc_attr( $key );
		pootlepb_render_content_block_field( $key . '_type', $field );

		$input_attrs = "dialog-field='{$key}_data' class='content-block-{$key}_data' ";
		echo
		"<input type='hidden' $input_attrs data-style-field-type='text' />";
		?>
		<div class="photo-source spaced-up photo-" style="display: block;">
			<button class='button photo-select-images'><?php _e( 'Select Images', 'pootle-page-builder' ); ?></button>
		</div>
		<div class="photo-source spaced-up photo-unsplash" style="display: block;">
			<input type="search">
			<button class='button photo-select-unsplash'><?php _e( 'Search Unsplash', 'pootle-page-builder' ); ?></button>
		</div>
		<div class="photo-images photo-source spaced-up photo- photo-unsplash"></div>
		<div class="photo-source spaced-up photo-cat" style="display: none;">
			<?php $this->output_terms_select( 'category', $key . '_cats', $field, 'Categories' ) ?>
		</div>
		<div class="photo-source spaced-up photo-tax" style="display: none;">
			<?php
			$args             = array(
				'public'   => true,
				'_builtin' => false
			);
			$taxonomies       = get_taxonomies( $args, 'objects' );
			$field['type']    = 'select';
			$field['options'] = array();
			if ( $taxonomies ) {
				$field['options'][''] = __( 'Choose...', 'pootle-page-builder' );
				foreach ( $taxonomies as $tax ) {
					$field['options'][ $tax->name ] = $tax->label;
				}
				pootlepb_render_content_block_field( $key . '_taxes', $field );
				unset( $field['options'][''] );
				foreach ( $field['options'] as $taxonomy => $lbl ) {
					echo '<div class="photo_tax_terms photo_tax_terms_' . $taxonomy . ' spaced-up">';
					$this->output_terms_select( $taxonomy, $key . '_' . $taxonomy, $field, $lbl );
					echo '</div>';
				}
			} else {
				echo '<p>' . __( 'No public custom taxonomies found!', 'pootle-page-builder' ) . '</p>';
			}
			?>
		</div>
		<?php
	}

	protected function output_terms_select( $taxonomy, $key, $field, $label ) {
		$field['type']        = 'multi-select';
		$field['placeholder'] = sprintf( __( 'Choose Some %s...', 'pootle-page-builder' ), $label );
		$field['options']     = array();
		$terms                = get_terms( $taxonomy );
		if ( ! $terms || $terms instanceof WP_Error ) {
			echo '<p>' . sprintf( __( 'No %s found!...', 'pootle-page-builder' ), $label ) . '</p></span>';

			return;
		}
		foreach ( $terms as $term ) {
			$field['options'][ $term->term_id ] = $term->name;
		}
		pootlepb_render_content_block_field( $key, $field );
	}
}