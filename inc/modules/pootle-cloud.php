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

class Pootle_PB_Pootle_Cloud {

	/** @var Pootle_PB_Pootle_Cloud Instance */
	private static $_instance = null;
	/** @var string Token */
	public $token = 'pootle-cloud';
	/** @var string Main plugin class, Module is greyed out if this class is not present */
	public $class = 'Pootle_PB_Pootle_Cloud';
	/** @var string Module name */
	public $name = 'Pootle cloud template';
	private $tpls = [];
	private $tpl_cats = [];
	private $ppbPro = false;

	static function user_templates() {
		return get_option( 'pcld_user_tpls', [] );
	}

	static function pp_templates() {

		$tpls     = get_transient( 'pootle_pb_live_design_templates' );

		if ( ! $tpls || isset( $_GET['force_get_templates'] ) ) {
			$response = wp_remote_retrieve_body( wp_remote_get( 'https://pagebuilder-9144f.firebaseio.com/design-templates.json' ) );
			if ( $response ) {
				$tpls = json_decode( $response, 'assoc' );
				set_transient( 'pootle_pb_live_design_templates', $tpls, DAY_IN_SECONDS * 2.5 );
			}
		}

		return $tpls;
	}

	static function get_categories() {

		$categories = get_transient( 'pootle_pb_live_design_tpl_cats' );

		if ( ! $categories || isset( $_GET['force_get_templates'] ) ) {

			$response = wp_remote_retrieve_body( wp_remote_get( 'https://pagebuilder-9144f.firebaseio.com/categories.json' ) );
			if ( $response ) {
				$categories = json_decode( $response, 'assoc' );
				$categories = [ 'Our picks' => $categories['Our picks'] ] + $categories;
				set_transient( 'pootle_pb_live_design_tpl_cats', $categories, DAY_IN_SECONDS * 2.5 );
			}
		}

		return $categories;
	}

	/**
	 * Gets Pootle_PB_Pootle_Cloud instance
	 * @return Pootle_PB_Pootle_Cloud instance
	 * @since  1.0.0
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
		add_action( 'pootlepb_le_dialogs', array( $this, 'dialog', ) );
		add_action( 'pootlepb_enqueue_admin_scripts', array( $this, 'enqueue', ) );
		add_action( 'pootlepb_modules', array( $this, 'module' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wp_ajax_pcld_save_tpls', array( $this, 'save_cloud_tpls' ) );
	}

	function save_cloud_tpls() {
		if ( ! empty( $_POST['tpls'] ) ) {
			$tpls = $_POST['tpls'];

			if ( ! is_array( $tpls ) ) {
				$tpls = json_decode( $tpls, 'array' );
			}

			if ( $tpls ) {
				update_option( 'pcld_user_tpls', $tpls );
			}
		}
	}

	/**
	 * Enqueues JS and CSS
	 * @filter pootlepb_enqueue_admin_scripts
	 */
	public function init() {

		$this->ppbPro = class_exists( 'Pootle_Page_Builder_Pro' );

	}

	private function init_tpl_data() {
		$this->tpls = self::pp_templates();
		$this->tpl_cats = self::get_categories();
	}

	/**
	 * Enqueues JS and CSS
	 * @filter pootlepb_enqueue_admin_scripts
	 */
	public function enqueue() {
		$this->init_tpl_data();
		wp_localize_script( 'pootle-live-editor', 'ppbDesignTpls', $this->tpls );
	}

	private function tpl_html( $id ) {
		if ( empty ( $this->tpls[ $id ] ) ) {
			return '';
		}
		$tpl = $this->tpls[ $id ];
		$class = 'ppb-tpl';
		$html = '';
		if ( ! empty( $tpl['pro'] ) ) {
			if ( ! $this->ppbPro ) {
				$class .= ' pro-inactive';
			}
			$html .= "<span class='pro'>Pro</span>";
		}
		return
			"<div class='$class' data-id='$id'>$html" .
			"<img src='$tpl[img]' alt='$id'>" .
			"<div class='label'>$id</div>" .
			"<i class='fa fa-search'></i>" .
			"</div>";
	}

	function templates_html() {
		if ( ! $this->tpl_cats ) {
			$this->init_tpl_data();
		}
		foreach ( $this->tpl_cats as $cat => $tpls ) {
			?>
			<h2>
				<?php echo $cat; ?>
			</h2>
			<div class="templates-wrap">
				<?php
				foreach ( $tpls as $index => $id ) {
					echo $this->tpl_html( $id );
				}
				?>
			</div>
			<?php
		}
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
			$this->templates_html();
			do_action( 'after_design_templates' );
			?>
		</div>
		<div onclick="jQuery(this).fadeOut();" style="display: none;" id="pootlepb-design-templates-preview-wrap">
			<img src="">
		</div>
		<?php
	}

	/**
	 * The module box data
	 *
	 * @param array $mods Modules
	 *
	 * @return array
	 * @filter pootlepb_modules
	 */
	public function module( $mods ) {
		$mods[ $this->token ] = array(
			'label'        => $this->name,
			'icon_class'   => 'dashicons dashicons-cloud',
			'icon_html'    => '',
			'callback'     => 'designTemplate',
			'row_callback' => 'designTemplateRow',
			'active_class' => $this->class,
			'priority'     => 3,
			'only_new_row' => true,
		);

		return $mods;
	}

	/**
	 * Adds Pootle cloud plugin entry to modules admin page
	 * Not used since v5.0.beta8
	 * @param $mods
	 * @return mixed
	 */
	public function module_plugin( $mods ) {
		$mods[ $this->token ] = array(
			'Name'         => $this->name,
			'Description'  => 'Save and use templates across all your Pootle Pagebuilder site with Pootle Cloud!',
			'InstallURI'   => admin_url( "/plugin-install.php?s=$this->name&tab=search&type=term" ),
			'AuthorURI'    => 'https://www.pootlepress.com',
			'Author'       => 'pootlepress',
			'Image'        => "//ps.w.org/$this->token/assets/icon-256x256.png?rev=1262610",
			'active_class' => $this->class,
		);

		return $mods;

	}
}

Pootle_PB_Pootle_Cloud::instance();