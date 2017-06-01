<?php
/*
Plugin Name: Pootle Page Builder design template module
Plugin URI: http://pootlepress.com/
Description: Let's users add designer classes
Author: Shramee
Version: 1.0.0
Author URI: http://shramee.com/
@developer shramee <shramee.srivastav@gmail.com>
*/

class Pootle_PB_Module_Design_Template {

	/** @var string Token */
	public $token = 'ppb-design-template';

	/** @var string Main plugin class, Module is greyed out if this class is not present */
	public $class = 'Pootle_PB_Module_Design_Template';

	/** @var string Module name */
	public $name = 'Design template';

	/** @var Pootle_PB_Module_Design_Template Instance */
	private static $_instance = null;
	private $tpls = [];

	/**
	 * Gets Pootle_PB_Module_Design_Template instance
	 * @return Pootle_PB_Module_Design_Template instance
	 * @since 	1.0.0
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * PootlePB_Meta_Slider constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init', ) );
		add_action( 'pootlepb_le_dialogs', array( $this, 'dialog', ) );
		add_action( 'pootlepb_enqueue_admin_scripts', array( $this, 'enqueue', ) );
	}

	/**
	 * Initiates the addon
	 * @action init
	 */
	public function init() {
		// Adding modules to live editor sidebar
		add_action( 'pootlepb_modules', array( $this, 'module' ) );
	}

	/**
	 * Enqueues JS and CSS
	 * @filter pootlepb_enqueue_admin_scripts
	 */
	public function enqueue() {
		$this->tpls = [
			'Test' => [
				'style' => '{"cells":"2","style":{"full_width":"1","full_height":"1","accordion":"","accordion_sec_wrap":"","accordion_text":"","accordion_text_color":"","accordion_sec_wrap_close":"","match_col_hi":"","animate_cols":"","row_height":"0","hide_row":"","margin_top":"0","margin_bottom":"0","col_gutter":"1","background_toggle":".bg_grad","bg_color_wrap":"","background":"","bg_grad_wrap":"","bg_grad_prevu":"","grad_type":"slant","grad_col1":"#43cea2","grad_col2":"#185a9d","grad_opacity":"0.68","grad_image":"https://images.unsplash.com/photo-1493676304819-0d7a8d026dcf?ixlib=rb-0.3.5&amp;q=80&amp;fm=jpg&amp;crop=entropy&amp;cs=tinysrgb&amp;w=1080&amp;fit=max&amp;s=08ea06c224d639febf97836cd5a1066d","bg_image_wrap":"","background_image":"","bg_overlay_opacity":"0.5","background_parallax":"","background_image_size":"cover","bg_overlay_color":"","bg_video_wrap":"","bg_video":"","bg_mobile_image":"","bg_wrap_close":"","style":"","class":"","col_class":"","ppbpro-row-css":""},"id":1}',
				'content' => [
					[
						'text' => '<h2><span style="color: rgb(255, 255, 255);" data-mce-style="color: #ffffff;">Content from Live template</span></h2><p>Test was successful. Yay!</p>',
						'style' => '{"background-color":"#000000","background-transparency":"0.7","text-color":"#ffffff","border-width":"5","border-color":"#0a0a0a","padding":"25","rounded-corners":"7","inline-css":"","class":" ppb-content-v-center ppb-content-h-center","wc_prods-add":"","wc_prods-attribute":"","wc_prods-filter":null,"wc_prods-ids":null,"wc_prods-category":null,"wc_prods-per_page":"","wc_prods-columns":"","wc_prods-orderby":"","wc_prods-order":"","background-image":"","margin-bottom":"","margin-top":318.5,"margin-left":282,"padding-mobile":"","width":"350","ppb-business-pack-pro-gmap_code":"","ninja_forms":"","ppb-photo-addon_show":"","ppb-photo-addon_source_type":"","ppb-photo-addon_source_data":"","ppb-photo-addon_source_cats":null,"ppb-photo-addon_source_taxes":"","ppb-photo-addon_source_product_cat":null,"ppb-photo-addon_max":"","ppb-photo-addon_size":"","ppb-photo-addon_gallery_attr_type":"","ppb-photo-addon_slider_attr_animation":"","ppb-photo-addon_gallery_attr_cols":"","ppb-photo-addon_gallery_attr_full_width":"","ppb-photo-addon_gallery_attr_title":"","ppb-photo-addon_slider_attr_full_width":"","ppb-photo-addon_slider_attr_arrows":"1","ppb-photo-addon_gallery_link":"lightbox","ppb-photo-addon_slider_attr_pagination":"1","ppb-photo-addon_slider_attr_title":"1","ppb-photo-addon_gallery_link_target":"","ppb-photo-addon_slider_attr_autoplay":"","ppb-photo-addon_slider_attr_animation_speed":"","ppb-blog-customizer-across":null,"ppb-blog-customizer-down":null,"ppb-blog-customizer-cat":null,"ppb-blog-customizer-orderby":"","ppb-blog-customizer-pagination":"","ppb-blog-customizer-image-posts-only":"1","ppb-blog-customizer-title-size":"1","ppb-blog-customizer-text-color":"","ppb-blog-customizer-text-position":"","ppb-blog-customizer-feat-img":"circle","ppb-blog-customizer-show-excerpt":"1","ppb-blog-customizer-post-border-width":"1","ppb-blog-customizer-post-border-color":"#e8e8e8","ppb-blog-customizer-show-gutters":"","ppb-blog-customizer-rounded-corners":"","ppb-blog-customizer-show-date":"","ppb-blog-customizer-show-author":"","ppb-blog-customizer-show-cats":"","ppb-blog-customizer-show-comments":"","pootle-slider-id":"","pootle-slider-full_width":"","pootle-slider-js_pauseOnHover":"","pootle-slider-js_controlNav":"","pootle-slider-js_animation":"","pootle-slider-js_slideshowSpeed":"","pootle-slider-ratio":"","ppb-business-pack-pro-tabs_accordion_data":"","ppb-business-pack-pro-tabs_accordion":""}',
					]
				],
				'img' => 'https://images.unsplash.com/photo-1493676304819-0d7a8d026dcf?ixlib=rb-0.3.5&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&s=08ea06c224d639febf97836cd5a1066d',
			]
		];

		wp_localize_script( 'pootle-live-editor', 'ppbDesignTpls', $this->tpls );
	}

	/**
	 * Renders dialog html and JS
	 * @filter pootlepb_le_dialogs
	 */
	public function dialog() {
		?>
		<div class="pootlepb-dialog" id="pootlepb-design-templates" data-title="Choose a template...">
		<?php
		foreach ( $this->tpls as $id => $tpl ) {
			echo <<<HTML
<div class="ppb-tpl" data-id="$id">
<img src="$tpl[img]" alt="$id">
</div>
HTML;
		}
		?>
		</div>
		<?php
	}

	/**
	 * The module box data
	 * @param array $mods Modules
	 * @return array
	 * @filter pootlepb_modules
	 */
	public function module( $mods ) {
		$mods[ $this->token ] = array(
			'label' => $this->name,
			'icon_class' => 'dashicons dashicons-welcome-widgets-menus',
			'icon_html' => '',
			'callback' => 'designTemplate',
			'row_callback' => 'designTemplateRow',
			'active_class' => $this->class,
			'priority'    => 35,
			'only_new_row' => true,
		);
		return $mods;
	}
}

Pootle_PB_Module_Design_Template::instance();