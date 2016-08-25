<?php
/**
 * Contains Pootle_Page_Builder_Front_Css_Js class
 * @author pootlepress
 * @since 0.1.0
 */

/**
 * Class Pootle_Page_Builder_Front_Css_Js
 * Renders front end css and js
 * @since 0.1.0
 */
final class Pootle_Page_Builder_Front_Css_Js {
	/**
	 * @var Pootle_Page_Builder_Front_Css_Js Instance
	 * @access protected
	 * @since 0.1.0
	 */
	protected static $instance;

	/** @var array $styles The styles array */
	protected $styles = array();

	/**
	 * Magic __construct
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Adds the actions and filter hooks for plugin functioning
	 * @since 0.1.0
	 */
	private function hooks() {
		add_filter( 'pootlepb_rwd_mobile_width', array( $this, 'no_rwd_for_app' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 0 );
		add_action( 'wp_footer', array( $this, 'rag_adjust_script_load' ) );
	}

	/**
	 * Adds a style entry to Pootle_Page_Builder_Front_Css_Js::$style
	 *
	 * @param string $style Style to apply to element
	 * @param string $lmn The element selector
	 * @param int $res Resolution
	 */
	private function css( $style, $lmn, $res = 1920 ) {

		if ( empty( $this->styles[ $res ] ) ) {
			$this->styles[ $res ] = array();
		}

		if ( empty( $this->styles[ $res ][ $style ] ) ) {
			$this->styles[ $res ][ $style ] = array();
		}

		$this->styles[ $res ][ $style ][] = $lmn;

	}

	/**
	 * Generate the actual CSS.
	 *
	 * @param int|string $post_id
	 * @param array $panels_data
	 *
	 * @return string|null CSS to output
	 * @since 0.1.0
	 */
	public function panels_generate_css( $post_id, $panels_data ) {
		// Exit if we don't have panels data
		if ( empty( $panels_data['grids'] ) ) { return null; }

		$settings = pootlepb_settings(); // Pootle page builder settings

		// Add the grid sizing
		$this->grid_styles( $settings, $panels_data, $post_id );
		//Margin and padding
		$this->grid_elements_margin_padding( $settings );

		/**
		 * Filter the unprocessed CSS array
		 * @since 0.1.0
		 */
		$this->styles = apply_filters( 'pootlepb_css', $this->styles );

		// Build the CSS
		return $this->grid_build_css();
	}

	/**
	 * Outputs style for rows and cells
	 *
	 * @param $settings
	 * @param $panels_data
	 * @param $post_id
	 *
	 * @since 0.1.0
	 */
	public function grid_styles( $settings, $panels_data, $post_id ) {
		$ci = 0;
		foreach ( $panels_data['grids'] as $gi => $grid ) {
			$cell_count = intval( $grid['cells'] );

			$this->col_widths( $ci, $gi, $post_id, $cell_count, $panels_data );

			$this->row_bottom_margin( $settings, $gi, $post_id, $panels_data );

			$this->mobile_styles( $settings, $gi, $post_id );
		}

		$panel_grid_cell_css = 'display: inline-block !important; vertical-align: top !important;';

		$this->css( $panel_grid_cell_css, '.panel-grid-cell' );

		$this->css( 'font-size: 0;', '.panel-grid-cell-container' );
		$this->css( 'font-size: initial;', '.panel-grid-cell-container > *' );
		$this->css( 'position: relative;', '#pootle-page-builder, .panel-row-style, .panel-grid-cell-container' );
		$this->css( 'z-index: 1;', '.panel-grid-cell-container' );
		$this->css( 'padding-bottom: 1px;', '.panel-grid-cell-container' );
		$this->css( 'position: absolute;width: 100%;height: 100%;content: "";top: 0;left: 0;z-index: 0;', '.panel-row-style:before' );
	}

	/**
	 * Outputs column width css
	 *
	 * @param int $ci Cell Index
	 * @param int $gi Grid Index
	 * @param int $post_id
	 * @param int $cell_count
	 * @param array $panels_data
	 *
	 * @since 0.1.0
	 */
	private function col_widths( &$ci, $gi, $post_id, $cell_count, $panels_data ) {

		$col_gutts = 0;
		if ( ! empty( $panels_data['grids'][ $gi ]['style']['col_gutter'] ) ) {
			$col_gutts = $panels_data['grids'][ $gi ]['style']['col_gutter'];
		}

		for ( $i = 0; $i < $cell_count; $i ++ ) {
			$cell = $panels_data['grid_cells'][ $ci ];
			if ( empty( $cell['weight'] ) ) {
				$cell['weight'] = 100 / $cell_count;
			}

			if ( $cell_count > 1 ) {
				$css_new = 'width:' . round( $cell['weight'] * ( 100 - ( ( $cell_count - 1 ) * $col_gutts ) ), 3 ) . '%';
				$this->css( $css_new, '#pgc-' . $post_id . '-' . $gi . '-' . $i );
			}

			$ci ++;
		}
	}

	/**
	 * Outputs margin bottom style for rows
	 * @param array $settings PPB settings
	 * @param int $gi Grid Index
	 * @param int $post_id
	 * @param array $panels_data
	 * @since 0.1.0
	 */
	private function row_bottom_margin( $settings, $gi, $post_id, $panels_data ) {

		$panels_margin_bottom = $settings['margin-bottom'];

		// Add the bottom margin to any grids that aren't the last
		if ( $gi != count( $panels_data['grids'] ) - 1 ) {
			$this->css( 'margin-bottom: ' . $panels_margin_bottom . 'px', '#pg-' . $post_id . '-' . $gi );
		}
	}

	/**
	 * Outputs styles for res < 768px
	 * @param array $settings Settings
	 * @param string $gi Grid Index
	 * @param string $post_id Post ID
	 * @since 0.1.0
	 */
	private function mobile_styles( $settings, $gi, $post_id ) {

		$panels_margin_bottom = $settings['margin-bottom'];
		$panels_mobile_width  = apply_filters( 'pootlepb_rwd_mobile_width', $settings['mobile-width'] );

		if ( $settings['responsive'] ) {
			// Mobile Responsive

			$this->css( 'float:none', '#pg-' . $post_id . '-' . $gi . ' .panel-grid-cell', $panels_mobile_width );
			$this->css( 'width:auto', '#pg-' . $post_id . '-' . $gi . ' .panel-grid-cell', $panels_mobile_width );

			$this->css( 'margin-bottom: 1' . $panels_margin_bottom . 'px', '.panel-grid-cell:not(:last-child)', $panels_mobile_width );

			// Add CSS to prevent overflow on mobile resolution.
			$panel_grid_css      = 'margin-left: 0 !important; margin-right: 0 !important;';
			$panel_grid_cell_css = 'padding: 0 !important; width: 100% !important;';

			$this->css( $panel_grid_css, '.panel-grid', $panels_mobile_width );
			$this->css( $panel_grid_cell_css, '.panel-grid-cell', $panels_mobile_width );
		}

	}

	/**
	 * Margin padding for rows and columns
	 *
	 * @param array $settings
	 * @param array $panels_margin_bottom
	 *
	 * @since 0.1.0
	 */
	public function grid_elements_margin_padding( $settings ) {

		// Add the bottom margin
		$bottom_margin      = 'margin-bottom: ' . $settings['margin-bottom'] . 'px';
		$bottom_margin_last = 'margin-bottom: 0 !important';

		$this->css( $bottom_margin, '.panel-grid-cell .panel' );
		$this->css( $bottom_margin_last, '.panel-grid-cell .panel:last-child' );

		if ( ! defined( 'POOTLEPB_OLD_V' ) ) {

			$this->css( 'padding: 10px', '.panel' );
			$this->css( 'padding: 5px', '.panel', 768 );
		}
	}

	/**
	 * Decodes array css to string
	 * @return string
	 * @since 0.1.0
	 */
	public function grid_build_css() {
		$css_text = '';
		krsort( $this->styles );
		foreach ( $this->styles as $res => $def ) {

			if ( $res < 1920 ) {
				$css_text .= '@media ( max-width:' . $res . 'px ) { ';
			}

			//Add styles from def to css string
			$this->output_styles( $def, $css_text );

			if ( $res < 1920 ) {
				$css_text .= ' } ';
			}
		}

		return $css_text;
	}

	/**
	 * Adds css styles from $styles array to string $css_text
	 * @param array $styles
	 * @param string $css_text
	 */
	protected function output_styles( $styles, &$css_text ) {

		foreach ( $styles as $property => $selector ) {
			//Remove duplicates
			$selector = array_unique( $selector );
			$css_text .= implode( ' , ', $selector ) . ' { ' . $property . ' } ';
		}
	}

	/**
	 * Enqueue the required styles
	 * @since 0.1.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'ppb-panels-front', POOTLEPB_URL . 'css/front.css', array(), POOTLEPB_VERSION );
	}

	/**
	 * Reduces rwd resolution to 610 or less for app
	 * @param int $res
	 * @return int
	 */
	public function no_rwd_for_app( $res ) {
		if ( isset( $_REQUEST['ppb-ipad'] ) || filter_input( INPUT_POST, 'action' ) == 'pootlepb_live_editor' ) {
			return min( $res, 700 );
		}
		return $res;
	}

	/**
	 * Enqueue the required scripts
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {
		$elements = apply_filters( 'pootlepb_rag_adjust_elements', array( 'p', ) );
		if ( $elements ) {
			wp_enqueue_script( 'pootle-page-builder-rag-adjust-js', POOTLEPB_URL . '/js/ragadjust.min.js', array( 'jquery' ) );
		}
		wp_enqueue_script( 'pootle-page-builder-front-js', POOTLEPB_URL . '/js/front-end.js', array( 'jquery' ) );
	}

	function rag_adjust_script_load() {
		$elements = apply_filters( 'pootlepb_rag_adjust_elements', array( 'p', ) );
		if ( $elements ) {
			$method = apply_filters( 'pootlepb_rag_adjust_method', 'all' );
			?>
			<script type="text/javascript">
				ragadjust( '<?php echo implode( ', ', $elements ); ?>', '<?php echo $method; ?>', true );
			</script>
			<?php
		}
	}
}

/** @var Pootle_Page_Builder_Front_Css_Js Instance */
$GLOBALS['Pootle_Page_Builder_Front_Css_Js'] = new Pootle_Page_Builder_Front_Css_Js();