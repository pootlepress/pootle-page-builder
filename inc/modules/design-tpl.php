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
		$this->tpls = get_transient( 'pootle_pb_live_design_templates' );

		if ( ! $this->tpls || isset( $_GET['force_get_templates'] ) ) {
			$response = wp_remote_retrieve_body( wp_remote_get( 'https://pagebuilder-9144f.firebaseio.com/design-templates.json' ) );
			if( $response ) {
				$this->tpls = json_decode( $response, 'assoc' );
				set_transient( 'pootle_pb_live_design_templates', $this->tpls, DAY_IN_SECONDS * 2.5 );
			}
		}

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
		do_action( 'before_design_templates' );
		foreach ( $this->tpls as $id => $tpl ) {
			echo <<<HTML
<div class="ppb-tpl" data-id="$id">
<img src="$tpl[img]" alt="$id">
<i class="fa fa-search"></i>
</div>
HTML;
		}
		do_action( 'after_design_templates' );
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