<?php
/**
 * pootle page builder blog customizer Admin class
 * @property string $token Plugin token
 * @property string $url Plugin root dir url
 * @property string $path Plugin root dir path
 * @property string $version Plugin version
 */
class pootle_page_builder_blog_customizer_Admin{

	/**
	 * @var 	pootle_page_builder_blog_customizer_Admin Instance
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Main pootle page builder blog customizer Instance
	 * @return pootle_page_builder_blog_customizer_Admin instance
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
		$this->token   =   pootle_page_builder_blog_customizer::$token;
		$this->url     =   pootle_page_builder_blog_customizer::$url;
		$this->path    =   pootle_page_builder_blog_customizer::$path;
		$this->version =   pootle_page_builder_blog_customizer::$version;
	} // End __construct()

	/**
	 * Adds grid adding ui stylesheet and js
	 * @action wp_enqueue_scripts
	 * @since 1.0.0
	 */
	public function enqueue() {
		$token = $this->token;
		$url = $this->url;

		wp_enqueue_style( $token . '-admin-css', $url . '/assets/admin.css' );
		wp_enqueue_script( $token . '-admin-js', $url . '/assets/admin.js', array( 'jquery' ) );

		wp_localize_script( $token . '-admin-js', 'ppbPostCustomizerUrl', $this->url );
	}

	/**
	 * Adds editor panel tab
	 * @param array $tabs The array of tabs
	 * @return array Tabs
	 * @filter pootlepb_content_block_tabs
	 * @since 	1.0.0
	 */
	public function content_block_tabs( $tabs ) {
		if ( 'post' == get_post_type() ) {
			return $tabs;
		}
		$tabs[ $this->token ] = array(
			'label' => 'Posts',
			'icon'  => 'dashicons-admin-post',
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
		if ( 'post' == get_post_type() ) {
			return $fields;
		}
		$cats = array();
		foreach( get_categories() as $cat ) {
			$cats[ $cat->term_id ] = $cat->name;
		}
		$fields[ $this->token ] = array(
			'name' => 'Show posts',
			'type' => 'post-display',
			'priority' => 1,
			'tab' => $this->token,
		);

		$fields[ $this->token . '-cat' ] = array(
			'name' => 'Category',
			'type' => 'multi-select',
			'priority' => 2,
			'placeholder' => 'Leave blank to use all categories',
			'options'=> $cats,
			'tab' => $this->token,
		);

		$fields[ $this->token . '-orderby' ] = array(
			'name' => 'Order by',
			'type' => 'select',
			'priority' => 3,
			'options'=> array(
				'' => 'Please choose...',
				'date' => 'Date',
				'ID' => 'Post ID',
				'rand' => 'Random',
				'comment_count' => 'Most comments',
			),
			'tab' => $this->token,
		);

		$fields[ $this->token . '-image-posts-only' ] = array(
			'name' => 'Show posts with featured images only',
			'type' => 'checkbox',
			'tab' => $this->token,
			'priority' => 4,
		);

		$fields[ $this->token . '-pagination' ] = array(
			'name' => 'Show posts pagination',
			'type' => 'checkbox',
			'tab' => $this->token,
			'priority' => 4,
		);

		$fields[ $this->token . '-layout' ] = array(
			'name' => 'Layout',
			'type' => 'radio',
			'priority' => 5,
			'options'=> array(
				'left-image' => "<img class='post-custo-layout-thumb' data-layout='left-image' src='{$this->url}assets/layout-.png'>",
				'top-image' => "<img class='post-custo-layout-thumb' data-layout='top-image' src='{$this->url}assets/layout-top-image.png'>",
				'right-image' => "<img class='post-custo-layout-thumb' data-layout='right-image' src='{$this->url}assets/layout-right-image.png'>",
				'full-image' => "<img class='post-custo-layout-thumb' data-layout='full-image' src='{$this->url}assets/layout-full-image.png'>",
			),
			'tab' => $this->token,
		);

		$fields[ $this->token . '-layout-options-container-open' ] = array(
			'name' => '<div class="ppb-custo-layout-options">',
			'type' => 'html',
			'tab' => $this->token,
			'priority' => 5.2,
		);

		$fields[ $this->token . '-title-size' ] = array(
			'name' => 'Title font size',
			'type' => 'slider',
			'min'  => '0.1',
			'max'  => '2.5',
			'step' => '0.05',
			'unit' => 'em',
			'show_actual' => 1,
			'tab'  => $this->token,
			'css'  => 'font-size',
			'selector'  => '.ppb-post .title a',
			'priority' => 7,
		);

		$fields[ $this->token . '-text-color' ] = array(
			'name' => 'Text color',
			'type' => 'color',
			'priority' => 9,
			'selector' => '.ppb-post *',
			'css'  => 'color',
			'tab' => $this->token,
		);

		$fields[ $this->token . '-text-position' ] = array(
			'name' => 'Text Position',
			'type' => 'select',
			'priority' => 10,
			'options'=> array(
				'' => 'Center middle',
				'x-center y-bottom' => 'Center bottom',
				'x-left y-bottom' => 'Left bottom',
			),
			'tab' => $this->token,
		);

		$fields[ $this->token . '-feat-img' ] = array(
			'name' => 'Featured image',
			'type' => 'select',
			'priority' => 12,
			'options'=> array(
				'' => 'Square',
				'image-66' => 'Rectangle',
				'circle' => 'Circle',
			),
			'tab' => $this->token,
		);

		$fields[ $this->token . '-show-excerpt' ] = array(
			'name' => 'Show excerpt',
			'type' => 'checkbox',
			'tab' => $this->token,
			'priority' => 14,
		);

		$fields[ $this->token . '-post-border' ] = array(
			'name' => 'Border',
			'type' => 'border',
			'selector' => '.top-image .ppb-blog-content',
			'css'  => 'border',
			'tab' => $this->token,
			'priority' => 15,
		);

		$fields[ $this->token . '-show-gutters' ] = array(
			'name' => 'Add gutters',
			'type' => 'checkbox',
			'tab' => $this->token,
			'priority' => 16,
		);

		$fields[ $this->token . '-rounded-corners' ] = array(
			'name' => 'Rounded corners',
			'type' => 'checkbox',
			'tab' => $this->token,
			'priority' => 17,
		);

		$fields[ $this->token . '-show-date' ] = array(
			'name' => 'Show date',
			'type' => 'select',
			'tab' => $this->token,
			'options'=> array(
				'' => 'Do not show',
				'above-title' => 'Show above title',
				'below-title' => 'Show below title',
				'below-excerpt' => 'Show below except',
			),
			'priority' => 18,
		);

		$fields[ $this->token . '-show-author' ] = array(
			'name' => 'Show author',
			'type' => 'select',
			'tab' => $this->token,
			'options'=> array(
				'' => 'Do not show',
				'above-title' => 'Show above title',
				'below-title' => 'Show below title',
				'below-excerpt' => 'Show below except',
			),
			'priority' => 19,
		);

		$fields[ $this->token . '-show-cats' ] = array(
			'name' => 'Show categories',
			'type' => 'select',
			'options'=> array(
				'' => 'Do not show',
				'above-title' => 'Show above title',
				'below-title' => 'Show below title',
				'below-excerpt' => 'Show below except',
			),
			'tab' => $this->token,
			'priority' => 20,
		);

		$fields[ $this->token . '-show-comments' ] = array(
			'name' => 'Show comments',
			'type' => 'select',
			'tab' => $this->token,
			'options'=> array(
				'' => 'Do not show',
				'above-title' => 'Show above title',
				'below-title' => 'Show below title',
				'below-excerpt' => 'Show below except',
			),
			'priority' => 21,
		);

		$fields[ $this->token . '-layout-options-container-close' ] = array(
			'name' => '</div><!--.ppb-custo-layout-options-->',
			'type' => 'html',
			'tab' => $this->token,
			'priority' => 22,
		);

		return $fields;
	}

	public function post_display_field( $key, $field ) {
		$field['type'] = 'select';
		$field['options'] = array();
		for ( $i = 1; $i < 5; $i++ ) {
			$field['options'][$i] = $i;
		}
		pootlepb_render_content_block_field( $key . '-across', $field );
		echo ' across by ';
		for ( $i = 5; $i < 21; $i++ ) {
			$field['options'][$i] = $i;
		}
		pootlepb_render_content_block_field( $key . '-down', $field );
		echo ' down';
	}
}