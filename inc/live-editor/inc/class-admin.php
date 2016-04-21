<?php
/**
 * Pootle Page Builder Live Editor Admin class
 * @property string token Plugin token
 * @property string $url Plugin root dir url
 * @property string $path Plugin root dir path
 * @property string $version Plugin version
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
	 * @since 	1.0.0
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
	 * @since 	1.0.0
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
	 * @since 	1.0.0
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
	#wp-admin-bar-ppb-publish-parent > a:before,
	#wp-admin-bar-ppb-publish a:before {
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
					'id'		=> 'ppb-publish-parent',
					'title'		=> 'Save/Publish',
					'href'		=> '#ppb-live-save-publish',
					'meta'		=> array(
						'title' => __( 'Save and publish your changes.' ),
					),
				);
				$admin_bar->add_menu( $args );

				$args['parent'] = 'ppb-publish-parent';
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
		$this->new_live_post();
	}

	/**
	 * Saves setting from front end via ajax
	 * @param string $post_type Post type to create
	 * @since 1.1.0
	 */
	public function new_live_post( $post_type = 'page' ) {
		$nonce = filter_input( INPUT_GET, 'ppbLiveEditor' );
		if ( wp_verify_nonce( $nonce, 'ppb-new-live-post' ) ) {

			$id = wp_insert_post( array(
				'post_title'    => 'Untitled',
				'post_content'  => '',
				'post_type'  => $post_type,
			) );

			if ( $id ) {
//				$ppble_quotes = $ppble_new_live_page = array();
				require 'vars.php';

				$user = '';
				$current_user = wp_get_current_user();
				if ( $current_user instanceof WP_User ) {
					$user = ' ' . ucwords( $current_user->user_login );
				}

				$ppble_new_live_page['widgets'][0]['text'] = str_replace( '<!--USER-->', $user, $ppble_new_live_page['widgets'][0]['text'] );

				update_post_meta( $id, 'panels_data', $ppble_new_live_page );
				$nonce_url = wp_nonce_url( get_the_permalink( $id ), 'ppb-live-' . $id, 'ppbLiveEditor' );
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
}