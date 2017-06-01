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
			'testtpl' => [
				'rowStyles' => '{"cells":"2","style":{"full_width":"","full_height":"","accordion":"","accordion_sec_wrap":"","accordion_text":"","accordion_text_color":"","accordion_sec_wrap_close":"","match_col_hi":"","animate_cols":"","row_height":"0","hide_row":"","margin_top":"0","margin_bottom":"0","col_gutter":"1","background_toggle":".bg_grad","bg_color_wrap":"","background":"","bg_grad_wrap":"","bg_grad_prevu":"","grad_type":"slant","grad_col1":"#43cea2","grad_col2":"#185a9d","grad_opacity":"0.68","grad_image":"https://images.unsplash.com/photo-1493676304819-0d7a8d026dcf?ixlib=rb-0.3.5&amp;q=80&amp;fm=jpg&amp;crop=entropy&amp;cs=tinysrgb&amp;w=1080&amp;fit=max&amp;s=08ea06c224d639febf97836cd5a1066d","bg_image_wrap":"","background_image":"","bg_overlay_opacity":"0.5","background_parallax":"","background_image_size":"cover","bg_overlay_color":"","bg_video_wrap":"","bg_video":"","bg_mobile_image":"","bg_wrap_close":"","style":"","class":"","col_class":"","ppbpro-row-css":""},"id":1}',
				'img' => 'https://images.unsplash.com/photo-1493676304819-0d7a8d026dcf?ixlib=rb-0.3.5&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&s=08ea06c224d639febf97836cd5a1066d',
				'label' => 'Test',
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
<div class="ppb-tpl" id="$id">
<img src="$tpl[img]" alt="$tpl[label]">
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
			'ActiveClass' => $this->class,
			'priority'    => 35,
			'only-new-row' => true,
		);
		return $mods;
	}
}

Pootle_PB_Module_Design_Template::instance();