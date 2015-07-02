<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 25/6/15
 * Time: 11:32 PM
 * @since 0.1.0
 */
final class Pootle_Page_Builder_Front_Css_Js extends Pootle_Page_Builder_Abstract {
	/**
	 * @var Pootle_Page_Builder_Front_Css_Js
	 * @access protected
	 * @since 0.1.0
	 */
	protected static $instance;

	/**
	 * Magic __construct
	 * @since 0.1.0
	 */
	protected function __construct() {
		$this->hooks();
	}

	/**
	 * Adds the actions and filter hooks for plugin functioning
	 * @since 0.1.0
	 */
	private function hooks() {
		add_filter( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 0 );
		add_filter( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 0 );
	}

	/**
	 * Generate the actual CSS.
	 *
	 * @param int|string $post_id
	 * @param array $panels_data
	 * @return string Css styles
	 * @since 0.1.0
	 */
	function panels_generate_css( $post_id, $panels_data ) {
		// Exit if we don't have panels data
		if ( empty( $panels_data ) || empty( $panels_data['grids'] ) ) {
			return;
		}

		$settings = pootlepb_settings();

		$panels_mobile_width  = $settings['mobile-width'];
		$panels_margin_bottom = $settings['margin-bottom'];

		$css                         = array();
		$css[1920]                   = array();
		$css[ $panels_mobile_width ] = array(); // This is a mobile resolution

		// Add the grid sizing
		$this->grid_styles( $css, $settings, $panels_margin_bottom, $panels_mobile_width, $panels_data, $post_id );

		if ( $settings['responsive'] ) {
			// Add CSS to prevent overflow on mobile resolution.
			$panel_grid_css      = 'margin-left: 0 !important; margin-right: 0 !important;';
			$panel_grid_cell_css = 'padding: 0 !important; width: 100% !important;';
			if ( empty( $css[ $panels_mobile_width ][ $panel_grid_css ] ) ) {
				$css[ $panels_mobile_width ][ $panel_grid_css ] = array();
			}
			if ( empty( $css[ $panels_mobile_width ][ $panel_grid_cell_css ] ) ) {
				$css[ $panels_mobile_width ][ $panel_grid_cell_css ] = array();
			}
			$css[ $panels_mobile_width ][ $panel_grid_css ][]      = '.panel-grid';
			$css[ $panels_mobile_width ][ $panel_grid_cell_css ][] = '.panel-grid-cell';
		} else {
			$panel_grid_cell_css = 'display: inline-block !important; vertical-align: top !important;';

			if ( empty( $css[ $panels_mobile_width ][ $panel_grid_cell_css ] ) ) {
				$css[ $panels_mobile_width ][ $panel_grid_cell_css ] = array();
			}

			$css[ $panels_mobile_width ][ $panel_grid_cell_css ][] = '.panel-grid-cell';
		}

		//Margin and padding
		$this->grid_elements_margin_padding( $css, $settings, $panels_margin_bottom );

		/**
		 * Filter the unprocessed CSS array
		 * @since 0.1.0
		 */
		$css = apply_filters( 'pootlepb_css', $css );

		// Build the CSS
		return $this->grid_build_css( $css );
	}

	/**
	 * Outputs style for rows and cells
	 * @param $css
	 * @param $settings
	 * @param $panels_margin_bottom
	 * @param $panels_mobile_width
	 * @param $panels_data
	 * @param $post_id
	 * @since 0.1.0
	 */
	public function grid_styles( &$css, $settings, $panels_margin_bottom, $panels_mobile_width, $panels_data, $post_id ) {
		$ci = 0;
		foreach ( $panels_data['grids'] as $gi => $grid ) {
			$cell_count = intval( $grid['cells'] );
			for ( $i = 0; $i < $cell_count; $i ++ ) {
				$cell = $panels_data['grid_cells'][ $ci ++ ];

				if ( $cell_count > 1 ) {
					$css_new = 'width:' . round( $cell['weight'] * 100, 3 ) . '%';
					if ( empty( $css[1920][ $css_new ] ) ) {
						$css[1920][ $css_new ] = array();
					}
					$css[1920][ $css_new ][] = '#pgc-' . $post_id . '-' . $gi . '-' . $i;
				}
			}

			// Add the bottom margin to any grids that aren't the last
			if ( $gi != count( $panels_data['grids'] ) - 1 ) {
				$css[1920][ 'margin-bottom: ' . $panels_margin_bottom . 'px' ][] = '#pg-' . $post_id . '-' . $gi;
			}

			if ( $settings['responsive'] ) {
				// Mobile Responsive
				$mobile_css = array( 'float:none', 'width:auto' );
				foreach ( $mobile_css as $c ) {
					if ( empty( $css[ $panels_mobile_width ][ $c ] ) ) {
						$css[ $panels_mobile_width ][ $c ] = array();
					}
					$css[ $panels_mobile_width ][ $c ][] = '#pg-' . $post_id . '-' . $gi . ' .panel-grid-cell';
				}

				for ( $i = 0; $i < $cell_count; $i ++ ) {
					if ( $i != $cell_count - 1 ) {
						$css_new = 'margin-bottom:' . $panels_margin_bottom . 'px';
						if ( empty( $css[ $panels_mobile_width ][ $css_new ] ) ) {
							$css[ $panels_mobile_width ][ $css_new ] = array();
						}
						$css[ $panels_mobile_width ][ $css_new ][] = '#pgc-' . $post_id . '-' . $gi . '-' . $i;
					}
				}
			}
		}

	}

	/**
	 * Margin padding for rows and columns
	 * @param $css
	 * @param $settings
	 * @param $panels_margin_bottom
	 * @since 0.1.0
	 */
	public function grid_elements_margin_padding( &$css, $settings, $panels_margin_bottom ) {

		// Add the bottom margin
		$bottom_margin      = 'margin-bottom: ' . $panels_margin_bottom . 'px';
		$bottom_margin_last = 'margin-bottom: 0 !important';
		if ( empty( $css[1920][ $bottom_margin ] ) ) {
			$css[1920][ $bottom_margin ] = array();
		}
		if ( empty( $css[1920][ $bottom_margin_last ] ) ) {
			$css[1920][ $bottom_margin_last ] = array();
		}
		$css[1920][ $bottom_margin ][]      = '.panel-grid-cell .panel';
		$css[1920][ $bottom_margin_last ][] = '.panel-grid-cell .panel:last-child';

		// This is for the side margins
		$magin_half    = $settings['margin-sides'] / 2;
		$side_paddings = "padding: 0 {$magin_half}px 0";

		if ( empty( $css[1920][ $side_paddings ] ) ) {
			$css[1920][ $side_paddings ] = array();
		}

		$css[1920][ $side_paddings ][] = '.panel-grid-cell';

		if ( ! defined( 'POOTLEPB_OLD_V' ) ) {

			$css[1920]['padding: 10px'][] = '.panel';
			$css[768]['padding: 5px'][]   = '.panel';

		}

	}

	/**
	 * Decodes array css to string
	 * @param array $css
	 * @return string
	 * @since 0.1.0
	 */
	public function grid_build_css( $css ) {
		$css_text = '';
		krsort( $css );
		foreach ( $css as $res => $def ) {
			if ( empty( $def ) ) {
				continue;
			}

			if ( $res < 1920 ) {
				$css_text .= '@media ( max-width:' . $res . 'px )';
				$css_text .= ' { ';
			}

			foreach ( $def as $property => $selector ) {
				$selector = array_unique( $selector );
				$css_text .= implode( ' , ', $selector ) . ' { ' . $property . ' } ';
			}

			if ( $res < 1920 ) {
				$css_text .= ' } ';
			}
		}

		return $css_text;
	}

	/**
	 * Enqueue the required styles
	 * @since 0.1.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'ppb-panels-front', POOTLEPB_URL . 'css/front.css', array(), POOTLEPB_VERSION );
	}

	/**
	 * Enqueue the required scripts
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'pootle-page-builder-front-js', POOTLEPB_URL . '/js/admin.js', array( 'jquery' ) );

	}
}

//Instantiating Pootle_Page_Builder_Front_Css_Js class
Pootle_Page_Builder_Front_Css_Js::instance();