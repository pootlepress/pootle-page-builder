<?php
class pootle_page_builder_Meta_Slider {

	public $token = 'metaslider';
	protected $choices = array( '' => 'Please choose...' );

	/** @var pootle_page_builder_Meta_Slider Instance */
	private static $_instance = null;

	/** @var int Tracks slider in the page */
	private $id = 1;

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
		// Adding modules to live editor sidebar
		add_action( 'pootlepb_modules', array( $this, 'module' ), 25 );
		// Adding modules plugin to Modules page
		add_action( 'pootlepb_modules_page', array( $this, 'module_plugin' ), 25 );
		if ( class_exists( 'MetaSliderPlugin' ) ) {
			// Content block panel tabs
			add_filter( 'pootlepb_content_block_tabs', array( $this, 'tab' ) );
			add_filter( 'pootlepb_le_content_block_tabs', array( $this, 'tab' ) );
			// Adding shortcode to content block
			add_action( 'pootlepb_render_content_block', array( $this, 'shortcode' ), 52 );
			// Content block panel fields
			add_filter( 'pootlepb_content_block_fields', array( $this, 'fields' ) );

			$sliders = get_posts( array( 'post_type' => 'ml-slider' ) );
			foreach( $sliders as $slider ) {
				$this->choices[ "[metaslider id={$slider->ID}]" ] = "{$slider->ID} - {$slider->post_title}";
			}
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

		$fields[ $this->token ] = array(
			'name' => 'Choose slider',
			'type' => 'select',
			'options' => $this->choices,
			'priority' => 1,
			'tab' => $this->token,
		);
		return $fields;
	}

	public function shortcode( $info ) {
		$set = json_decode( $info['info']['style'], true );
		if ( ! empty( $set[ $this->token ] ) ) {
			$id     = 'ppb-meta-slider-' . $this->id ++;
			$slider = do_shortcode( $set[ $this->token ] );
			echo <<<HTML
<div id='$id'>$slider</div>
<script>
( function( $ ) {
	var ro = $('#$id').closest('.ppb-row.ppb-stretch-full-width');
	if ( ro.length ) {
		ro.addClass('ppb-meta-slider-full-width')
	}
} )( jQuery );
</script>
<style>
.ppb-row.ppb-meta-slider-full-width {
	padding: 0!important;
}
</style>
HTML;

		}
	}

	public function module( $mods ) {
		$mods['metaslider-slider'] = array(
			'label' => 'Metaslider - Slider',
			'icon_class' => 'dashicons dashicons-images-alt',
			'icon_html' => '',
			'tab' => '#pootle-metaslider-tab',
			'active_class' => 'MetaSliderPlugin',
			'priority'    => 40,
		);
		return $mods;
	}

	public function module_plugin( $mods ) {
		$mods['ml-slider'] = array(
			'Name' => 'Meta slider',
			'Description' => 'Adds awesome meta slider module',
			'InstallURI' => admin_url( "/plugin-install.php?s=Meta Slider&tab=search&type=term" ),
			'AuthorURI' => 'https://www.metaslider.com',
			'Author' => 'Matcha Labs',
			'Image' => '//ps.w.org/ml-slider/assets/icon.svg?rev=1000654',
			'active_class' => 'MetaSliderPlugin',
		);
		return $mods;

	}
}

pootle_page_builder_Meta_Slider::instance();