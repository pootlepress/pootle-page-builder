<?php

/**
 * Adds notice to output in next admin_notices actions call
 *
 * @param string $id Unique id for pootle page builder message
 * @param string $message
 * @param string $type Standard WP admin notice types supported defaults 'updated'
 *
 * @since 0.1.0
 */
function pootlepb_add_admin_notice( $id, $message, $type = 'updated' ) {

	$notices = get_option( 'pootlepb_admin_notices', array() );

	$notices[$id] = array(
		'type'    => $type,
		'message' => $message,
	);

	update_option( 'pootlepb_admin_notices', $notices );
}

/**
 * Renders color picker control
 *
 * @param string $label
 * @param string $value
 * @param string $default_color
 * @param string $link
 * @since 0.1.0
 */
function pootlepb_color_control( $label, $value, $default_color, $link ) {

	$current_color = isset( $value ) ? $value : $default_color;

	?>
	<label><span><?php _e( $label, 'scratch' ); ?></span>
		<input class="color-picker-hex sc-font-color-text-box" type="text" maxlength="7"
		       placeholder="<?php esc_attr_e( 'Hex Value' ); ?>"
		       value="<?php echo $current_color; ?>" data-default-color="<?php echo $default_color ?>"
			<?php echo $link ?>
			/>
	</label>
<?php
}

/**
 * Test whether or not a typeface has been selected for a "typography" field.
 *
 * @param   string $face The noble warrior ( typeface ) to be tested.
 * @param   string $test_case The test case. Does the warrior pass the ultimate test and reep eternal glory?
 *
 * @return  bool              Whether or not eternal glory shall be achieved by the warrior.
 * @since 0.1.0
 */
function pootlepb_test_typeface_against_test_case( $face, $test_case ) {
	$response = false;

	$face = stripslashes( str_replace( '"', '', str_replace( '&quot;', '', $face ) ) );

	$parts = explode( ',', $face );

	if ( $test_case == $parts[0] ) {
		$response = true;
	}

	return $response;
}

/**
 * Outputs html for options in font face select field
 *
 * @param $font_faces
 * @param $test_cases
 * @param $value
 *
 * @return string
 * @since 0.1.0
 */
function pootlepb_output_font_select_options( $value ) {
	global $pootlepb_font;

	$font_faces = $pootlepb_font;
	$test_cases = array();

	if ( function_exists( 'wf_get_system_fonts_test_cases' ) ) {
		$test_cases = wf_get_system_fonts_test_cases();
	}

	$html = '';
	foreach ( $font_faces as $k => $v ) {

		$selected = '';

		// If one of the fonts requires a test case, use that value. Otherwise, use the key as the test case.
		if ( in_array( $k, array_keys( $test_cases ) ) ) {
			$value_to_test = $test_cases[ $k ];
		} else {
			$value_to_test = $k;
		}
		if ( pootlepb_test_typeface_against_test_case( $value, $value_to_test ) ) {
			$selected = ' selected="selected"';
		}
		$html .= '<option value="' . esc_attr( $k ) . '" ' . $selected . '>' . esc_html( $v ) . '</option>' . "\n";
	}

	return $html;

}

/**
 * Converts hex color string to rgb
 *
 * @param $hex
 *
 * @return string red, green, blue
 * @since 0.1.0
 */
function pootlepb_hex2rgb( $hex ) {
	$hex = str_replace( "#", "", $hex );

	if ( strlen( $hex ) == 3 ) {
		$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
		$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
		$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
	} else {
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
	}

	return " $r, $g, $b";
}

/**
 * Check if we're currently viewing a panel.
 * @param bool $can_edit Also check if the user can edit this page
 * @return bool
 * @since 0.1.0
 */
function pootlepb_is_panel( $can_edit = false ) {
	// Check if this is a panel
	$is_panel = ( is_singular() && get_post_meta( get_the_ID(), 'panels_data', false ) != '' );

	return $is_panel && ( ! $can_edit || ( is_singular() && current_user_can( 'edit_post', get_the_ID() ) ) );
}

function pootlepb_row_style_fields( $fields ) {

	global $pootlepb_row_styling_fields;
	return array_merge( $fields, $pootlepb_row_styling_fields );
}

/**
 * A callback that replaces temporary break tag with actual line breaks.
 *
 * @param $val
 *
 * @return array|mixed
 * @since 0.1.0
 */
function pootlepb_wp_import_post_meta_map( $val ) {
	if ( is_string( $val ) ) {
		return str_replace( '<<<br>>>', "\n", $val );
	} else {
		return array_map( 'pootlepb_wp_import_post_meta_map', $val );
	}
}

/**
 * Get the settings
 *
 * @param string $key Only get a specific key.
 *
 * @return mixed
 * @since 0.1.0
 */
function pootlepb_settings( $key = '' ) {

	if ( has_action( 'after_setup_theme' ) ) {
		// Only use static settings if we've initialized the theme
		static $settings;
	} else {
		$settings = false;
	}

	if ( empty( $settings ) ) {
		$display_settings = get_option( 'siteorigin_panels_display', array() );

		$settings = get_theme_support( 'ppb-panels' );
		if ( ! empty( $settings ) ) {
			$settings = $settings[0];
		} else {
			$settings = array();
		}


		$settings = wp_parse_args( $settings, array(
			'post-types'        => apply_filters( 'pootlepb_builder_post_types', array( 'page' ) ),
			// Post types that can be edited using panels.
			'responsive'        => ! isset( $display_settings['responsive'] ) ? true : $display_settings['responsive'] == '1',
			// Should we use a responsive layout
			'mobile-width'      => ! isset( $display_settings['mobile-width'] ) ? 780 : $display_settings['mobile-width'],
			// What is considered a mobile width?
			'margin-bottom'     => ! isset( $display_settings['margin-bottom'] ) ? 0 : $display_settings['margin-bottom'],
			// Bottom margin of a cell
			'margin-sides'      => ! isset( $display_settings['margin-sides'] ) ? 10 : $display_settings['margin-sides'],
			// Spacing between 2 cells
			'inline-css'        => true,
			// How to display CSS
		) );
	}

	if ( ! empty( $key ) ) {
		return isset( $settings[ $key ] ) ? $settings[ $key ] : null;
	}

	return $settings;
}

/**
 * Convert form post data into more efficient panels data.
 * @param $form_post
 * @return array
 * @since 0.1.0
 */
function pootlepb_get_panels_data_from_post( $form_post ) {
	$panels_data            = array();
	$panels_data['widgets'] = array_values( stripslashes_deep( isset( $form_post['widgets'] ) ? $form_post['widgets'] : array() ) );

	foreach ( $panels_data['widgets'] as $i => $widget ) {

		if ( empty( $widget['info'] ) ) {
			continue;
		}

		$info = $widget['info'];

		$widget = json_decode( $widget['data'], true );

		if ( class_exists( $info['class'] ) ) {
			$the_widget = new $info['class'];
			if ( method_exists( $the_widget, 'update' ) && ! empty( $info['raw'] ) ) {
				$widget = $the_widget->update( $widget, $widget );
			}
		}

		unset( $info['raw'] );
		$widget['info'] = $info;

		// if widget style is not present in $_POST, set a default
		if ( ! isset( $info['style'] ) ) {
			$widgetStyle = pootlepb_default_content_block_style();

			$info['style'] = $widgetStyle;
		}

		$panels_data['widgets'][ $i ] = $widget;

	}

	$panels_data['grids']      = array_values( stripslashes_deep( isset( $form_post['grids'] ) ? $form_post['grids'] : array() ) );
	$panels_data['grid_cells'] = array_values( stripslashes_deep( isset( $form_post['grid_cells'] ) ? $form_post['grid_cells'] : array() ) );

	return apply_filters( 'pootlepb_panels_data_from_post', $panels_data );
}