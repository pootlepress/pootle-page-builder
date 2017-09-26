<?php

/**
 * Pootle Page Builder Live Editor public class
 * @since 2.0.0
 */
class Pootle_Page_Builder_Live_Editor_Public {

	/**
	 * @var    Pootle_Page_Builder_Live_Editor_Public Instance
	 * @access  private
	 * @since 2.0.0
	 */
	private static $_instance = null;
	private $_do_nothing = null;
	private $_active = false;

	/**
	 * @var    mixed Edit title
	 * @access  private
	 * @since 2.0.0
	 */
	private $edit_title = false;

	/**
	 * @var    mixed Edit title
	 * @access private
	 * @since 2.0.0
	 */
	private $post_id = false;

	/**
	 * @var    array Addons to display
	 * @access  private
	 * @since 2.0.0
	 */
	private $addons = array();
	private $user;
	private $nonce;
	private $ipad = false;

	/**
	 * Constructor function.
	 * @access  private
	 * @since   1.0.0
	 */
	private function __construct() {
		$this->token   = Pootle_Page_Builder_Live_Editor::$token;
		$this->url     = Pootle_Page_Builder_Live_Editor::$url;
		$this->path    = Pootle_Page_Builder_Live_Editor::$path;
		$this->version = Pootle_Page_Builder_Live_Editor::$version;
	} // End instance()

	/**
	 * Deactivate live editor hooks for one ppb render
	 * @uses Pootle_Page_Builder_Live_Editor_Public::instance(), Pootle_Page_Builder_Live_Editor_Public::_active
	 */
	public static function deactivate_le() {
		Pootle_Page_Builder_Live_Editor_Public::instance()->_active = false;
	}

	/**
	 * Main Pootle Page Builder Live Editor Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @since 1.0.0
	 * @return Pootle_Page_Builder_Live_Editor_Public instance
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Check if live editor is active
	 * @return bool Live editor active?
	 */
	public static function is_active() {
		return Pootle_Page_Builder_Live_Editor_Public::instance()->_active;
	} // End __construct()

	function tinymce_plugin( $plugin_array ) {
		$plugin_array['ppblink'] = $this->url . '/assets/ppblink.js';

		return $plugin_array;
	}

	public function post_status() {
		return get_post_status( $this->post_id );
	}

	/**
	 * Add pootle-live-editor-active class to body
	 *
	 * @param array $classes
	 *
	 * @return array Content
	 */
	public function body_class( $classes ) {
		$classes[] = 'pootle-live-editor-active';

		return $classes;
	}

	/**
	 * Add post type class to post
	 *
	 * @param array $classes
	 *
	 * @return string Content
	 */
	public function post_type_class( $classes, $unused, $post ) {
		$classes[] = get_post_type( $post );

		return $classes;
	}

	/**
	 * Enqueue the required styles
	 * @since 1.1.0
	 */
	public function enqueue() {
		$this->enqueue_scripts();
		$this->l10n_scripts();

		$ver = POOTLEPB_VERSION;
		$url = $this->url . 'assets';
		wp_enqueue_style( 'pootlepb-ui-styles', POOTLEPB_URL . 'css/ppb-jq-ui.css', array(), $ver );
		wp_enqueue_style( 'ppb-panels-live-editor-css', "$url/front-end.css", array(), $ver );
		wp_enqueue_style( 'wp-color-picker' );
	}

	protected function enqueue_scripts() {
		global $pootlepb_color_deps;
		$url       = $this->url . 'assets';
		$jQui_deps = array(
			'jquery',
			'jquery-ui-slider',
			'jquery-ui-dialog',
			'jquery-ui-tabs',
			'jquery-ui-sortable',
			'jquery-ui-resizable',
			'jquery-ui-droppable'
		);
		$ppb_js    = POOTLEPB_URL . 'js';
		$ver       = POOTLEPB_VERSION;

		wp_enqueue_media();

		wp_enqueue_style( 'ppb-chosen-style', "$ppb_js/chosen/chosen.css" );
		wp_enqueue_script( 'pootlepb-chosen', "$ppb_js/chosen/chosen.jquery.min.js", array( 'jquery' ), POOTLEPB_VERSION );

		wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), $pootlepb_color_deps );
		wp_enqueue_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), array( 'iris' ) );

		wp_enqueue_script( 'ppb-fields', "$url/ppb-deps.js", array( 'wp-color-picker', ), $ver );
		wp_enqueue_script( 'ppb-ui', "$ppb_js/ppb-ui.js", $jQui_deps, $ver );
		wp_enqueue_script( 'ppb-unsplash', "$ppb_js/unsplash.js", $jQui_deps, $ver );

		wp_enqueue_script( 'ppb-fa-picker', "$ppb_js/fontawesome-iconpicker.min.js", $jQui_deps, $ver );
		wp_enqueue_style( 'ppb-fa-picker', "$ppb_js/fontawesome-iconpicker.min.css" );

		wp_enqueue_script( 'ppb-ui-tooltip', "$ppb_js/ui.admin.tooltip.js" );
		wp_enqueue_script( 'ppble-tmce-view', "$url/tmce.view.js" );
		wp_enqueue_script( 'ppble-tmce-theme', "$url/tmce.theme.js", array( 'ppble-tmce-view' ) );

		wp_enqueue_script( 'ppble-sd', "$url/showdown.min.js", array( 'ppb-ui', 'ppb-fields', ), $ver );

		$suff = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'dev.js' : 'min.js';
		wp_enqueue_script( 'pootle-live-editor', "$url/front-end.$suff", array(
			'pootle-page-builder-front-js',
			'masonry',
			'ppb-ui',
			'ppble-sd',
			'ppb-fields',
			'wp-color-picker',
		), $ver );

		if ( isset( $_REQUEST['tour'] ) ) {
			wp_enqueue_style( 'pootle-live-editor-tour-css', "$url/tour.css", array(), $ver );
			if ( $_REQUEST['tour'] == 'ipad' ) {
				wp_enqueue_script( 'pootle-live-editor-tour', "$url/tour-ipad.js", array( 'jquery', ), $ver );
			} else {
				wp_enqueue_script( 'pootle-live-editor-tour', "$url/tour.js", array( 'jquery', ), $ver );
			}
		}

		wp_enqueue_script( "pp-pb-iris", "$ppb_js/iris.js", array( 'iris' ) );

		wp_enqueue_script( 'pp-pb-color-picker', "$ppb_js/color-picker-custom.js", array( 'pp-pb-iris' ) );

		wp_enqueue_script( 'mce-view' );
		wp_enqueue_script( 'image-edit' );

		if ( defined( 'NINJA_FORMS_URL' ) ) {
			wp_enqueue_style( 'ninja-forms-display', NINJA_FORMS_URL . 'css/ninja-forms-display.css?nf_ver=' . NF_PLUGIN_VERSION );
		}

		do_action( 'pootlepb_enqueue_admin_scripts' );

	}

	protected function l10n_scripts() {
		global $post;

		//Grid data
		$panels_data = get_post_meta( $post->ID, 'panels_data', true );
		if ( count( $panels_data ) > 0 ) {
			wp_localize_script( 'pootle-live-editor', 'ppbData', $panels_data );
		}
		wp_localize_script( 'pootle-live-editor', 'ppbPost', array(
			'status' => get_post_status(),
		) );

		//Fix: panels undefined
		wp_localize_script( 'ppb-fields', 'panels', array() );

		//Ajax
		$ppbAjax = array(
			'site'   => site_url(),
			'url'    => admin_url( 'admin-ajax.php' ),
			'action' => 'pootlepb_live_editor',
			'post'   => $post->ID,
			'nonce'  => $this->nonce,
			'user'   => $this->user,
		);
		if ( $this->ipad ) {
			$ppbAjax['ipad']     = 1;
			$ppbAjax['ppb-ipad'] = 1;
		}

		if ( $this->edit_title || ( $this->ipad && 'draft' == get_post_status() ) ) {
			$ppbAjax['title'] = get_the_title();
		}
		wp_localize_script( 'pootle-live-editor', 'ppbAjax', $ppbAjax );
		wp_localize_script( 'pootle-live-editor', 'ppbModules', array() );

		//Colorpicker
		$colorpicker_l10n = array(
			'clear'         => __( 'Clear' ),
			'defaultString' => __( 'Default' ),
			'pick'          => __( 'Select Color' ),
			'current'       => __( 'Current Color' ),
		);
		wp_localize_script( 'pp-pb-color-picker', 'wpColorPicker_i18n', $colorpicker_l10n );
	}

	/**
	 * Wraps the content in .pootle-live-editor-realtime and convert short codes to strings
	 *
	 * @param string $content
	 *
	 * @return string Content
	 */
	public function content( $content ) {
		if ( ! $this->_active ) {
			return $content;
		}

		$content = str_replace( array( '[', ']' ), array( '&#91;', '&#93;' ), $content );

		return "<div class='pootle-live-editor-realtime'>$content</div>";
	}

	/**
	 * Saves setting from front end via ajax
	 * @since 1.1.0
	 */
	public function sync() {
		if ( $this->init_live_editing() ) {
			$id = $_POST['post'];

			if ( ! empty( $_POST['data'] ) ) {

				foreach ( $_POST['data']['widgets'] as $i => $wid ) {
					if ( ! empty( $wid['info']['style'] ) ) {
						$_POST['data']['widgets'][ $i ]['info']['style'] = wp_unslash( $wid['info']['style'] );
						$_POST['data']['widgets'][ $i ]['text']          = wp_unslash( $wid['text'] );
					}
				}

			}
			if ( filter_input( INPUT_POST, 'publish' ) ) {
				// Update post
				$live_page_post = array( 'ID' => $id );

				if ( 'Publish' == filter_input( INPUT_POST, 'publish' ) ) {
					$live_page_post['post_status'] = 'publish';
				}

				$live_page_post = $this->savePostMeta( $live_page_post );

				/**
				 * Fired before saving pootle page builder post meta
				 *
				 * @param array $ppb_data Page builder data
				 * @param Int $post_id Post ID
				 * @param WP_Post $post Post object
				 */
				do_action( 'pootlepb_save_post', $_POST['data'], $id, get_post( $id ) );

				foreach ( $_POST['data']['widgets'] as $i => $wid ) {
					if ( ! empty( $wid['info']['style'] ) ) {
						$_POST['data']['widgets'][ $i ]['info']['style'] = wp_slash( $wid['info']['style'] );
						$_POST['data']['widgets'][ $i ]['text']          = wp_slash( $wid['text'] );
					}
				}
				// Update PPB data
				update_post_meta( $id, 'panels_data', $_POST['data'] );

				// Generate post content
				$post_content = pootlepb_text_content( '', (object) $live_page_post );
				if ( $post_content ) {
					$live_page_post['post_content'] = $post_content;
				}

				// Update post
				wp_update_post( $live_page_post, true );

				echo get_permalink( $id );
			} else {
				query_posts( array(
					'post_type'           => 'any',
					'post_status'         => 'any',
					'post__in'            => array( $id ),
					'ignore_sticky_posts' => 1,
				) );

				if ( have_posts() ) {
					$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
					while ( have_posts() ) {
						the_post();

						//$live_page_post = $this->savePostMeta( array( 'ID' => $id ) );

						// Update post
						//wp_update_post( $live_page_post, true );
						echo Pootle_Page_Builder_Render_Layout::render( $id, $_POST['data'] );
					}
				} else {
					// Return data even if the post can't be queried
					echo Pootle_Page_Builder_Render_Layout::render( $id, $_POST['data'] );
				}
			}
		}
		die();
	}

	/**
	 * Adds the actions anf filter hooks for plugin
	 * @since 1.1.0
	 */
	public function init_live_editing() {
		global $post;

		//Checking nonce
		$nonce       = filter_input( INPUT_GET, 'ppbLiveEditor' );
		$this->nonce = $nonce ? $nonce : filter_input( INPUT_POST, 'nonce' );
		$user        = filter_input( INPUT_POST, 'user' );
		$this->user  = $user = $user ? $user : filter_input( INPUT_GET, 'user' );

		//Post ID
		$id            = $post ? $post->ID : filter_input( INPUT_POST, 'post' );
		$this->post_id = $id;

		if ( isset( $_COOKIE['ppb-ipad'] ) ) {
			add_filter( 'show_admin_bar', '__return_false' );
		}

		if ( isset( $_REQUEST['ppb-ipad'] ) ) {
			if ( stristr( $_SERVER['HTTP_USER_AGENT'], '(iPad;' ) ) {
				setcookie( 'ppb-ipad', 'true' );
			}
			add_filter( 'show_admin_bar', '__return_false' );
			if ( $this->nonce === get_transient( 'ppb-ipad-' . $user ) ) {
				$this->ipad = true;
				$this->actions();
				add_action( 'wp_head', array( $this, 'ipad_html' ) );

				return true;
			}
		} else if ( wp_verify_nonce( $this->nonce, 'ppb-live-edit-nonce' ) || wp_verify_nonce( $this->nonce, 'ppb-live-' . $id ) ) {
			$this->actions();

			return true;
		} else {
			return null;
		}
	}

	public function actions() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			function is_plugin_active() {
				return 0;
			}
		}

		$this->_active = true;

		do_action( 'pootlepb_live_editor_init' );

		$this->addons = apply_filters( 'pootlepb_le_content_block_tabs', array() );

		remove_filter( 'pootlepb_content_block', array(
			$GLOBALS['Pootle_Page_Builder_Content_Block'],
			'auto_embed'
		), 8 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 99 );
		add_action( 'pootlepb_before_pb', array( $this, 'before_pb' ), 7, 4 );
		add_action( 'pootlepb_render_content_block', array( $this, 'edit_content_block' ), 7, 4 );
		add_action( 'pootlepb_render', array( $this, 'mark_active' ), 999 );
		add_filter( 'mce_external_plugins', array( $this, 'tinymce_plugin' ) ); // PPBlink tmce plugin

		if ( ! empty( $_GET['edit_title'] ) ) {
			$this->edit_title = true;
		}

		add_filter( 'pootlepb_row_cell_attributes', array( $this, 'cell_attr' ), 10, 3 );
		add_filter( 'pootlepb_content_block', array( $this, 'content' ), 5 );
		add_action( 'pootlepb_before_row', array( $this, 'edit_row' ), 7, 2 );
		add_action( 'pootlepb_after_content_blocks', array( $this, 'column' ) );
		add_action( 'pootlepb_after_pb', array( $this, 'add_row' ) );
		add_action( 'wp_footer', array( $this, 'dialogs' ), 7 );
		add_filter( 'pootlepb_rag_adjust_elements', '__return_empty_array', 999 );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter( 'post_class', array( $this, 'post_type_class' ), 10, 3 );

		do_action( 'pootlepb_live_editor_after_init' );
	}

	/**
	 * Saves title, thumbnail, categories, tags etc. for post
	 *
	 * @param array $live_page_post
	 *
	 * @return array
	 */
	public function savePostMeta( $live_page_post ) {
		$id = $live_page_post['ID'];
		if ( ! empty( $_POST['title'] ) ) {
			$live_page_post['post_title'] = $_POST['title'];
		}
		if ( ! empty( $_POST['thumbnail'] ) ) {
			set_post_thumbnail( $id, $_POST['thumbnail'] );
		}

		if ( ! empty( $_POST['category'] ) ) {
			wp_set_post_terms( $id, $_POST['category'], "category", false );
		}

		if ( ! empty( $_POST['tags'] ) ) {
			wp_set_post_terms( $id, $_POST['tags'], "post_tag", false );
		}

		return $live_page_post;
	}

	/**
	 * Reset panel index
	 * @since 1.1.0
	 */
	public function before_pb() {
		$this->pi = 0;
	}

	/**
	 * Adds front end grid edit ui
	 *
	 * @param array $data
	 * @param int $gi
	 *
	 * @since 1.1.0
	 */
	public function edit_row( $data, $gi = 0 ) {
		if ( ! $this->_active || $this->post_id != get_the_ID() ) {
			return;
		}
		?>
		<div class="pootle-live-editor ppb-live-edit-object ppb-edit-row" data-index="<?php echo $gi; ?>"
				 data-i_bkp="<?php echo $gi; ?>">
			<span href="javascript:void(0)" title="Row Styling" class="dashicons-before settings-dialog dashicons-edit">
				<span class="screen-reader-text">Edit Row</span>
			</span>
			<span href="javascript:void(0)" title="Row Sorting" class="dashicons-before drag-handle dashicons-editor-code">
				<span class="screen-reader-text">Sort row</span>
			</span>
			<?php /*
 			<span href="javascript:void(0)" title="Insert Row" class="dashicons-before insert-row dashicons-plus">
				<span class="screen-reader-text">Insert row</span>
			</span>
 			*/ ?>
			<?php /*
			<span href="javascript:void(0)" title="Duplicate Row" class="dashicons-before dashicons-admin-page">
				<span class="screen-reader-text">Duplicate Row</span>
			</span>
			*/ ?>
			<span href="javascript:void(0)" title="Delete Row" class="dashicons-before dashicons-no">
				<span class="screen-reader-text">Delete Row</span>
			</span>
		</div>
		<?php
	}

	/**
	 * Edit content block icons
	 */
	public function edit_content_block( $content_block ) {
		if ( ! $this->_active ) {
			return;
		}
		?>
		<div class="pootle-live-editor ppb-live-edit-object ppb-edit-block"
				 data-index="<?php echo $content_block['info']['id']; ?>"
				 data-i_bkp="<?php echo $content_block['info']['id']; ?>">
			<span href="javascript:void(0)" title="Drag content block" class="dashicons-before drag-handle dashicons-move">
				<span class="screen-reader-text">Drag content block</span>
			</span>
			<span href="javascript:void(0)" title="Edit Content" class="dashicons-before settings-dialog dashicons-edit">
				<span class="screen-reader-text">Edit Content Block</span>
			</span>
			<?php /*
			<span href="javascript:void(0)" title="Insert image" class="dashicons-before dashicons-format-image">
				<span class="screen-reader-text">Insert image</span>
			</span>
			<?php
			if ( ! empty( $this->addons ) ) {
				?>
				<span href="javascript:void(0)" title="Addons"
				      class="dashicons-before dashicons-admin-plugins pootle-live-editor-addons">
					<span class="screen-reader-text">Add ons</span>
					<span class="pootle-live-editor-addons-list">
					<?php
					foreach ( $this->addons as $id => $addon ) {
						$addon = wp_parse_args( $addon, array( 'icon' => 'dashicons-star-filled', ) );
						echo
							"<span href='javascript:void(0)' data-id='$id' title='$addon[label]' class='pootle-live-editor-addon dashicons-before $addon[icon]'>" .
							"<span class='addon-text'><span class='addon-label'>$addon[label]</span></span>" .
							"</span>";
					}
					?>
					</span>
				</span>
				<?php
			}
			*/ ?>
			<span href="javascript:void(0)" title="Delete Content" class="dashicons-before delete dashicons-no">
				<span class="screen-reader-text">Delete Content</span>
			</span>
		</div>
		<div class="ui-resizable-handle ui-resizable-e">
			<div class="top"></div>
			<div class="bottom"></div>
		</div>
		<div class="ui-resizable-handle ui-resizable-w">
			<div class="top"></div>
			<div class="bottom"></div>
		</div>
		<?php
	}

	/**
	 * Edit content block icons
	 */
	public function cell_attr( $attr, $ci, $gi ) {
		$attr['data-index'] = $ci;

		return $attr;
	}

	public function mark_active( $pb_html ) {
		if ( ! $this->_active ) {
			$this->_active = true;
		}

		return $pb_html;
	}

	/**
	 * Edit content block icons
	 */
	public function add_row() {
		if ( ! $this->_active ) {
			return;
		}
		?>
		<div class="pootle-live-editor  ppb-live-add-object add-row">
			<span href="javascript:void(0)" title="Add row" class="dashicons-before dashicons-plus">
				<span class="screen-reader-text">Add row</span>
			</span>
		</div>
		<?php
	}

	/**
	 * Edit content block icons
	 */
	public function column() {
		if ( ! $this->_active ) {
			return;
		}
		/*
				<div class="pootle-live-editor ppb-live-add-object add-content">
					<span href="javascript:void(0)" title="Add Content" class="dashicons-before dashicons-plus">
						<span class="screen-reader-text">Add Content</span>
					</span>
				</div>
				*/ ?>
		<div class="pootle-live-editor resize-cells"></div>
		<div class="pootle-guides"></div>
		<?php
	}

	/**
	 * Magic __construct
	 * @since 1.1.0
	 */
	public function dialogs() {
		require 'tpl-dialogs.php';
	}

	public function ipad_html() {
		include "tpl-ipad-html.php";
	}
}
