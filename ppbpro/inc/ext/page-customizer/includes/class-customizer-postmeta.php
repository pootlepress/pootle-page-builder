<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 30/9/15
 * Time: 2:12 PM
 */

class Lib_Customizer_Postmeta {

	/**
	 * @var string Section id
	 */
	public $id;

	/**
	 * @var string Section title
	 */
	public $title;

	/**
	 * @var array Section fields
	 */
	public $fields;

	/**
	 * @var array Customizer field values
	 */
	public $values;

	/**
	 * @var string Customizer controls classes
	 */
	public $controls_classes;

	/**
	 * Constructor function.
	 * @param $id
	 * @param $title
	 * @param $fields
	 * @since   1.0.0
	 */
	public function __construct( $id, $title, $fields ) {
		$this->id = $id;
		$this->title = $title;
		$this->fields = $fields;
		$this->controls_classes = array(
			'text'          => 'WP_Customize_Control',
			'checkbox'      => 'WP_Customize_Control',
			'radio'         => 'WP_Customize_Control',
			'select'        => 'WP_Customize_Control',
			'dropdownpages' => 'WP_Customize_Control',
			'textarea'      => 'WP_Customize_Control',
			'color'         => 'WP_Customize_Color_Control',
			'lib_color'     => 'Lib_Customize_Alpha_Color_Control',
			'image'         => 'WP_Customize_Image_Control',
			'upload'        => 'WP_Customize_Upload_Control',
		);

		//Register the panels, sections, controls and settings
		add_action( 'customize_register', array( $this, 'customizer_register' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customizer_scripts' ) );

	}

	/**
	 * Adds custom fields, panels, and sections to WP_Customize_Manager
	 * @param WP_Customize_Manager $manager
	 * @action customize_register
	 * @since 1.0.0
	 */
	public function customizer_register( WP_Customize_Manager $manager ) {
		if ( ! class_exists( 'Lib_Customize_Setting' ) ) {
			require 'class-customize-setting.php';
		}
		require 'class-alpha-color-picker.php';
		$sections = array();

		$fields = $this->fields;

		foreach ( $fields as $ki => &$option ) {
			if ( empty( $option['section'] ) ) {
				$option['section'] = 'lib' . $this->id;
				$option['id'] = $this->id . '[' . $option['id'] . ']';
			} else {
				$option['id'] = $this->id . '[' . $option['id'] . ']';
				$section = strtolower( $option['section'] );
				$section ='lib-panel-' . $this->id . '-' .  preg_replace( '/[^a-z0-9]/', '-', $section );
				$sections[ $section ] = $option['section'];
				$option['section'] = $section;
			}
		}

		//Fields
		$this->add_controls( $manager, $fields );

		if ( empty( $_GET['post_id'] ) ) {
			return;
		}

		if ( ! empty( $sections ) ) {

			$manager->add_panel( 'lib-' . $this->id, array(
				'title'    => $this->title,
				'priority' => 1,
			) );

			foreach( $sections as $k => $v ) {

				$manager->add_section( $k, array(
					'title'    => $v,
					'panel' => 'lib-' . $this->id,
				) );
			}
		} else {

			//Adding the panel{}
			$manager->add_section( 'lib-' . $this->id, array(
				'title'    => $this->title,
				'priority' => 1,
			) );

		}
	}

	/**
	 * Adds controls and settings to WP_Customize_Manager
	 * @param WP_Customize_Manager $manager
	 * @param array $fields Controls data
	 * @since 1.0.0
	 */
	private function add_controls( WP_Customize_Manager $manager, $fields ){

		foreach ( $fields as $ki => $option ) {

			//Render Simple controls ( Containing single field )
			$this->add_simple_control( $manager, $option );
		}
	}

	/**
	 * Adds controls and settings to WP_Customize_Manager
	 * @action customize_controls_enqueue_scripts
	 * @since 1.0.0
	 */
	public function customizer_scripts(){
		wp_enqueue_script(
			'ppc-custo-controls',
			plugin_dir_url( __FILE__ ) . '../assets/ppc-custo-controls.js',
			array( 'jquery', 'wp-color-picker' )
		);
	}

	/**
	 * Adds simple control and its setting to WP_Customize_Manager
	 * @param WP_Customize_Manager $manager
	 * @param array $option Field data
	 * @since 1.0.0
	 */
	private function add_simple_control( WP_Customize_Manager $manager, $option ) {

		//Add settings
		$manager->add_setting(
			new Shramee_Post_Meta_Customize_Setting(
				$manager,
				$option['id'],
				array(
					'default' => $option['default'],
					'type' => 'post_meta'
				)
			)
		);

		if ( empty( $_GET['post_id'] ) ) {
			return;
		}

		//Create a section class
		if ( ! empty( $this->controls_classes[ $option['type'] ] ) ){
			$control_class = $this->controls_classes[ $option['type'] ];
			//Add control
			$manager->add_control( new $control_class( $manager, $option['id'], $option ) );
		}
	}
}