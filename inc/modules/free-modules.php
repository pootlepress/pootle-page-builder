<?php
class pootle_page_builder_Unsplash {

	public $class = 'Pootle_Page_Builder';

	/** @var pootle_page_builder_Unsplash Instance */
	private static $_instance = null;

	/**
	 * Gets pootle_page_builder_Unsplash instance
	 * @return pootle_page_builder_Unsplash instance
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
		if ( class_exists( $this->class ) ) {
			// Adding modules to live editor sidebar
			add_action( 'pootlepb_modules', array( $this, 'module' ), 25 );
		}
	}

	public function module( $mods ) {
		$mods["unsplash"] = array(
			'label' => 'Unsplash',
			'icon_class' => 'dashicons dashicons-camera',
			'callback' => 'ppbUnsplashContent',
			'ActiveClass' => $this->class,
		);
		$mods["pbtn"] = array(
			'label' => 'Pootle button',
			'icon_html' => '',
			'callback' => 'ppbPbtnContent',
			'ActiveClass' => $this->class,
		);
		return $mods;
	}
}

pootle_page_builder_Unsplash::instance();