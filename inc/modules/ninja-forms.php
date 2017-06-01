<?php
class pootle_page_builder_Ninja_Forms {

	public $token = 'ninja_forms';
	public $slug = 'ninja-forms';
	public $class = 'Ninja_Forms';
	public $name = 'Ninja Form';

	/** @var pootle_page_builder_Ninja_Forms Instance */
	private static $_instance = null;
	protected $choices = array( '' => 'Please choose...' );

	/**
	 * Gets pootle_page_builder_Ninja_Forms instance
	 * @return pootle_page_builder_Ninja_Forms instance
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
		add_action( 'pootlepb_modules', array( $this, 'module' ) );
		// Adding modules plugin to Modules page
		add_action( 'pootlepb_modules_page', array( $this, 'module_plugin' ), 25 );
		if ( class_exists( $this->class ) ) {
			// Content block panel tabs
			add_filter( 'pootlepb_content_block_tabs', array( $this, 'tab' ) );
			add_filter( 'pootlepb_le_content_block_tabs', array( $this, 'tab' ) );
			// Adding shortcode to content block
			add_action( 'pootlepb_render_content_block', array( $this, 'shortcode' ), 52 );
			// Content block panel fields
			add_filter( 'pootlepb_content_block_fields', array( $this, 'fields' ) );

			// Get all NinjaForms form ids
			$form_ids = array();
			if ( method_exists( Ninja_Forms(), 'forms' ) ) {
				$form_ids = Ninja_Forms()->forms()->get_all();
				foreach ( $form_ids as $form_id ) {
					$this->choices[ $form_id ] = "$form_id - " . Ninja_Forms()->form( $form_id )->get_setting( 'form_title' );
				}
			} else if ( method_exists( Ninja_Forms(), 'form' ) ) {
				$form_objs = Ninja_Forms()->form()->get_forms();
				foreach ( $form_objs as $form ) {
					$form_id = $form->get_id();
					$this->choices[ $form_id ] = "$form_id - " . $form->get_setting( 'title' );
				}
			}
		}
	}

	public function tab( $tabs ) {
		$tabs[ $this->token ] = array(
			'label' => $this->name,
			'priority' => 5,
		);
		return $tabs;
	}

	public function fields( $fields ) {
		$fields[ $this->token ] = array(
			'name' => 'Form ID',
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
			echo do_shortcode( "[$this->token id={$set[ $this->token ]}]" );
		}
	}

	public function module( $mods ) {
		$mods["$this->token-form"] = array(
			'label' => $this->name,
			'icon_class' => 'dashicons dashicons-feedback',
			'icon_html' => '',
			'tab' => "#pootle-$this->token-tab",
			'active_class' => $this->class,
			'priority'    => 35,
		);
		return $mods;
	}

	public function module_plugin( $mods ) {
		$mods[ $this->slug ] = array(
			'Name' => $this->name,
			'Description' => 'Adds awesome ninja form module',
			'InstallURI' => admin_url( "/plugin-install.php?s=$this->name&tab=search&type=term" ),
			'AuthorURI' => 'https://www.metaslider.com',
			'Author' => 'Matcha Labs',
			'Image' => "//ps.w.org/$this->slug/assets/icon-256x256.png?rev=1262610",
			'active_class' => $this->class,
		);
		return $mods;

	}
}

pootle_page_builder_Ninja_Forms::instance();