<?php
/**
 * Contains Pootle_Page_Builder_Content_Block class
 * @author pootlepress
 * @since 0.1.0
 */

/**
 * Class Pootle_Page_Builder_Content_Block
 * @since 0.1.0
 */
final class Pootle_Page_Builder_Content_Block {
	/**
	 * @var Pootle_Page_Builder_Content_Block Instance
	 * @since 0.1.0
	 */
	protected static $instance;

	/**
	 * Magic __construct
	 * @since 0.1.0
	 */
	public function __construct() {
		add_filter( 'pootlepb_content_block', array( $this, 'do_shortcode' ) );
		add_filter( 'pootlepb_content_block', array( $this, 'auto_embed' ), 8 );
		add_action( 'pootlepb_render_content_block', array( $this, 'open_block' ), 5, 6 );
		add_action( 'pootlepb_render_content_block', array( $this, 'render_content_block' ), 50 );
		add_action( 'pootlepb_render_content_block', array( $this, 'close_block' ), 99 );
		add_action( 'wp_head', array( $this, 'print_inline_css' ), 12 );
		add_action( 'wp_footer', array( $this, 'print_inline_css' ) );
		add_action( 'pootlepb_content_block_tabs', array( $this, 'add_wc_tab' ) );

		add_action( 'edit_page_form', array( $this, 'ppb_tmce_dialog' ) );
		add_action( 'edit_form_advanced', array( $this, 'ppb_tmce_dialog' ) );

		add_action( 'pootlepb_content_block_editor_tab', array( $this, 'panels_editor' ) );
		add_action( 'pootlepb_content_block_woocommerce_tab', array( $this, 'wc_tab' ) );
		add_action( 'wp_ajax_pootlepb_editor_form', array( $this, 'ajax_content_panel' ) );
	}

	/**
	 * Renders shortcodes in content
	 * @param string $text
	 * @return string
	 * @filter pootlepb_content_block
	 * @since 0.1.0
	 */
	public function do_shortcode( $text ) {
		if( preg_match( '/<p>\n?\[([^\]]+)\]\n?<\/p>/i', $text ) ) {
			$text = str_replace( [ '<p>', '</p>' ], '', $text );
		}
		return do_shortcode( $text );
	}

	/**
	 * Enables oEmbed in content blocks
	 * @param string $text
	 * @return string
	 * @filter pootlepb_content_block
	 * @since 0.1.0
	 */
	public function auto_embed( $text ) {

		$text = str_replace(
			array( '<p>', '</p>', ), array(
			"<p>\n",
			"\n</p>",
		), $text
		);

		return $GLOBALS['wp_embed']->autoembed( $text );
	}

	/**
	 * Opens the content block container with styles and classes
	 * @param $block_info
	 * @param $gi
	 * @param $ci
	 * @param $pi
	 * @param $blocks_num
	 * @param $post_id
	 * @action pootlepb_render_content_block
	 * @since 0.1.0
	 */
	public function open_block( $block_info, $gi, $ci, $pi, $blocks_num, $post_id ) {

		$styleArray = array();
		if ( isset( $block_info['info']['style'] ) ) {
			$styleArray = json_decode( $block_info['info']['style'], true );
		}

		//Id for this content block
		$id = 'panel-' . $post_id . '-' . $gi . '-' . $ci . '-' . $pi;

		$attr = array( 'id' => $id );

		//Classes for this content block
		$attr['class'] = array( 'ppb-block' );
		if ( ! empty( $styleArray['class'] ) ) { $attr['class'][] = $styleArray['class']; }
		if ( empty( $styleArray['padding-mobile'] ) ) {
			$attr['class'][] = 'ppb-no-mobile-spacing';
		} else {
			$attr['class'][] = 'ppb-mobile-behaviour-' . $styleArray['padding-mobile'];
		}

		$styleWithSelector = ''; // Passed with reference
		if ( ! empty( $styleArray ) ) {
			$styleArray = wp_parse_args( $styleArray, [
				'inline-css' => '',
			] );
			$this->set_inline_embed_styles( $attr, $styleWithSelector, $styleArray, $id ); // Get Styles
		}

		if ( ! empty( $styleWithSelector ) ) { echo '<style>' . $styleWithSelector . '</style>'; }

		echo '<div ';
		echo pootlepb_stringify_attributes( $attr );
		echo '>';
	}

	/**
	 * Sets content block embed and inline css
	 * @param array $attr
	 * @param string $styleWithSelector
	 * @param array $styleArray
	 * @param string $id
	 */
	private function set_inline_embed_styles( &$attr, &$styleWithSelector, $styleArray, $id ) {

		$inlineStyle = '';

		$widgetStyleFields = pootlepb_block_styling_fields();
		foreach ( $widgetStyleFields as $key => $field ) {
			if ( empty( $field['css'] ) ) { continue; }
			if ( $field['type'] == 'border' ) {
				//Border field
				$this->content_block_border( $inlineStyle, $styleWithSelector, $id, $styleArray, $key, $field );
			} else {
				//Default for fields
				$this->default_block_field( $inlineStyle, $styleWithSelector, $styleArray, $id, $key, $field );
			}
		}

		$this->bg_color_transparency( $inlineStyle, $styleArray );

		$attr['style'] = $inlineStyle . $styleArray['inline-css'];

		/**
		 * Filters content block attributes
		 * @var array $attr Content block attributes
		 * @var array $style Content block style settings
		 * @var string $id Unique ID of content block
		 * @since 0.1.0
		 */
		$attr = apply_filters( 'pootlepb_content_block_attributes', $attr, $styleArray, $id );
	}

	/**
	 * Renders border for the content block
	 * @param string $inlineStyle inline style
	 * @param string $styleWithSelector CSS with selector
	 * @param string $id CSS id
	 * @param array $styleArray style data
	 * @param string $key field key
	 * @param array $field field data
	 * @since 0.1.0
	 */
	private function content_block_border( &$inlineStyle, &$styleWithSelector, $id, $styleArray, $key, $field ) {

		//Border
		if ( ! empty( $styleArray[ $key . '-width' ] ) ) {
			if ( empty( $field['selector'] ) ) {
				$inlineStyle .= $field['css'] . ': ' . $styleArray[ $key . '-width' ] . 'px solid ' . $styleArray[ $key . '-color' ] . ';';
			} else {
				$styleWithSelector .= '#' . $id . ' ' . $field['selector'] . ' { ' . $field['css'] . ': ' . $styleArray[ $key . '-width' ] . 'px solid ' . $styleArray[ $key . '-color' ] . ';}';
			}
		}
	}

	/**
	 * Fallback content block style renderer
	 * @param string $inlineStyle
	 * @param string $styleWithSelector
	 * @param array $styleArray
	 * @param string $id
	 * @param string $key
	 * @param array $field
	 * @since 0.1.0
	 */
	private function default_block_field( &$inlineStyle, &$styleWithSelector, $styleArray, $id, $key, $field ) {

		if ( ! empty( $styleArray[ $key ] ) ) {

			$unit = '';

			if ( ! empty( $field['unit'] ) ) { $unit = $field['unit']; }

			if ( ! isset( $field['selector'] ) ) {
				//No selector
				if ( strpos( $field['css'], '%s' ) ) {
					$inlineStyle .= sprintf( $field['css'], $styleArray[ $key ] );
				} else {
					$inlineStyle .= $field['css'] . ': ' . $styleArray[ $key ] . $unit . ';';
				}
			} else {
				//Has a selector
				$styleWithSelector .= '#' . $id . ' ' . $field['selector'] . ' { ' . $field['css'] . ': ' . $styleArray[ $key ] . $unit . '; }';
			}
		}
	}

	/**
	 * Sets transparency for content block bg color
	 * @param string $style Content CSS
	 * @param array $set Settings
	 * @since 0.2.3
	 */
	private function bg_color_transparency( &$style, $set ) {
		if ( ! empty( $set['background-transparency'] ) && ! empty( $set['background-color'] ) ) {
			$style .= 'background-color: rgba( ' . pootlepb_hex2rgb( $set['background-color'] ) . ', ' . ( 1 - $set['background-transparency'] ) . ' ); ';
		}
	}
	/**
	 * Render the Content Panel.
	 * @param string $widget_info The widget class name.
	 * @since 0.1.0
	 */
	public function render_content_block( $block_info ) {
		if ( ! empty( $block_info['text'] ) ) {
			echo apply_filters( 'pootlepb_content_block', $block_info['text'] );
		}
	}

	/**
	 * Closes the content block container
	 * @since 0.1.0
	 */
	public function close_block() {
		echo '</div>';
	}

	/**
	 * Print inline CSS
	 * @since 0.1.0
	 */
	public function print_inline_css() {
		global $pootlepb_inline_css;

		if ( ! empty( $pootlepb_inline_css ) ) {
			?>
			<!----------Pootle Page Builder Inline Styles---------->
			<style type="text/css" media="all"><?php echo $pootlepb_inline_css ?></style>
			<?php
		}

		$pootlepb_inline_css = '';
	}

	/**
	 * Output TMCE Editor
	 * @param $request
	 * @since 0.1.0
	 */
	public function panels_editor() {
		//Init text to populate in editor
		wp_editor(
			'',
			'ppbeditor',
			array(
				'textarea_name'  => 'widgets[{$id}][text]',
				'default_editor' => 'tmce',
				'editor_height' => 400,
				'tinymce'        => array(
					'force_p_newlines' => false,
					'height' => 400,
				)
			)
		);
	}

	/**
	 * Adds Woocommerce tab
	 * @param array $tabs The array of tabs
	 * @return array Tabs
	 * @since 0.1.0
	 */
	public function add_wc_tab( $tabs ) {

		if( class_exists( 'WooCommerce' ) ) {
			$tabs['woocommerce'] = array(
				'label'    => 'Products',
				'priority' => 2,
			);
		}
		return $tabs;
	}

	/**
	 * Outputs content editor panel
	 * @since 0.1.0
	 */
	public function ppb_tmce_dialog() {

		$screen = get_current_screen();
		if ( in_array( $screen->id, pootlepb_settings( 'post-types' ) ) ) {
			?>
			<div id="ppb-editor-container"
			     style="display:none;position:absolute;right:25px;left:auto;"
			     class="panels-admin-dialog ppb-dialog ppb-add-content-panel ppb-cool-panel-container ppb-helper-clearfix"
			     tabindex="-1" role="dialog" aria-describedby="ppb-id-7" aria-labelledby="ppb-id-8">
				<div
					class="ppb-dialog-titlebar ppb-widget-header ppb-corner-all ppb-helper-clearfix ui-draggable-handle">
					<span id="ppb-id-8" class="ppb-dialog-title"> Editor </span>
					<button
						class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only ppb-dialog-titlebar-close"
						title="Close" type="button"><span class=
						                                  "ui-button-icon-primary ui-icon ppb-icon-closethick"></span><span
							class="ui-button-text">Close</span></button>
				</div>
				<div
					class="panel-dialog dialog-form widget-dialog-pootle_pb_content_block ppb-dialog-content ppb-widget-content"
					id="ppb-id-7">
					<?php
					require POOTLEPB_DIR . 'tpl/content-block-panel.php';
					?>
				</div>
				<div class="ppb-dialog-buttonpane ppb-widget-content ppb-helper-clearfix">
					<div class="ppb-dialog-buttonset">
						<button type="button"
						        class="button pootle stop ppb-button ppb-widget ppb-state-default ppb-corner-all ppb-button-text-only"
						        role="button">
							<span class="ppb-button-text">Done</span>
						</button>
					</div>
				</div>
			</div>
		<?php
		}
	}

	/**
	 * Output woo commerce tab
	 * @since 0.1.0
	 */
	public function wc_tab() {
		?>
		Using WooCommerce? <a href="<?php echo esc_url( admin_url( 'admin.php?page=page_builder_addons' ) ); ?>">Check out our WooCommerce add-on for page builder</a>
	<?php
	}
}

/** @var Pootle_Page_Builder_Content_Block Instance */
$GLOBALS['Pootle_Page_Builder_Content_Block'] = new Pootle_Page_Builder_Content_Block();