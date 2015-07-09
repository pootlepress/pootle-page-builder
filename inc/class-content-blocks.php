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
		add_action( 'pootlepb_render_content_block', array( $this, 'render_content_block' ), 50 );
		add_action( 'pootlepb_render_content_block', array( $this, 'close_block' ), 99 );
		add_action( 'wp_head', array( $this, 'print_inline_css' ), 12 );
		add_action( 'wp_footer', array( $this, 'print_inline_css' ) );
		add_action( 'pootlepb_content_block_tabs', array( $this, 'add_wc_tab' ) );

		add_action( 'pootlepb_content_block_editor_tab', array( $this, 'panels_editor' ) );
		add_action( 'pootlepb_content_block_woocommerce_tab', array( $this, 'wc_tab' ) );
		add_action( 'wp_ajax_pootlepb_editor_form', array( $this, 'ajax_content_panel' ) );
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

		if ( isset( $block_info['info']['style'] ) ) {
			$styleArray = json_decode( $block_info['info']['style'], true );
		}

		//Id for this content block
		$id = 'panel-' . $post_id . '-' . $gi . '-' . $ci . '-' . $pi;

		$attr = array( 'id' => $id );

		//Classes for this content block
		$attr['class'] = array( 'panel' );
		if ( ! empty( $styleArray['class'] ) ) { $attr['class'][] = $styleArray['class']; }

		$styleWithSelector = ''; // Passed with reference
		$this->set_inline_embed_styles( $attr, $styleWithSelector, $styleArray, $id ); // Get Styles

		$attr['class'] = implode( ' ', $attr['class'] );

		echo '<div';
		foreach ( $attr as $k => $v ) {
			echo " $k='$v'";
		}
		echo '>';
	}

	/**
	 * Sets content block embed and inline css
	 *
	 * @param string $attr
	 * @param array $styleWithSelector
	 * @param array $styleArray
	 * @param string $id
	 */
	private function set_inline_embed_styles( &$attr, &$styleWithSelector, $styleArray, $id ) {

		$inlineStyle = '';
		//Inline styles
		if ( ! empty( $styleArray['inline-css'] ) ) { $inlineStyle .= $styleArray['inline-css']; }

		$widgetStyleFields = pootlepb_block_styling_fields();
		foreach ( $widgetStyleFields as $key => $field ) {
			if ( $field['type'] == 'border' ) {
				//Border field
				$this->content_block_border( $inlineStyle, $styleArray, $key, $field );
			} else {
				//Default for fields
				$this->default_block_field( $inlineStyle, $styleWithSelector, $styleArray, $id, $key, $field );
			}
		}

		$attr['style'] = $inlineStyle;

		$attr = apply_filters( 'pootlepb_content_block_attributes', $attr, $styleArray, $id );
	}

	/**
	 * Renders border for the content block
	 *
	 * @param string $inlineStyle
	 * @param array $styleArray
	 * @param string $key
	 * @param array $field
	 */
	private function content_block_border( &$inlineStyle, $styleArray, $key, $field ) {

		//Set border color key if not set
		if ( empty( $styleArray[ $key . '-color' ] ) ) {
			$styleArray[ $key . '-color' ] = '';
		}

		//Border
		if ( ! empty( $styleArray[ $key . '-width' ] ) ) {
			$inlineStyle .= $field['css'] . ': ' . $styleArray[ $key . '-width' ] . 'px solid ' . $styleArray[ $key . '-color' ] . ';';
		}
	}

	/**
	 * Fallback content block style renderer
	 *
	 * @param string $inlineStyle
	 * @param array $styleWithSelector
	 * @param array $styleArray
	 * @param string $id
	 * @param string $key
	 * @param array $field
	 */
	private function default_block_field( &$inlineStyle, &$styleWithSelector, $styleArray, $id, $key, $field ) {

		if ( ! empty( $styleArray[ $key ] ) ) {

			if ( empty( $field['css'] ) ) {
				return;
			}

			$unit = $this->get_unit( $field );

			if ( ! isset( $field['selector'] ) ) {
				//No selector
				$inlineStyle .= $field['css'] . ': ' . $styleArray[ $key ] . $unit . ';';
			} else {
				//Has a selector
				$styleWithSelector .= '#' . $id . ' > ' . $field['selector'] . ' { ' . $field['css'] . ': ' . $styleArray[ $key ] . $unit . '; }';
			}
		}
	}

	private function get_unit( $field ){

		$unit = '';

		//Assign Unit if not empty
		if ( ! empty( $field['unit'] ) ) {
			$unit = $field['unit'];
		}

		return $unit;
	}

	/**
	 * Render the Content Panel.
	 *
	 * @param string $widget_info The widget class name.
	 *
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
	 *
	 * @param $request
	 *
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
	 *
	 * @param array|null $request Request data ($_POST/$_GET)
	 *
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
	 * Adds Woocommerce tab
	 * @param array $tabs The array of tabs
	 * @return array Tabs
	 * @since 0.1.0
	 */
	public function add_wc_tab( $tabs ) {
		$tabs['woocommerce'] = array(
			'label' => 'Woocommerce',
			'priority' => 2,
		);
		return $tabs;
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