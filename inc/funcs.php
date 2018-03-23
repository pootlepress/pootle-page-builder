<?php
/**
 * Functions used accross pootle page builder and it's add-ons
 * @author pootlepress
 * @since 0.1.0
 */

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

	$notices[ $id ] = array(
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
 *
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
 * Converts attributes array into html attributes string
 * @param array $attributes Associative ( multidimensional ) array attributes
 * @return string HTML attributes
 */
function pootlepb_stringify_attributes( $attributes ) {
	$attr = '';

	foreach ( $attributes as $name => $value ) {
		if ( is_array( $value ) ) {
			$value = implode( " ", array_unique( $value ) );
		}

		$attr .= esc_attr( $name ) . '="' . esc_attr( $value ) . '" ';
	}

	return $attr;
}

/**
 * Check if we're currently viewing a panel.
 *
 * @param bool $can_edit Also check if the user can edit this page
 *
 * @return bool
 * @since 0.1.0
 */
function pootlepb_is_panel( $can_edit = false, $post = false ) {
	// Check if this is a panel
	$is_panel = ( is_singular() && pootlepb_uses_pb( $post ) );

	return $is_panel && ( ! $can_edit || current_user_can( 'edit_post', get_the_ID() ) );
}

/**
 * Check if we're currently viewing a panel.
 *
 * @param WP_Post|int|bool $post_id Also check if the user can edit this page
 *
 * @return bool
 * @since 0.1.0
 */
function pootlepb_uses_pb( $post_id = false ) {
	if ( ! $post_id ) {
		global $post;
		$post_id = $post->ID;
	} else if ( $post_id instanceof WP_Post ) {
		$post_id = $post_id->ID;
	}
	// Check if this is a panel
	$ppb_data = get_post_meta( $post_id, 'panels_data', true );
	return ! empty( $ppb_data['grids'] );
}

/**
 * Returns content block styling fields
 * @return array Style fields
 * @since 0.1.0
 */
function pootlepb_block_styling_fields() {
	global $pootlepb_content_block_styling_fields;

	$fields = apply_filters( 'pootlepb_content_block_fields', $pootlepb_content_block_styling_fields );

	return $fields;
}

/**
 * Get all the row styles.
 *
 * @return array An array defining the row fields.
 * @since 0.1.0
 */
function pootlepb_row_settings_fields() {

	global $pootlepb_row_styling_fields;

	$fields = apply_filters( 'pootlepb_row_settings_fields', $pootlepb_row_styling_fields );

	return $fields;
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

	$set = get_option( 'pootlepb_display', array() );

	$settings = get_theme_support( 'ppb-panels' );
	if ( ! empty( $settings ) ) {
		$settings = $settings[0];
	} else {
		$settings = array();
	}

	$settings = wp_parse_args( $settings, array(
		'post-types'       => pootlepb_posts(), // Supported post types
		'responsive'       => true, // Always responsive
		'mobile-width'     => ! isset( $set['mobile-width'] ) ? 780 : $set['mobile-width'],      // Width for RWD
		'margin-bottom'    => ! isset( $set['margin-bottom'] ) ? 0 : $set['margin-bottom'],     // for cell
		'margin-sides'     => ! isset( $set['margin-sides'] ) ? 10 : $set['margin-sides'],      // for cells
		'inline-css'       => true,        // CSS in HTML? or separate file
		'modules-position' => ! isset( $set['modules-position'] ) ? 'left' : $set['modules-position'],
	) );

	if ( ! empty( $key ) ) {
		return isset( $settings[ $key ] ) ? $settings[ $key ] : null;
	}

	return $settings;
}

/**
 * Get post types supported by ppb
 * @return array Supported post types
 */
function pootlepb_posts() {
	return apply_filters( 'pootlepb_builder_post_types', array( 'page', 'post', ) );
}

/**
 * Compares priority
 * @param array $a
 * @param array $b
 * @return bool
 */
function pootlepb_priority_cmp( $a, $b ) {

	return $a['priority'] > $b['priority'];
}

/**
 * Compares priority
 *
 * @param array $arr Array to prioritize
 * @param int $default_priority Defaults priority if priority not set
 * @return bool
 */
function pootlepb_prioritize_array( &$arr = array(), $default_priority = 25 ) {
	foreach ( $arr as $k => $v ) {
		$arr[ $k ]['id'] = $k;
		$arr[ $k ]['priority'] = empty( $arr[ $k ]['priority'] ) ? $default_priority : $arr[ $k ]['priority'];
	}
	uasort( $arr, 'pootlepb_priority_cmp' );
}

/**
 * Returns defaults from content style fields
 * @global $pootlepb_content_block_styling_fields
 * @return array
 */
function pootlepb_default_content_block_style( ) {
	global $pootlepb_content_block_styling_fields;

	$result = array( );
	foreach ( $pootlepb_content_block_styling_fields as $key => $field ) {
		if ( $field['type'] == 'border' ) {
			$result[$key . '-width'] = 0;
			$result[$key . '-color'] = '';
		} elseif ( $field['type'] == 'number' ) {
			$result[$key] = 0;
		} elseif ( $field['type'] == 'checkbox' ) {
			$result[$key] = '';
		} else{
			$result[$key] = '';
		}
	}
	return $result;
}

/**
 * Convert form post data into more efficient panels data.
 * @param $form_post
 * @return array
 * @since 0.1.0
 */
function pootlepb_get_panels_data_from_post( $form_post = null ) {

	if ( null == $form_post ) {
		$form_post = $_POST;
	}

	$panels_data = array();

	if ( ! empty( $_POST['pootlepb_noPB'] ) ) {
		return $panels_data;
	}

	$panels_data['widgets'] = array_values( stripslashes_deep( isset( $form_post['widgets'] ) ? $form_post['widgets'] : array() ) );

	foreach ( $panels_data['widgets'] as $i => $widget ) {

		if ( empty( $widget['info'] ) ) {
			continue;
		}

		$info = $widget['info'];

		$widget = json_decode( $widget['data'], true );

		if ( class_exists( $info['class'] ) ) {
			$the_block = new $info['class'];
			if ( method_exists( $the_block, 'update' ) && ! empty( $info['raw'] ) ) {
				$widget = $the_block->update( $widget, $widget );
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

	$panels_data['grids'] = array_values( stripslashes_deep( isset( $form_post['grids'] ) ? $form_post['grids'] : array() ) );
	$panels_data['grid_cells'] = array_values( stripslashes_deep( isset( $form_post['grid_cells'] ) ? $form_post['grid_cells'] : array() ) );

	return apply_filters( 'pootlepb_panels_data_from_post', $panels_data );
}

/**
 * Return value if key $key exists in array $arr else returns $default
 * @param array $arr Array to find the key in
 * @param string $key The key to find value of
 * @param string $default The default if key ain't set
 *
 * @return string
 */
 function pootlepb_array_key_value( $arr, $key, $default = '' ) {
	 if ( is_array( $arr ) && isset( $arr[ $key ] ) ) {
		 return $arr[ $key ];
	 } else {
		 return $default;
	 }
 }

/**
 * Compares priority
 *
 * @param array $a
 * @param array $b
 *
 * @return bool
 */
function pootlepb_array_cmp( $a, $b ) {
	global $pootlepb_array_cmp_ki;
	if ( ! $pootlepb_array_cmp_ki ) {
		reset( $array );
		$pootlepb_array_cmp_ki = key( $array );
	}

	return $a[ $pootlepb_array_cmp_ki ] > $b[ $pootlepb_array_cmp_ki ];
}

add_filter( 'the_content', function ( $content ) {
	return str_replace( array( '&amp;#10004;', '&amp;#10006;' ), array( '&#10004;', '&#10006;' ), $content );
}, 1, 99 );

function pootlepb_rand( $length = 16 ) {
	$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen( $characters );
	$randomString     = '';
	for ( $i = 0; $i < $length; $i ++ ) {
		$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
	}

	return $randomString;
}
