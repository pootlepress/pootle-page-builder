<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 25/6/15
 * Time: 11:29 PM
 * @since 0.1.0
 */

/**
 * Class Pootle_Page_Builder_Content_Block
 * @since 0.1.0
 */
final class Pootle_Page_Builder_Content_Block extends Pootle_Page_Builder_Abstract {
	/**
	 * @var Pootle_Page_Builder_Content_Block
	 * @since 0.1.0
	 */
	protected static $instance;

	/**
	 * Magic __construct
	 * $since 1.0.0
	 * @since 0.1.0
	 */
	protected function __construct() {
		add_filter( 'pootlepb_content_block', array( $this, 'auto_embed' ), 8 );
		add_action( 'pootlepb_render_content_block', array( $this, 'open_block' ), 5, 6 );
		add_action( 'pootlepb_render_content_block', array( $this, 'render_content_block' ) );
		add_action( 'pootlepb_render_content_block', array( $this, 'close_block' ), 99 );
		add_action( 'wp_head', array( $this, 'print_inline_css' ), 12 );
		add_action( 'wp_footer', array( $this, 'print_inline_css' ) );
		add_action( 'pootlepb_content_block_editor_form', array( $this, 'panels_editor' ) );
		add_action( 'wp_ajax_pootlepb_editor_form', array( $this, 'ajax_content_panel' ) );
		add_action( 'pootlepb_add_content_woocommerce_tab', array( $this, 'wc_tab' ) );
	}

	/**
	 * Enables oEmbed in content blocks
	 *
	 * @param string $text
	 *
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
	 *
	 * @param $block_info
	 * @param $gi
	 * @param $ci
	 * @param $pi
	 * @param $blocks_num
	 * @param $post_id
	 *
	 * @action pootlepb_render_content_block
	 * @since 0.1.0
	 */
	public function open_block( $block_info, $gi, $ci, $pi, $blocks_num, $post_id ) {

		$styleArray = $widgetStyle = isset( $block_info['info']['style'] ) ? json_decode( $block_info['info']['style'], true ) : pootlepb_default_content_block_style();;

		//Classes for this content block
		$classes = array( 'panel' );

		if ( ! empty( $styleArray['class'] ) ) {
			$classes[] = $styleArray['class'];
		}

		//Id for this content block
		$id = 'panel-' . $post_id . '-' . $gi . '-' . $ci . '-' . $pi;

		$widgetStyleFields = pootlepb_block_styling_fields();

		$inlineStyle = '';

		$styleWithSelector = '';

		$this->set_inline_embed_styles( $inlineStyle, $styleWithSelector, $styleArray, $widgetStyleFields, $id );

		if ( $styleWithSelector != '' ) {
			echo "<style>\n";
			echo str_replace( 'display', 'display:none;display', $styleWithSelector );
			echo "</style>\n";
		}

		echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" id="' . $id . '" style="' . $inlineStyle . '" >';
	}

	/**
	 * Sets content block embed and inline css
	 * @param string $inlineStyle
	 * @param array $styleWithSelector
	 * @param array $styleArray
	 * @param array $widgetStyleFields
	 * @param string $id
	 */
	private function set_inline_embed_styles( &$inlineStyle, &$styleWithSelector, $styleArray, $widgetStyleFields, $id ) {

		foreach ( $widgetStyleFields as $key => $field ) {
			if ( $field['type'] == 'border' ) {
				//Border field
				$this->content_block_border( $inlineStyle, $styleArray, $key, $field );

			} elseif ( $key == 'inline-css' ) {

				if ( ! empty( $styleArray[ $key ] ) ) {
					$inlineStyle .= $styleArray[ $key ];
				}

			} else {
				//Default for fields
				$this->default_block_field( $inlineStyle, $styleWithSelector, $styleArray, $id, $key, $field );
			}
		}
	}

	/**
	 * Renders border for the content block
	 * @param string $inlineStyle
	 * @param array $styleArray
	 * @param string $key
	 * @param array $field
	 */
	private function content_block_border( &$inlineStyle, $styleArray, $key, $field ) {

		//Border width
		if ( ! empty( $styleArray[ $key . '-width' ] ) ) {
			$inlineStyle .= $field['css'] . ': ' . $styleArray[ $key . '-width' ] . 'px solid;';
		}

		//Border color
		if ( ! empty( $styleArray[ $key . '-color' ] ) ) {
			$inlineStyle .= $field['css'] . '-color: ' . $styleArray[ $key . '-color' ] . ';';
		}
	}

	/**
	 * Fallback content block style renderer
	 * @param string $inlineStyle
	 * @param array $styleWithSelector
	 * @param array $styleArray
	 * @param string $id
	 * @param string $key
	 * @param array $field
	 */
	private function default_block_field( &$inlineStyle, &$styleWithSelector, $styleArray, $id, $key, $field ) {

		if ( ! empty( $styleArray[ $key ] ) ) {

			$unit = '';
			//Assign Unit if not empty
			if ( ! empty( $field['unit'] ) ) {
				$unit = $field['unit'];
			}

			if ( ! isset( $field['selector'] ) ) {
				//No selector
				$inlineStyle .= $field['css'] . ': ' . $styleArray[ $key ] . $unit . ';';
			} else {
				//Has a selector
				$styleWithSelector .= '#' . $id . ' > ' . $field['selector'] . ' { ' . $field['css'] . ': ' . $styleArray[ $key ] . $unit . '; }';
			}
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
			<style type="text/css" media="all"><?php echo $pootlepb_inline_css ?></style><?php
		}

		$pootlepb_inline_css = '';
	}

	/**
	 * Output TMCE Editor
	 * @param $request
	 * @since 0.1.0
	 */
	public function panels_editor( $request ) {
		//Init text to populate in editor
		$text = '';
		if ( ! empty( $request['instance'] ) ) {
			$instance = json_decode( $request['instance'] );
			if ( ! empty( $instance->text ) ) {
				$text = $instance->text;
			}
		}

		wp_editor(
			$text,
			'ppbeditor',
			array(
				'textarea_name'  => 'widgets[{$id}][text]',
				'default_editor' => 'tmce',
				'tinymce'        => array(
					'force_p_newlines' => false,
				)
			)
		);
	}

	/**
	 * Display a widget form with the provided data
	 * @param array|null $request Request data ($_POST/$_GET)
	 * @since 0.1.0
	 */
	public function editor_panel( $request = null ) {
		require POOTLEPB_DIR . 'tpl/content-block-panel.php';
	}

	/**
	 * Handles ajax requests for the content panel
	 * @uses Pootle_Page_Builder_Content_Block::editor_panel()
	 * @since 0.1.0
	 */
	public function ajax_content_panel() {
		$request = array_map( 'stripslashes_deep', $_REQUEST );
		$this->editor_panel( $request );
		exit();
	}

	/**
	 * Output woo commerce tab
	 * @since 0.1.0
	 */
	public function wc_tab() {
		//Using WooCommerce? You can now build a stunning shop with Page Builder. Just get our WooCommerce extension and start building!
		?>
		Using WooCommerce? Will we soon be launching a WooCommerce Add-on for page builder!
	<?php
	}
}

//Instantiating Pootle_Page_Builder_Content_Block class
Pootle_Page_Builder_Content_Block::instance();