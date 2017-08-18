<?php
/**
 * Contains Pootle_Page_Builder_Custom_Styles class
 * @author pootlepress
 * @since 0.1.0
 */

/**
 * Class Pootle_Page_Builder_Custom_Styles
 * Renders customized style for grid
 * @since 0.1.0
 */
final class Pootle_Page_Builder_Custom_Styles {
	/**
	 * @var Pootle_Page_Builder_Custom_Styles Instance
	 * @since 0.1.0
	 */
	protected static $instance;

	/** @var string Current bg type video */
	protected $row_bg_type;

	/**
	 * Magic __construct
	 * @since 0.1.0
	 */
	public function __construct() {
		/* Add style attributes */
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_style_vars' ), 5, 2 );
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_border' ), 10, 2 );
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_full_width' ), 11, 2 );
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_bg_parallax' ), 10, 2 );
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_height' ), 10, 3 );
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_hide_row' ), 10, 2 );
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_inline_css' ), 10, 2 );
	}

	/**
	 * Set's row border
	 * @param array $attr
	 * @param array $style
	 * @return array
	 */
	public function row_border( $attr, $style ) {

		if ( ! empty( $attr['top_border_height'] ) ) {
			$attr['style'] .= 'border-top: ' . $style['top_border_height'] . 'px solid ' . $style['top_border'] . '; ';
		}
		if ( ! empty( $style['bottom_border_height'] ) ) {
			$attr['style'] .= 'border-bottom: ' . $style['bottom_border_height'] . 'px solid ' . $style['bottom_border'] . '; ';
		}

		return $attr;
	}

	/**
	 * Initiates vars and properties for row styling
	 * @param array $attr
	 * @param array $style
	 * @return array
	 */
	public function row_style_vars( $attr, $style ) {

		$attr['style'] = '';

		//Setting row bg type property
		$this->row_bg_type = '.bg_image';
		if ( isset( $style['background_toggle'] ) ) {
			$this->row_bg_type = $style['background_toggle'];
		}

		$method = 'row_bg_' . str_replace( '.bg_', '', $this->row_bg_type );
		if ( method_exists( $this, $method ) ) {
			$attr = $this->$method( $attr, $style );
		}

		return $attr;
	}

	/**
	 * Set's row background color
	 * @param array $attr
	 * @param array $style
	 * @return array
	 */
	public function row_bg_color( $attr, $style ) {

		if ( ! empty( $style['background'] ) ) {
			$attr['style'] .= 'background-color: ' . $style['background'] . ';';
		}

		return $attr;
	}

	/**
	 * Set's row background color
	 * @param array $attr
	 * @param array $style
	 * @return array
	 */
	public function row_bg_grad( $attr, $style ) {

		foreach( array(
			'grad_col1' => '#fff',
			'grad_col2' => '#ccc',
			'grad_type' => '',
			'grad_image' => '',
		) as $k => $v ) {
			if ( empty( $style[ $k ] ) ) $style[ $k ] = $v;
		}
		
		add_action( 'pootlepb_row_embed_style', array( $this, 'row_bg_grad_css' ), 11, 3 );

		$attr['style'] .= "background: url('$style[grad_image]') center/cover;";

		return $attr;
	}
	public function row_bg_grad_css( $css, $style, $rowID ) {

		global $pootlepb_gradient_css;

		$grad_css = sprintf(
			$pootlepb_gradient_css[ $style['grad_type'] ],
			"$style[grad_col1],$style[grad_col2]"
		);

		if ( ! empty( $style['grad_opacity'] ) ) {
			$grad_css .= 'opacity: ' . ( 1 - $style['grad_opacity'] ) . ';';
		}
		$css .= "$rowID .panel-row-style:before { $grad_css }";

		remove_action( 'pootlepb_row_embed_style', array( $this, 'row_bg_grad_css' ), 11, 3 );

		return $css;
	}

	/**
	 * Row bg video class and video mobile image
	 * @param array $attr
	 * @param array $style
	 * @return array
	 */
	public function row_bg_video( $attr, $style ) {

		$attr['class'][] = 'video-bg';

		if ( ! empty( $style['bg_mobile_image'] ) ) {
			$attr['style'] .= 'background: url( ' . esc_url( $style['bg_mobile_image'] ) . ' ) center/cover; ';
		}

		add_action( 'pootlepb_before_cells', array( $GLOBALS['Pootle_Page_Builder_Render_Layout'], 'row_bg_video' ) );

		return $attr;
	}

	/**
	 * Set's row background image
	 * @param array $attr
	 * @param array $style
	 * @return array
	 */
	public function row_bg_image( $attr, $style ) {

		if ( '.bg_image' != $this->row_bg_type ) {
			return $attr;
		}

		if ( ! empty( $style['background_image'] ) ) {
			$attr['style'] .= 'background-image: url( ' . esc_url( $style['background_image'] ) . ' ); ';
			$attr = $this->row_bg_img_size( $attr, $style );
			$attr = $this->row_bg_img_repeat( $attr, $style );
		}

		return $attr;
	}

	/**
	 * Set's row bg image repeat
	 * @param array $attr
	 * @param array $style
	 * @return array
	 */
	public function row_bg_img_repeat( $attr, $style ) {

		$repeat = 'no-repeat';

		if ( ! empty( $style['background_image_repeat'] ) ) {
			$repeat = 'repeat';
		}

		$attr['style'] .= "background-repeat: $repeat; ";

		return $attr;
	}

	/**
	 * Outputs row bg image size
	 * @param array $attr
	 * @param array $style
	 * @return array
	 */
	public function row_bg_img_size( $attr, $style ) {
		if ( ! empty( $style['background_image_size'] ) ) {
			$attr['style'] .= 'background-size: ' . $style['background_image_size'] . '; ';
		}

		return $attr;
	}

	/**
	 * Row full width class
	 * @param array $attr
	 * @param array $style
	 * @return array
	 * @since 0.1.0
	 */
	public function row_full_width( $attr, $style ) {

		if ( ! empty( $style['full_width'] ) ) {
			$attr['class'][] = 'ppb-stretch-full-width';
			$attr['class'][] = "ppb-stretch-full-width-$style[full_width]";
			$attr['class'][] = 'ppb-full-width-no-bg';
		}

		if ( ! empty( $style['accordion'] ) ) {
			$attr['style'] .= 'min-height:0;display:none;';
		}

		if ( ! empty( $style['match_col_hi'] ) ) {
			$attr['class'][] = 'ppb-match-col-hi';
		}

		return $attr;
	}

	/**
	 * Row bg parallax class
	 * @param array $attr
	 * @param array $style
	 * @return array
	 * @since 0.1.0
	 */
	public function row_bg_parallax( $attr, $style ) {

		if ( '.bg_image' != $this->row_bg_type ) {
			return $attr;
		}

		if ( ! empty( $style['background_parallax'] ) ) {
			$attr['class'][] = "ppb-row-effect-$style[background_parallax]";
			if ( 3 == $style['background_parallax'] ) {
				add_action( 'pootlepb_before_cells', array(
					$GLOBALS['Pootle_Page_Builder_Render_Layout'],
					'row_bg_ken_burns'
				) );
			}
		}

		return $attr;
	}

	/**
	 * Row bg video class and video mobile image
	 * @param array $attr
	 * @param array $style
	 * @param array $cells
	 * @return array
	 * @since 0.1.0
	 */
	public function row_height( $attr, $style, $cells = array() ) {

		if ( ! empty( $style['full_height'] ) ) {
			$attr['style'] .= 'min-height:100vh;';
		} else if ( ! empty( $style['row_height'] ) ) {
			$attr['style'] .= 'min-height:' . $style['row_height'] . 'px;';
		}

		return $attr;
	}

	/**
	 * Return true if row contains content blocks in any cell
	 * @param array $cells Cells of the row to search for content blocks in
	 * @return bool
	 */
	protected function row_has_content( $cells ) {

		//Loop through the cells
		foreach ( $cells as $cell ) {

			//If cell contains content blocks
			if ( ! empty( $cell ) ) {
				return true;
			}
		}

		//No content blocks found in the cells of the row
		return false;
	}

	/**
	 * Row bg video class and video mobile image
	 * @param array $attr
	 * @param array $style
	 * @return array
	 * @since 0.1.0
	 */
	public function row_hide_row( $attr, $style ) {

		if ( ! empty( $style['hide_row'] ) ) {
			$attr['style'] .= 'display:none;';
		}

		return $attr;
	}

	/**
	 * Row bg video class and video mobile image
	 * @param array $attr
	 * @param array $style
	 * @return array
	 */
	public function row_inline_css( $attr, $style ) {

		if ( ! empty( $style['style'] ) ) {
			$attr['style'] .= preg_replace( "/\r|\n/", ';', $style['style'] );;
		}

		return $attr;
	}
}

/** @var Pootle_Page_Builder_Custom_Styles Instance */
$GLOBALS['Pootle_Page_Builder_Custom_Styles'] = new Pootle_Page_Builder_Custom_Styles();