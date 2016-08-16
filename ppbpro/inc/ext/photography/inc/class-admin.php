<?php
/**
 * Pootle page builder Photography add on Admin class
 * @property string token Plugin token
 * @property string $url Plugin root dir url
 * @property string $path Plugin root dir path
 * @property string $version Plugin version
 */
class page_builder_photo_addon_Admin{

	/**
	 * @var 	page_builder_photo_addon_Admin Instance
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Main Pootle page builder Photography add on Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @return page_builder_photo_addon instance
	 * @since 	1.0.0
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
	 * @since 	1.0.0
	 */
	private function __construct() {
		$this->token   =   page_builder_photo_addon::$token;
		$this->url     =   page_builder_photo_addon::$url;
		$this->path    =   page_builder_photo_addon::$path;
		$this->version =   page_builder_photo_addon::$version;
	} // End __construct()

	/**
	 * Adds editor panel tab
	 * @param array $tabs The array of tabs
	 * @return array Tabs
	 * @filter pootlepb_content_block_tabs
	 * @since 	1.0.0
	 */
	public function content_block_tabs( $tabs ) {
		$tabs[ $this->token ] = array(
			'label' => 'Photos',
			'priority' => 5,
		);
		return $tabs;
	}

	/**
	 * Adds content block panel fields
	 * @param array $fields Fields to output in content block panel
	 * @return array Tabs
	 * @filter pootlepb_content_block_fields
	 * @since 	1.0.0
	 */
	public function content_block_fields( $fields ) {
		$prefix = $this->token . '_';

		$fields[ $prefix . 'show' ] = array(
			'name' => 'Show',
			'type' => 'select',
			'options' => array(
				''			=> 'Please choose',
				'slider'	=> 'Slider',
				'gallery'	=> 'Gallery',
			),
			'priority' => 1,
			'tab' => $this->token,
		);
		$fields[ $prefix . 'source' ] = array(
			'name' => 'Source',
			'type' => 'slider_source',
			'options' => array(
				''				=> 'Media library',
				'unsplash'				=> 'Unsplash',
				'rcnt_posts'	=> 'Recent Posts',
				'cat'   		=> 'Categories',
				'tax'   		=> 'Taxonomy',
			),
			'priority' => 2,
			'tab' => $this->token,
		);
		$fields[ $prefix . 'max' ] = array(
			'name' => 'Max slides',
			'type' => 'number',
			'priority' => 3,
			'tab' => $this->token,
		);
		$fields[ $prefix . 'size' ] = array(
			'name' => 'Image size',
			'type' => 'select',
			'options' => array(
				'thumbnail' => 'Thumbnail',
				'' => 'Medium',
				'large' => 'Large',
			),
			'priority' => 3,
			'tab' => $this->token,
		);
		$fields[ $prefix . 'slider_attr_animation' ] = array(
			'name' => 'Slider animation',
			'type' => 'select',
			'options' => array(
				''			=> 'Fade',
				'slide'		=> 'Slide',
				'kb'		=> 'Ken Burns',
				'ribbon'	=> 'Ribbon',
			),
			'priority' => 4,
			'tab' => $this->token,
		);
		$fields[ $prefix . 'slider_attr_full_width' ] = array(
			'name' => 'Stretch to full width of screen',
			'type' => 'checkbox',
			'priority' => 5,
			'tab' => $this->token,
		);
		$fields[ $prefix . 'slider_attr_arrows' ] = array(
			'name' => 'Slider navigation arrows',
			'type' => 'checkbox',
			'priority' => 7,
			'tab' => $this->token,
		);
		$fields[ $prefix . 'slider_attr_pagination' ] = array(
			'name' => 'Slider pagination',
			'type' => 'checkbox',
			'priority' => 7,
			'tab' => $this->token,
		);
		$fields[ $prefix . 'slider_attr_title' ] = array(
			'name' => 'Show title',
			'type' => 'checkbox',
			'priority' => 7,
			'tab' => $this->token,
		);
		$fields[ $prefix . 'slider_attr_autoplay' ] = array(
			'name' => 'Autoplay speed',
			'type' => 'number',
			'unit' => 'ms (1/1000 second)',
			'help-text' => 'Default 5000ms.',
			'priority' => 8,
			'tab' => $this->token,
		);
		$fields[ $prefix . 'slider_attr_animation_speed' ] = array(
			'name' => 'Animation speed',
			'type' => 'number',
			'unit' => 'ms (1/1000 second)',
			'help-text' => 'Default 500ms.',
			'priority' => 9,
			'tab' => $this->token,
		);
		$fields[ $prefix . 'gallery_attr_type' ] = array(
			'name' => 'Gallery type',
			'type' => 'select',
			'options' => array(
				''			=> 'Normal Grid',
				'masonry'	=> 'Masonry',
				'photo-listing'		=> 'Photo Listing',
			),
			'priority' => 3,
			'tab' => $this->token,
		);
		$fields[ $prefix . 'gallery_attr_cols' ] = array(
			'name' => 'Columns',
			'type' => 'select',
			'options' => array(
				''	=> 'Default',
				1	=> 1,
				2	=> 2,
				3	=> 3,
				4	=> 4,
				5	=> 5,
				6	=> 6,
				7	=> 7,
				8	=> 8,
				9	=> 9,
				10	=> 10,
				11	=> 11,
				12	=> 12,
			),
			'priority' => 4,
			'tab' => $this->token,
		);
		$fields[ $prefix . 'gallery_attr_full_width' ] = array(
			'name' => 'Stretch to full width of screen',
			'type' => 'checkbox',
			'priority' => 5,
			'tab' => $this->token,
		);
		$fields[ $prefix . 'gallery_attr_title' ] = array(
			'name' => 'Show title',
			'type' => 'select',
			'options' => array(
				''			=> 'Never',
				'1'		=> 'Always',
				'2'		=> 'On mouse over',
			),
			'priority' => 5,
			'tab' => $this->token,
		);
/*
		$fields[ $prefix . 'gallery_attr_title_hide_mobile' ] = array(
			'name' => 'Hide title on mobile devices',
			'type' => 'checkbox',
			'priority' => 6,
			'tab' => $this->token,
		);
*/
		$fields[ $prefix . 'gallery_link' ] = array(
			'name' => 'Link',
			'type' => 'select',
			'options' => array(
				''			=> 'No link',
				'img'		=> 'Large image',
				'lightbox'	=> 'Lightbox',
				'post'		=> 'Post page',
			),
			'priority' => 7,
			'tab' => $this->token,
		);
		$fields[ $prefix . 'gallery_link_target' ] = array(
			'name' => 'Link Target',
			'type' => 'select',
			'options' => array(
				''			=> 'Same window',
				'_blank'		=> 'New window',
			),
			'priority' => 8,
			'tab' => $this->token,
		);
		return $fields;
	}


	/**
	 * Enqueue admin scripts
	 * @filter pootlepb_enqueue_admin_scripts
	 * @since 	1.0.0
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
		$key = esc_attr( $key );
		pootlepb_render_content_block_field( $key . '_type', $field );

		$input_attrs = "dialog-field='{$key}_data' class='content-block-{$key}_data' ";
		echo
			"<input type='hidden' $input_attrs data-style-field-type='text' />";
		?>
		<div class="photo-source spaced-up photo-" style="display: block;">
			<button class='button photo-select-images'>Select Images</button>
		</div>
		<div class="photo-source spaced-up photo-unsplash" style="display: block;">
			<input type="search">
			<button class='button photo-select-unsplash'>Search Unsplash</button>
		</div>
		<div class="photo-images photo-source spaced-up photo- photo-unsplash"></div>
		<div class="photo-source spaced-up photo-cat" style="display: none;">
			<?php $this->output_terms_select( 'category',  $key . '_cats', $field, 'Categories' ) ?>
		</div>
		<div class="photo-source spaced-up photo-tax" style="display: none;">
			<?php
			$args = array(
				'public'   => true,
				'_builtin' => false
			);
			$taxonomies = get_taxonomies( $args, 'objects' );
			$field['type'] = 'select';
			$field['options'] = array();
			if ( $taxonomies ) {
				$field['options'][''] = 'Choose...';
				foreach ( $taxonomies  as $tax ) {
					$field['options'][ $tax->name ] = $tax->label;
				}
				pootlepb_render_content_block_field( $key . '_taxes', $field );
				unset( $field['options'][''] );
				foreach ( $field['options']  as $taxonomy => $lbl ) {
					echo '<div class="photo_tax_terms photo_tax_terms_' . $taxonomy . ' spaced-up">';
					$this->output_terms_select( $taxonomy, $key . '_' . $taxonomy, $field, $lbl );
					echo '</div>';
				}
			} else {
				echo '<p>No public custom taxonomies found!</p>';
			}
			?>
		</div>
		<?php
	}

	protected function output_terms_select( $taxonomy, $key, $field, $label ) {
		$field['type'] = 'multi-select';
		$field['placeholder'] = 'Choose Some ' . $label . '...';
		$field['options'] = array();
		$terms = get_terms( $taxonomy );
		if ( ! $terms || $terms instanceof WP_Error ) {
			echo '<p>No ' . $label . ' found!</p></span>';
			return;
		}
		foreach ( $terms as $term ) {
			$field['options'][ $term->term_id ] = $term->name;
		}
		pootlepb_render_content_block_field( $key, $field );
	}
}