<?php
class pootle_page_builder_Pootle_Slider {

	public $token = 'pootle_slider';
	public $slug = 'pootle-slider';
	public $class = 'Pootle_Slider';
	public $name = 'Pootle Slider';

	/** @var pootle_page_builder_Pootle_Slider Instance */
	private static $_instance = null;

	/**
	 * Gets pootle_page_builder_Pootle_Slider instance
	 * @return pootle_page_builder_Pootle_Slider instance
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
		add_filter( 'pootlepb_modules', array( $this, 'module' ), 52 );
		// Adding modules plugin to Modules page
		add_action( 'pootlepb_modules_page', array( $this, 'module_plugin' ), 25 );
	}

	public function module( $mods ) {

		if ( 'pootle-slider' == get_post_type() ) {
			$modules = $mods;
			$mods = array();
			$mods['pootle-slider'] = array(
				'label' => 'New Slide',
				'icon_class' => 'dashicons dashicons-slides',
				'tab' => "#pootlepb-background-row-tab",
				'callback' => 'pootleSliderSlide',
				'active_class' => 'Pootle_Slider',
				'priority' => 5
			);

			$mods['ninja_forms-form'] = $modules['ninja_forms-form'];
			$mods['pbtn'] = $modules['pbtn'];

			add_filter( 'pootlepb_enabled_addons', function() {
				return array( 'pootle-slider', 'pbtn', 'ninja_forms-form', );
			} );

			add_filter( 'pootlepb_disabled_addons', function() {
				return array();
			} );
		} else {
			$mods["pootle-slider"] = array(
				'label' => 'Slider',
				'icon_class' => 'dashicons dashicons-slides',
				'tab' => "#pootle-pootle-slider-tab",
				//'callback' => 'pootleSliderSlide',
				'active_class' => 'Pootle_Slider',
				'priority' => 5
			);
		}
		return $mods;
	}

	public function module_plugin( $mods ) {
		$mods[ $this->slug ] = array(
			'Name' => $this->name,
			'Description' => 'Adds awesome sliders to the pootle page builder',
			'InstallURI' => admin_url( "/plugin-install.php?s=$this->name&tab=search&type=term" ),
			'AuthorURI' => 'https://www.pootlepress.com',
			'Author' => 'pootlepress',
			'Image' => "//ps.w.org/$this->slug/assets/icon-256x256.png?rev=1262610",
			'active_class' => $this->class,
		);
		return $mods;

	}
}

pootle_page_builder_Pootle_Slider::instance();