<?php
class pootle_page_builder_Meta_Slider {

	public $token = 'metaslider';

	/** @var pootle_page_builder_Meta_Slider Instance */
	private static $_instance = null;

	/**
	 * Gets pootle_page_builder_Meta_Slider instance
	 * @return pootle_page_builder_Meta_Slider instance
	 * @since 	1.0.0
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	} // End instance()

	/**
	 * PootlePB_Meta_Slider constructor.
	 */
	function __construct() {
		add_action( 'init', array( $this, 'init', ) );
	}

	function init() {
		if ( class_exists( 'MetaSliderPlugin' ) ) {
			// Content block panel tabs
			add_filter( 'pootlepb_content_block_tabs', array( $this, 'tab' ) );
			add_filter( 'pootlepb_le_content_block_tabs', array( $this, 'tab' ) );
			// Adding shortcode to content block
			add_action( 'pootlepb_render_content_block', array( $this, 'shortcode' ), 25 );
			// Adding modules to live editor sidebar
			add_action( 'pootlepb_modules', array( $this, 'module' ), 25 );
			// Adding modules plugin to Modules page
			add_action( 'pootlepb_modules_page', array( $this, 'module_plugin' ), 25 );
			// Content block panel fields
			add_filter( 'pootlepb_content_block_fields', array( $this, 'fields' ) );
		}
	}

	public function tab( $tabs ) {
		$tabs[ $this->token ] = array(
			'label' => 'Meta Slider',
			'priority' => 5,
		);
		return $tabs;
	}

	public function fields( $fields ) {
		$sliders = get_posts( array( 'post_type' => 'ml-slider' ) );
		$shortcodes = array(
			'' => 'Please choose...'
		);
		foreach( $sliders as $slider ) {
			$shortcodes[ "[metaslider id={$slider->ID}]" ] = "{$slider->ID} - {$slider->post_title}";
		}

		$fields[ $this->token ] = array(
			'name' => 'Choose slider',
			'type' => 'select',
			'options' => $shortcodes,
			'priority' => 1,
			'tab' => $this->token,
		);
		return $fields;
	}

	public function shortcode( $info ) {
		$set = json_decode( $info['info']['style'], true );
		if ( ! empty( $set[ $this->token ] ) ) {
			echo do_shortcode( $set[ $this->token ] );
		}
	}

	public function module( $mods ) {
		$mods['metaslider-slider'] = array(
			'label' => 'Metaslider - Slider',
			'icon_class' => 'dashicons dashicons-images-alt',
			'icon_html' => '',
			'tab' => '#pootle-metaslider-tab',
			'ActiveClass' => 'MetaSliderPlugin',
		);
		return $mods;
	}

	public function module_plugin( $mods ) {
		$mods['ml-slider'] = array(
			'Name' => 'Meta slider',
			'Description' => 'Adds awesome meta slider module',
			'InstallURI' => admin_url( 'plugin-install.php?tab=plugin-information&plugin=ml-slider&TB_iframe=true&width=772&height=460"' ),
			'AuthorURI' => 'https://www.metaslider.com',
			'Author' => 'Matcha Labs',
			'Image' => '//ps.w.org/ml-slider/assets/icon.svg?rev=1000654',
			'ActiveClass' => 'MetaSliderPlugin',
		);
		return $mods;

	}
}

pootle_page_builder_Meta_Slider::instance();