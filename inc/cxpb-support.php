<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 25/6/15
 * Time: 11:53 PM
 * @since 0.1.0
 */

/**
 * Output old Page Builder for Canvas styles
 * @TODO Move to update add-on
 * @since 0.1.0
 */
function pootlepb_cxpb_option_css() {

	$output = '';

	// Widget Styling
	$widget_font_title    = get_option( 'page_builder_widget_font_title', array(
		'size'  => '14',
		'unit'  => 'px',
		'face'  => 'Helvetica, Arial, sans-serif',
		'style' => 'bold',
		'color' => '#555555'
	) );
	$widget_font_text     = get_option( 'page_builder_widget_font_text', array(
		'size'  => '13',
		'unit'  => 'px',
		'face'  => 'Helvetica, Arial, sans-serif',
		'style' => 'thin',
		'color' => '#555555'
	) );
	$widget_padding_tb    = get_option( 'page_builder_widget_padding_tb', '0' );
	$widget_padding_lr    = get_option( 'page_builder_widget_padding_lr', '0' );
	$widget_bg            = get_option( 'page_builder_widget_bg', 'transparent' );
	$widget_border        = get_option( 'page_builder_widget_border', array(
		'width' => '0',
		'style' => 'solid',
		'color' => '#dbdbdb'
	) );
	$widget_title_border  = get_option( 'page_builder_widget_title_border', array(
		'width' => '1',
		'style' => 'solid',
		'color' => '#e6e6e6'
	) );
	$widget_border_radius = get_option( 'page_builder_widget_border_radius', '0px' );

	// in Visual Editor, dont set underline for h3
	$output .= '.widget_pootle-text-widget > .textwidget h3 { border-bottom: none !important; }';

	$widget_title_css = '';
	if ( $widget_font_title ) {
		$widget_title_css .= 'font:' . $widget_font_title["style"] . ' ' . $widget_font_title["size"] . $widget_font_title["unit"] . '/1.2em ' . stripslashes( $widget_font_title["face"] ) . ';color:' . $widget_font_title["color"] . ';';
	}
	if ( $widget_title_border ) {
		$widget_title_css .= 'border-bottom:' . $widget_title_border["width"] . 'px ' . $widget_title_border["style"] . ' ' . $widget_title_border["color"] . ' !important;';
	}
	if ( isset( $widget_title_border["width"] ) AND $widget_title_border["width"] == 0 ) {
		$widget_title_css .= 'margin-bottom:0 !important;';
	}

	if ( $widget_title_css != '' ) {
		$output .= '.panel-grid-cell .widget h3.widget-title {' . $widget_title_css . '}' . "\n";
	}


	if ( $widget_title_border ) {
		$output .= '.panel-grid-cell .widget_recent_comments li{ border-color: ' . $widget_title_border["color"] . ';}' . "\n";
	}

	if ( $widget_font_text ) {
		$output .= '.panel-grid-cell .widget p, .panel-grid-cell .widget .textwidget { ' . pootlepb_generate_font_css( $widget_font_text, 1.5 ) . ' }' . "\n";
	}

	$widget_css = '';
	if ( $widget_font_text ) {
		$widget_css .= 'font:' . $widget_font_text["style"] . ' ' . $widget_font_text["size"] . $widget_font_text["unit"] . '/1.5em ' . stripslashes( $widget_font_text["face"] ) . ';color:' . $widget_font_text["color"] . ';';
	}

	if ( ! $widget_padding_lr ) {
		$widget_css .= 'padding-left: 0; padding-right: 0;';
	} else {
		$widget_css .= 'padding-left: ' . $widget_padding_lr . 'px ; padding-right: ' . $widget_padding_lr . 'px;';
	}
	if ( ! $widget_padding_tb ) {
		$widget_css .= 'padding-top: 0; padding-bottom: 0;';
	} else {
		$widget_css .= 'padding-top: ' . $widget_padding_tb . 'px ; padding-bottom: ' . $widget_padding_tb . 'px;';
	}

	if ( $widget_bg ) {
		$widget_css .= 'background-color:' . $widget_bg . ';';
	} else {
		$widget_css .= 'background-color: transparent;';
	}


	if ( $widget_border["width"] > 0 ) {
		$widget_css .= 'border:' . $widget_border["width"] . 'px ' . $widget_border["style"] . ' ' . $widget_border["color"] . ';';
	}
	if ( $widget_border_radius ) {
		$widget_css .= 'border-radius:' . $widget_border_radius . ';-moz-border-radius:' . $widget_border_radius . ';-webkit-border-radius:' . $widget_border_radius . ';';
	}

	if ( $widget_css != '' ) {
		$output .= '.panel-grid-cell .widget {' . $widget_css . '}' . "\n";
	}

	if ( $widget_border["width"] > 0 ) {
		$output .= '.panel-grid-cell #tabs {border:' . $widget_border["width"] . 'px ' . $widget_border["style"] . ' ' . $widget_border["color"] . ';}' . "\n";
	}

	// Tabs Widget
	$widget_tabs_bg        = get_option( 'page_builder_widget_tabs_bg', 'transparent' );
	$widget_tabs_bg_inside = get_option( 'page_builder_widget_tabs_bg_inside', '' );
	$widget_tabs_font      = get_option( 'page_builder_widget_tabs_font', array(
		'size'  => '12',
		'unit'  => 'px',
		'face'  => 'Helvetica, Arial, sans-serif',
		'style' => 'bold',
		'color' => '#555555'
	) );
	$widget_tabs_font_meta = get_option( 'page_builder_widget_tabs_font_meta', array(
		'size'  => '11',
		'unit'  => 'px',
		'face'  => 'Helvetica, Arial, sans-serif',
		'style' => 'thin',
		'color' => ''
	) );

	if ( $widget_tabs_bg ) {
		$output .= '.panel-grid-cell #tabs, .panel-grid-cell .widget_woodojo_tabs .tabbable {background-color:' . $widget_tabs_bg . ';}' . "\n";
	} else {
		$output .= '.panel-grid-cell #tabs, .panel-grid-cell .widget_woodojo_tabs .tabbable {background-color: transparent;}' . "\n";
	}

	if ( $widget_tabs_bg_inside ) {
		$output .= '.panel-grid-cell #tabs .inside, .panel-grid-cell #tabs ul.wooTabs li a.selected, .panel-grid-cell #tabs ul.wooTabs li a:hover {background-color:' . $widget_tabs_bg_inside . ';}' . "\n";
	} else {
		//$output .= '.panel-grid-cell #tabs .inside, .panel-grid-cell #tabs ul.wooTabs li a.selected, .panel-grid-cell #tabs ul.wooTabs li a:hover {background-color: transparent; }' . "\n";
	}

	if ( $widget_tabs_font ) {
		$output .= '.panel-grid-cell #tabs .inside li a, .panel-grid-cell .widget_woodojo_tabs .tabbable .tab-pane li a { ' . pootlepb_generate_font_css( $widget_tabs_font, 1.5 ) . ' }' . "\n";
	}
	if ( $widget_tabs_font_meta ) {
		$output .= '.panel-grid-cell #tabs .inside li span.meta, .panel-grid-cell .widget_woodojo_tabs .tabbable .tab-pane li span.meta { ' . pootlepb_generate_font_css( $widget_tabs_font_meta, 1.5 ) . ' }' . "\n";
	}
	$output .= '.panel-grid-cell #tabs ul.wooTabs li a, .panel-grid-cell .widget_woodojo_tabs .tabbable .nav-tabs li a { ' . pootlepb_generate_font_css( $widget_tabs_font_meta, 2 ) . ' }' . "\n";

	echo "<style>\n" . $output . "\n" . "</style>\n";
}
add_action( 'wp_head', 'pootlepb_cxpb_option_css' );

/**
 * Generates font CSS from options
 *
 * @param $option
 * @param string $em
 * @TODO Move to update add-on
 * @return string
 * @since 0.1.0
 */
function pootlepb_generate_font_css( $option, $em = '1' ) {

	// Test if font-face is a Google font
	global $google_fonts;
	if ( is_array( $google_fonts ) ) {
		foreach ( $google_fonts as $google_font ) {

			// Add single quotation marks to font name and default arial sans-serif ending
			if ( $option['face'] == $google_font['name'] ) {
				$option['face'] = "'" . $option['face'] . "', arial, sans-serif";
			}

		} // END foreach
	}

	if ( ! @$option['style'] && ! @$option['size'] && ! @$option['unit'] && ! @$option['color'] ) {
		return 'font-family: ' . stripslashes( $option["face"] ) . ' !important;';
	} else {
		return 'font:' . $option['style'] . ' ' . $option['size'] . $option['unit'] . '/' . $em . 'em ' . stripslashes( $option['face'] ) . ' !important; color:' . $option['color'] . ' !important;';
	}
} // End pootlepb_generate_font_css( )

/**
 * Checks if older version of Page Builder was being used on site
 * Then runs compatibility functions accordingly
 * @TODO Move to update add-on
 * @since 0.1.0
 */
function pootlepb_version_check() {
	//Get initial version
	$initial_version = get_option( 'pootlepb_initial_version', POOTLEPB_VERSION );

	if ( POOTLEPB_VERSION != get_option( 'pootlepb_builder_version' ) ) {
		//If initial version < Current version
		if ( - 1 == version_compare( $initial_version, POOTLEPB_VERSION ) ) {
			//Sort compatibility issues
			require_once 'inc/class-pootle-page-compatibility.php';
			new Pootle_Page_Compatibility();
		}
		//Update current version
		update_option( 'pootlepb_builder_version', POOTLEPB_VERSION );
	}
}
add_action( 'admin_init', 'pootlepb_version_check' );
