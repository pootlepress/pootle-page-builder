<?php
/**
 * Pootle Page Builder Live Editor Admin class
 * @property string token Plugin token
 * @property string $url Plugin root dir url
 * @property string $path Plugin root dir path
 * @property string $version Plugin version
 * @since 2.0.0
 */
class Pootle_Page_Builder_Live_Editor_Admin{

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
		$this->token   =   Pootle_Page_Builder_Live_Editor::$token;
		$this->url     =   Pootle_Page_Builder_Live_Editor::$url;
		$this->path    =   Pootle_Page_Builder_Live_Editor::$path;
		$this->version =   Pootle_Page_Builder_Live_Editor::$version;
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
	function add_item( $admin_bar ) {
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
	li[id^="wp-admin-bar-ppb-new-live-"] a {
		clear: both;
	}
	li[id^="wp-admin-bar-ppb-new-live-"] a:before {
		content: '\f180';
	}
</style>
		<?php
		global $post;
		//Checking nonce
		$nonce = filter_input( INPUT_GET, 'ppbLiveEditor' );

		$new_live_page_url = admin_url( 'admin-ajax.php' );
		$new_live_page_url = wp_nonce_url( $new_live_page_url, 'ppb-new-live-post', 'ppbLiveEditor' );
		$admin_bar->add_menu( array(
			'parent'    => 'new-content',
			'id'        => 'ppb-new-live-page',
			'title'     => 'Live Page',
			'href'      => $new_live_page_url . '&action=pootlepb_live_page'
		) );

		if ( wp_verify_nonce( $nonce, 'ppb-live-' . get_the_id() ) ) {
			if ( 'draft' == Pootle_Page_Builder_Live_Editor_Public::instance()->post_status() ) {
				$args = array(
					'id'		=> 'ppb-publish',
					'title'		=> 'Save/Publish',
					'href'		=> '#ppb-live-save-publish',
					'meta'		=> array(
						'title' => __( 'Save and publish your changes.' ),
					),
				);
				$admin_bar->add_menu( $args );

				$args['parent'] = 'ppb-publish';
				$args['id'] = 'ppb-live-update-changes';
				$args['href'] = '#ppb-live-update-changes';
				$args['title'] = 'Save';
				$admin_bar->add_menu( $args );

				$args['id'] = 'ppb-live-publish-changes';
				$args['href'] = '#ppb-live-publish-changes';
				$args['title'] = 'Publish';
				$admin_bar->add_menu( $args );
			} else {
				$args = array(
					'id'    => 'ppb-publish',
					'title' => 'Update',
					'href'  => '#ppb-live-update-changes',
					'meta'  => array(
						'title' => __( 'Save and publish your changes.' ),
					),
				);
				$admin_bar->add_menu( $args );
			}
		} else if ( pootlepb_is_panel( true ) ) {
			$nonce_url = wp_nonce_url( get_the_permalink( $post->ID ), 'ppb-live-' . $post->ID, 'ppbLiveEditor' );
			$args = array(
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
	 * Saves setting from front end via ajax
	 * @since 1.1.0
	 */
	public function new_live_page() {
		$requested_post_type = filter_input( INPUT_GET, 'post' );
		$post_type = $requested_post_type ? $requested_post_type : 'page';

		$this->new_live_post( $post_type );
	}

	/**
	 * Saves setting from front end via ajax
	 * @param string $post_type Post type to create
	 * @since 1.1.0
	 */
	public function new_live_post( $post_type = 'page' ) {
		$nonce = filter_input( INPUT_GET, 'ppbLiveEditor' );
		$ios_user = filter_input( INPUT_GET, 'user' );
		if ( $nonce === get_transient( 'ppb-ios-' . $ios_user ) || wp_verify_nonce( $nonce, 'ppb-new-live-post' ) ) {

			$id = wp_insert_post( array(
				'post_title'    => 'Untitled',
				'post_content'  => '',
				'post_type'  => $post_type,
			) );

			if ( is_numeric( $id )
			) {
				global $ppble_new_live_page;

				require 'vars.php';

				$user = '';
				$current_user = wp_get_current_user();
				if ( $current_user instanceof WP_User ) {
					$user = ' ' . ucwords( $current_user->user_login );
				}

				/**
				 * Filters new live page template
				 * @param int $id Post ID
				 */
				$ppb_data = apply_filters( 'pootlepb_live_page_template', $ppble_new_live_page, $id );

				foreach ( $ppb_data['widgets'] as $i => $wid ) {
					if ( ! empty( $wid['info']['style'] ) ) {
						$ppb_data['widgets'][ $i ]['info']['style'] = stripslashes( $wid['info']['style'] );
					}
					$ppb_data['widgets'][ $i ]['text'] = html_entity_decode( stripslashes( str_replace( '<!--USER-->', $user, str_replace( '&nbsp;', '&amp;nbsp;', $wid['text'] ) ) ) );
				}

				update_post_meta( $id, 'panels_data', $ppb_data );
				$plink = get_the_permalink( $id );

				if ( isset( $_GET['ppb-ios'] ) ) {
					header("Location: $plink?ppb-ios&user=$ios_user&ppbLiveEditor=$nonce&edit_title=true");
					exit();
				}
				$nonce_url = wp_nonce_url( $plink, 'ppb-live-' . $id, 'ppbLiveEditor' );
				$nonce_url = html_entity_decode( $nonce_url );
				header("Location: $nonce_url&edit_title=true");
				exit();
			}
		} else {
			?>
			<h1>Sorry,<br>Nonce validation failed...</h1>
			<?php
		}
		die();
	}

	public function browser_cache_page () {
		if ( empty( $_GET['pootle_pb_ios_cache_helper'] ) ) {
			return;
		}

		Pootle_Page_Builder_Live_Editor::instance( __FILE__ )->public->actions();
		get_header();
		global $ppble_new_live_page;
		require 'vars.php';

		echo $GLOBALS['Pootle_Page_Builder_Render_Layout']->panels_render( 1, $ppble_new_live_page );

		get_footer();
		exit();
	}
}
