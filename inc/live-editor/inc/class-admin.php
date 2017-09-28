<?php
/**
 * Pootle Page Builder Live Editor Admin class
 * @property string token Plugin token
 * @property string $url Plugin root dir url
 * @property string $path Plugin root dir path
 * @property string $version Plugin version
 * @since 2.0.0
 */
class Pootle_Page_Builder_Live_Editor_Admin {

	/** @var Pootle_Page_Builder_Live_Editor_Admin Instance */
	private static $_instance = null;

	/** @var int Panel Index */
	protected $pi = 0;

	/**
	 * Main Pootle Page Builder Live Editor Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @return Pootle_Page_Builder_Live_Editor instance
	 * @since 2.0.0
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	} // End instance()

	/**
	 * Constructor function.
	 * @access  private
	 * @since 2.0.0
	 */
	private function __construct() {
		$this->token   = Pootle_Page_Builder_Live_Editor::$token;
		$this->url     = Pootle_Page_Builder_Live_Editor::$url;
		$this->path    = Pootle_Page_Builder_Live_Editor::$path;
		$this->version = Pootle_Page_Builder_Live_Editor::$version;
	}

	/**
	 * Constructor function.
	 * @access  private
	 * @since 2.0.0
	 */
	public function enqueue() {
		global $post;

		$url = $this->url . 'assets/';
		wp_enqueue_script( 'pootle-live-editor-js', "{$url}admin.js" );
		wp_enqueue_style( 'pootle-live-editor-css', "{$url}admin.css" );

		// Live preview nonce
		$nonce_url = wp_nonce_url( get_the_permalink( $post->ID ), 'ppb-live-' . $post->ID, 'ppbLiveEditor' );
		wp_localize_script( 'pootle-live-editor-js', 'live_editor_url', $nonce_url );
	}

	/**
	 * @param $admin_bar
	 */
	function admin_bar_menu( $admin_bar ) {
		?>
		<style>
			#wpadminbar #wp-admin-bar-pootle-live-editor .ab-item:before {
				content: "\f180";
				top: 1px;
			}

			#wpadminbar li#wp-admin-bar-ppb-publish {
				display: block;
			}

			#wp-admin-bar-ppb-publish > a:before {
				content: "\f319";
				top: 2px;
			}

			li[id^="wp-admin-bar-ppb-"] a {
				clear: both;
			}

			li[id^="wp-admin-bar-ppb-new-live-"] a:before {
				content: '\f180';
			}
			li#wp-admin-bar-ppb-live-post-settings a:before {
				content: "\f111";
			}
		</style>
		<?php
		global $post;
		//Checking nonce
		$nonce = filter_input( INPUT_GET, 'ppbLiveEditor' );

		$new_live_page_url = admin_url( 'admin-ajax.php' );
		$new_live_page_url = wp_nonce_url( $new_live_page_url, 'ppb-new-live-post', 'ppbLiveEditor' ) . '&action=pootlepb_live_page';

		$admin_bar->add_menu( array(
			'parent' => 'new-content',
			'id'     => 'ppb-new-live-page',
			'title'  => 'Live Page',
			'href'   => $new_live_page_url
		) );
		$admin_bar->add_menu( array(
			'parent' => 'new-content',
			'id'     => 'ppb-new-live-post',
			'title'  => 'Live Post',
			'href'   => $new_live_page_url . '&post_type=post'
		) );

		if ( wp_verify_nonce( $nonce, 'ppb-live-edit-nonce' ) || wp_verify_nonce( $nonce, 'ppb-live-' . get_the_id() ) ) {
			if ( 'draft' == Pootle_Page_Builder_Live_Editor_Public::instance()->post_status() ) {
				$args = array(
//					'parent' => 'top-secondary',
					'id'    => 'ppb-publish',
					'title' => 'Save/Publish',
					'href'  => '#ppb-live-save-publish',
					'meta'  => array(
						'title' => __( 'Save and publish your changes.' ),
					),
				);
				$admin_bar->add_menu( $args );

				$args['parent'] = 'ppb-publish';

				if ( 'post' == get_post_type() ) {
					$args['id']    = 'ppb-live-post-settings';
					$args['href']  = '#ppb-live-post-settings';
					$args['title'] = 'Post settings';
					$admin_bar->add_menu( $args );
				}

				$args['id']     = 'ppb-live-update-changes';
				$args['href']   = '#ppb-live-update-changes';
				$args['title']  = 'Save';
				$admin_bar->add_menu( $args );

				$args['id']    = 'ppb-live-publish-changes';
				$args['href']  = '#ppb-live-publish-changes';
				$args['title'] = 'Publish';
				$admin_bar->add_menu( $args );
			} else {
				$args = array(
//					'parent' => 'top-secondary',
					'id'    => 'ppb-publish',
					'title' => 'Update',
					'href'  => '#ppb-live-update-changes',
					'meta'  => array(
						'title' => __( 'Save and publish your changes.' ),
					),
				);
				$admin_bar->add_menu( $args );

				if ( 'post' == get_post_type() ) {
					$args['parent'] = 'ppb-publish';
					$args['id']    = 'ppb-live-post-settings';
					$args['href']  = '#ppb-live-post-settings';
					$args['title'] = 'Post settings';
					$admin_bar->add_menu( $args );
				}
			}

		} else if ( pootlepb_is_panel( true ) ) {
			$nonce_url = wp_nonce_url( get_the_permalink( $post->ID ), 'ppb-live-' . $post->ID, 'ppbLiveEditor' );
			$args      = array(
				'id'    => 'pootle-live-editor',
				'title' => 'Live edit',
				'href'  => $nonce_url,
				'meta'  => array(
					'title' => __( 'Live edit this page with pootle page builder' ),
				),
			);
			$admin_bar->add_menu( $args );
		}
	}

	/**
	 * Filters the row actions
	 */
	public function post_row_actions() {

	}

	/**
	 * Saves setting from front end via ajax
	 * @since 1.1.0
	 */
	public function new_live_page() {

		$requested_post_type = filter_input( INPUT_GET, 'post_type' );
		$post_type           = $requested_post_type ? $requested_post_type : 'page';

		$this->new_live_post( $post_type );
	}

	/**
	 * Saves setting from front end via ajax
	 *
	 * @param string $post_type Post type to create
	 *
	 * @since 1.1.0
	 */
	public function new_live_post( $post_type = 'page' ) {
		$nonce     = filter_input( INPUT_GET, 'ppbLiveEditor' );
		$ipad_user = filter_input( INPUT_GET, 'user' );

		if ( ! is_user_logged_in() ) {
			if ( $ipad_user ) {
				echo "<h2>Hi $ipad_user</h2><h3>Please login again to continue building...</h3>";
			} else {
				?><h1>You need to be logged in to be able to do that!</h1><?php
			}
			exit;
		}
		$mobile_editing = wp_verify_nonce( $nonce, 'ppb-mobile-editing' );

		if ( $mobile_editing && is_user_logged_in() ) {
			$mobile_app_url = apply_filters( 'pootlepb_mobile_app_url', 'localhost:4200' );
			@header( "X-Frame-Options: ALLOW-FROM $mobile_app_url" );
		}

		if ( $mobile_editing || wp_verify_nonce( $nonce, 'ppb-new-live-post' ) ) {
			$id = wp_insert_post( array(
				'post_title'   => 'Untitled',
				'post_content' => '',
				'post_type'    => $post_type,
			) );

			if ( is_numeric( $id )
			) {
				global $ppble_new_live_page;

				require 'vars.php';

				$user         = '';
				$current_user = wp_get_current_user();
				if ( $current_user instanceof WP_User ) {
					$user = ' ' . ucwords( $current_user->user_login );
				}

				/**
				 * Filters new live page template
				 *
				 * @param int $id Post ID
				 */
				$ppb_data = apply_filters( 'pootlepb_live_page_template', $ppble_new_live_page, $id, $post_type );

				foreach ( $ppb_data['widgets'] as $i => $wid ) {
					if ( ! empty( $wid['info']['style'] ) ) {
						$ppb_data['widgets'][ $i ]['info']['style'] = stripslashes( $wid['info']['style'] );
					}
					$ppb_data['widgets'][ $i ]['text'] = html_entity_decode( stripslashes( str_replace( '<!--USER-->', $user, str_replace( '&nbsp;', '&amp;nbsp;', $wid['text'] ) ) ) );
				}

				update_post_meta( $id, 'panels_data', $ppb_data );
				$plink = get_the_permalink( $id );

				$plink .= strpos( $plink, '?' ) ? "&" : '?';
				
				if ( isset( $_GET['tour'] ) ) {
					$_GET['tour'] = $_GET['tour'] ? $_GET['tour'] : 1;
					$plink .= "tour=$_GET[tour]&";
				}
				if ( $mobile_editing ) {

					header( "Location: {$plink}ppb-mobile-editing={$nonce}" );
					exit();
				}

				if ( ! get_option( 'pootlepb_le_tour_done' ) ) {
					update_option( 'pootlepb_le_tour_done', 'done' );
					$plink .= '&tour=1';
				}

				$nonce_url = wp_nonce_url( $plink, 'ppb-live-' . $id, 'ppbLiveEditor' );
				$nonce_url = html_entity_decode( $nonce_url );
				header( "Location: $nonce_url&edit_title=true" );
				exit();
			}
		} else {
			?>
			<h1>Sorry,<br>Nonce validation failed...</h1>
			<?php
		}
		die();
	}

	public function browser_cache_page() {
		if ( empty( $_GET['pootle_pb_ipad_cache_helper'] ) ) {
			return;
		}

		Pootle_Page_Builder_Live_Editor::instance( __FILE__ )->public->actions();
		get_header();

		$user = '';
		$current_user = wp_get_current_user();
		if ( ! empty( $current_user->user_login ) ) {
			$user = ' ' . $current_user->user_login;
		}
		?>
		<script>
			ppbData = {"widgets":[{"text":"<h2>Hello shramee,</h2><p>I am your first row, go ahead, tap in me to start editing...</p>","info":{"class":"Pootle_PB_Content_Block","grid":0,"style":"{\"background-color\":\"\",\"background-transparency\":\"\",\"text-color\":\"\",\"border-width\":\"\",\"border-color\":\"\",\"padding\":\"\",\"rounded-corners\":\"\",\"inline-css\":\"\",\"class\":\"\",\"wc_prods-add\":\"\",\"wc_prods-attribute\":\"\",\"wc_prods-filter\":null,\"wc_prods-ids\":null,\"wc_prods-category\":null,\"wc_prods-per_page\":\"\",\"wc_prods-columns\":\"\",\"wc_prods-orderby\":\"\",\"wc_prods-order\":\"\"}","cell":0,"id":0}}],"grids":[{"id":0,"cells":1,"style":{"background":"","background_image":"","background_image_repeat":"","background_image_size":"cover","background_parallax":"","background_toggle":"","bg_color_wrap":"","bg_image_wrap":"","bg_mobile_image":"","bg_overlay_color":"","bg_overlay_opacity":"0.5","bg_video":"","bg_video_wrap":"","bg_wrap_close":"","class":"","col_class":"","col_gutter":"1","full_width":"","hide_row":"","margin_bottom":"0","margin_top":"0","row_height":"0","style":""}}],"grid_cells":[{"grid":0,"weight":0.9983,"id":0}]}
		</script>
		<div id="pootle-page-builder">
			<div class="panel-grid ppb-row" id="pg-522-0" style="margin-bottom: 0;">
				<div class="pootle-live-editor ppb-live-edit-object ppb-edit-row" data-index="0" data-i_bkp="0">
					<span title="Row Sorting" class="dashicons-before dashicons-editor-code">
						<span class="screen-reader-text">Sort row</span>
						<span class="intro-popup">
							<b>Touch and drag</b> to sort row.<br>
							<b>Tap</b> to show row options.<br>
							<b>Double tap</b> to open row settings.
						</span>
					</span>
					<span title="Row Styling" class="dashicons-before dashicons-admin-appearance">
						<span class="screen-reader-text">Edit Row</span>
					</span>
					<span title="Delete Row" class="dashicons-before dashicons-no">
						<span class="screen-reader-text">Delete Row</span>
					</span>
				</div>
				<div class="ppb-row panel-row-style panel-row-style- " style="">
					<style>
						#pg-522-0 .panel-grid-cell {
							padding: 0 0.5% 0;
						}
					</style>
					<div class="panel-grid-cell-container">
						<div class="ppb-col panel-grid-cell " id="pgc-522-0-0" data-index="0">
							<div id="panel-522-0-0-0" class="ppb-block ppb-no-mobile-spacing" style="">
								<div class="pootle-live-editor ppb-live-edit-object ppb-edit-block" data-index="0"
								     data-i_bkp="0">
			<span title="Drag and Drop content block"
			      class="dashicons-before dashicons-move">
				<span class="screen-reader-text">Sort Content Block</span>
				<span class="intro-popup">
					<b>Touch and drag</b> to sort content block.<br>
					<b>Double tap</b> to open content block settings.
				</span>
			</span>
			<span title="Edit Content" class="dashicons-before dashicons-edit">
				<span class="screen-reader-text">Edit Content Block</span>
			</span>
			<span title="Insert Image" class="dashicons-before dashicons-format-image">
				<span class="screen-reader-text">Insert Image</span>
			</span>
							<span title="Addons"
							      class="dashicons-before dashicons-admin-plugins pootle-live-editor-addons">
					<span class="screen-reader-text">Add ons</span>
					<span class="pootle-live-editor-addons-list">
					<span data-id="ppb-blog-customizer" title="Posts"
					      class="pootle-live-editor-addon dashicons-before dashicons-admin-post">
						<span class="addon-text"><span class="addon-label">Posts</span></span>
					</span>
					<span data-id="ppb-photo-addon" title="Photos"
					      class="pootle-live-editor-addon dashicons-before dashicons-star-filled">
						<span class="addon-text"><span class="addon-label">Photos</span></span>
					</span>
					<span data-id="wc_prods" title="Products"
					      class="pootle-live-editor-addon dashicons-before dashicons-cart">
						<span class="addon-text"><span class="addon-label">Products</span></span>
					</span>
					</span>
				</span>
							<span title="Delete Content"
							      class="dashicons-before dashicons-no">
				<span class="screen-reader-text">Delete Content</span>
			</span>
								</div>
								<div class="pootle-live-editor-realtime"><h2>Hello<?php echo $user ?>,</h2>
									<p>I am your first row, go ahead, tap in me to start editing...</p></div>
							</div>
							<div class="pootle-live-editor resize-cells"></div>
						</div>
					</div><!--.panel-grid-cell-container--></div><!--.panel-row-style--></div><!--.panel-grid-->
			<div class="pootle-live-editor  ppb-live-add-object add-row">
			<span title="Add row" class="dashicons-before dashicons-plus">
				<span class="screen-reader-text">Add row</span>
			</span>
			</div>
			<!----------Pootle Page Builder Inline Styles---------->
			<style id="pootle-live-editor-styles" type="text/css" media="all">.panel-grid-cell {
					display: inline-block !important;
					vertical-align: top !important;
				}

				.panel-grid-cell-container {
					font-size: 0;
				}

				.panel-grid-cell-container > * {
					font-size: initial;
				}

				#pootle-page-builder, .panel-row-style, .panel-grid-cell-container {
					position: relative;
				}

				.panel-grid-cell-container {
					z-index: 1;
				}

				.panel-grid-cell-container {
					padding-bottom: 1px;
				}

				.panel-row-style:before {
					position: absolute;
					width: 100%;
					height: 100%;
					content: "";
					top: 0;
					left: 0;
					z-index: 0;
				}

				.panel-grid-cell .panel {
					margin-bottom: 0px
				}

				.panel-grid-cell .panel:last-child {
					margin-bottom: 0 !important
				}

				.panel {
					padding: 10px
				}

				@media ( max-width: 780px ) {
					#pg-522-0 .panel-grid-cell {
						float: none
					}

					#pg-522-0 .panel-grid-cell {
						width: auto
					}

					.panel-grid-cell:not(:last-child) {
						margin-bottom: 10px
					}

					.panel-grid {
						margin-left: 0 !important;
						margin-right: 0 !important;
					}

					.panel-grid-cell {
						padding: 0 !important;
						width: 100% !important;
					}
				}

				@media ( max-width: 768px ) {
					.panel {
						padding: 5px
					}
				}

				.panel-grid:hover .ppb-edit-row span.intro-popup,
				.ppb-block:hover .pootle-live-editor span.intro-popup{
					visibility: visible;
				}

				span.intro-popup {
					position: absolute;
				}

				#pootle-page-builder:hover .ppb-row:hover span.intro-popup {
					background: #fff;
					border: 1px solid #aaa;
					width: 160px;
					display: block !important;
					padding: 16px;
					top: 97%;
					left: 0;
					box-shadow: 0 1px 3px 0px rgba(0,0,0,0.25);
				}

				#pootle-page-builder:hover .ppb-row:hover .ppb-block span.intro-popup {
					top: 106%;
					left: auto;
					right:0
				}

				span.intro-popup:before {
					position: absolute;
					content: '';
					border: 12px solid transparent;
					border-bottom: 10px solid #aaa;
					bottom:100%;
					left:5px;
				}

				.ppb-block span.intro-popup:before {
					left:auto;
					right:5px;
				}
			</style>
		</div>
		<?php

		get_footer();
		exit();
	}
}
