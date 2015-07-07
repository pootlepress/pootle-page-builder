<?php
/**
 * Code to handle the row styling
 * @since 0.1.0
 */

/**
 * Get all the row styles.
 *
 * @return array An array defining the row fields.
 * @since 0.1.0
 */
function pootlepb_style_get_fields() {
	static $fields = false;

	if ( false === $fields ) {
		$fields = array();

		$fields = apply_filters( 'pootlepb_row_style_fields', $fields );
	}

	return $fields;
}

function pootlepb_dialog_form_echo( $fields ) {

	foreach ( $fields as $name => $attr ) {

		echo '<p class="field_' . esc_attr( $name ) . '">';
		echo '<label>' . esc_attr( $attr['name'] ) . '</label>';

		switch ( $attr['type'] ) {
			case 'select':
				?>
				<select name="panelsStyle[<?php echo esc_attr( $name ) ?>]"
				        data-style-field="<?php echo esc_attr( $name ) ?>"
				        data-style-field-type="<?php echo esc_attr( $attr['type'] ) ?>">
					<?php foreach ( $attr['options'] as $ov => $on ) : ?>
						<option value="<?php echo esc_attr( $ov ) ?>"><?php echo esc_html( $on ) ?></option>
					<?php endforeach ?>
				</select>
				<?php
				break;

			case 'checkbox' :
				?>
				<label class="ppb-panels-checkbox-label">
					<input type="checkbox" name="panelsStyle[<?php echo esc_attr( $name ) ?>]"
					       data-style-field="<?php echo esc_attr( $name ) ?>"
					       data-style-field-type="<?php echo esc_attr( $attr['type'] ) ?>"/>
					Enabled
				</label>
				<?php
				break;

			case 'number' :
				?><input type="number" min="<?php echo esc_attr( $attr['min'] ) ?>" value="<?php echo $attr['default'] ?>"
				         name="panelsStyle[<?php echo esc_attr( $name ) ?>]"
				         data-style-field="<?php echo esc_attr( $name ) ?>"
				         data-style-field-type="<?php echo esc_attr( $attr['type'] ) ?>" /> <?php
				break;

			case 'upload':
				?><input type="text" id="pp-pb-<?php esc_attr_e( $name ) ?>"
				         name="panelsStyle[<?php echo esc_attr( $name ) ?>]"
				         data-style-field="<?php echo esc_attr( $name ) ?>"
				         data-style-field-type="<?php echo esc_attr( $attr['type'] ) ?>" />
				<button class="button upload-button">Select Image</button><?php
				break;

			default :
				?><input type="file" name="panelsStyle[<?php echo esc_attr( $name ) ?>]"
				         data-style-field="<?php echo esc_attr( $name ) ?>"
				         data-style-field-type="<?php echo esc_attr( $attr['type'] ) ?>" />
				<?php
				break;
		}

		echo '</p>';
	}
}

function pootlepb_hide_elements_dialog_echo( $fields ) {

	foreach ( $fields as $name => $attr ) {

		echo '<p>';
		echo '<label>' . esc_attr( $attr['name'] ) . '</label>';

		switch ( $attr['type'] ) {
			case 'checkbox' :
				?>
				<input type="checkbox" name="panelsStyle[<?php echo esc_attr( $name ) ?>]"
				       data-style-field="<?php echo esc_attr( $name ) ?>"
				       data-style-field-type="<?php echo esc_attr( $attr['type'] ) ?>"/>
				<?php
				break;
			default :
				?><input type="text" name="panelsStyle[<?php echo esc_attr( $name ) ?>]"
				         data-style-field="<?php echo esc_attr( $name ) ?>"
				         data-style-field-type="<?php echo esc_attr( $attr['type'] ) ?>" /> <?php
				break;
		}

		echo '</p>';
	}
}

function pootlepb_style_dialog_form() {
	$fields = pootlepb_style_get_fields();

	$sections = array();

	$sections['Background'][] = 'background_toggle';

	$sections['Background'][] = array( '<div class="bg_section bg_color">' );
	$sections['Background'][] = 'background';
	$sections['Background'][] = array( '</div>' );

	$sections['Background'][] = array( '<div class="bg_section bg_image">' );
	$sections['Background'][] = 'background_image';
	$sections['Background'][] = 'background_image_repeat';
	$sections['Background'][] = 'background_parallax';
	$sections['Background'][] = 'background_image_size';
	$sections['Background'][] = 'bg_overlay_color';
	$sections['Background'][] = 'bg_overlay_opacity';
	/** @hook pootlepb_row_styles_section_bg_image Add field id in background image sub section */
	$sections['Background']   = apply_filters( 'pootlepb_row_styles_section_bg_image', $sections['Background'] );
	$sections['Background'][] = array( '</div>' );

	$sections['Background'][] = array( '<div class="bg_section bg_video">' );
	$sections['Background'][] = 'bg_video';
	$sections['Background'][] = 'bg_mobile_image';
	/** @hook pootlepb_row_styles_section_bg_image Add field id in background video sub section */
	$sections['Background']   = apply_filters( 'pootlepb_row_styles_section_bg_video', $sections['Background'] );
	$sections['Background'][] = array( '</div>' );

	$sections['Layout'][] = 'full_width';
	$sections['Layout'][] = 'row_height';
	$sections['Layout'][] = 'hide_row';
	$sections['Layout'][] = 'margin_bottom';
	$sections['Layout'][] = 'col_gutter';
	/** @hook pootlepb_row_styles_section_bg_image Add field id in layout section */
	$sections['Layout'] = apply_filters( 'pootlepb_row_styles_section_layout', $sections['Layout'] );

	$sections['Advanced'][] = 'style';
	$sections['Advanced'][] = 'class';
	$sections['Advanced'][] = 'col_class';
	/** @hook pootlepb_row_styles_section_bg_image Add field id in advanced section */
	$sections['Advanced'] = apply_filters( 'pootlepb_row_styles_section_advanced', $sections['Advanced'] );

	if ( empty( $fields ) ) {
		_e( "Your theme doesn't provide any visual style fields. " );

		return;
	}

	$fields_output = '';

	echo '<ul class="ppb-acp-sidebar">';

	foreach ( $sections as $Sec => $secFields ) {

		$sec = strtolower( $Sec );

		echo '<li><a href="' . esc_attr( "#ppb-style-section-{$sec}" ) . '">' . $Sec . '</a></li>';

		ob_start();

		echo '<div id="' . esc_attr( "ppb-style-section-{$sec}" ) . '" class="ppb-style-section">';

		foreach ( $secFields as $name ) {

			if ( is_array( $name ) ) {
				echo wp_kses( $name[0], wp_kses_allowed_html( 'post' ) );
				continue;
			}

			$attr = $fields[ $name ];

			echo '<div class="field field_' . esc_attr( $name ) . '">';

			echo '<label>' . esc_html( $attr['name'] );
			echo '</label>';
			pootlepb_render_single_field( $name, $attr );
			if ( isset( $attr['help-text'] ) ) {
				echo '<span class="dashicons dashicons-editor-help tooltip" data-tooltip="' . esc_html( $attr['help-text'] ) . '"></span>';
			}
			echo '</div>';
		}

		echo '</div>';

		$fields_output .= ob_get_clean();
	}

	echo '</ul>';
	echo $fields_output;

}

function pootlepb_render_single_field( $name, $attr ) {

	switch ( $attr['type'] ) {
		case 'select':
			?>
			<select name="panelsStyle[<?php echo esc_attr( $name ) ?>]"
			        data-style-field="<?php echo esc_attr( $name ) ?>"
			        data-style-field-type="<?php echo esc_attr( $attr['type'] ) ?>">
				<?php foreach ( $attr['options'] as $ov => $on ) : ?>
					<option
						value="<?php echo esc_attr( $ov ) ?>" <?php if ( isset( $attr['default'] ) ) { selected( $ov, $attr['default'] ); } ?>  ><?php echo esc_html( $on ) ?></option>
				<?php endforeach ?>
			</select>
			<?php
			break;

		case 'checkbox' :
			$checked = ( isset( $attr['default'] ) ? checked( $attr['default'], true, false ) : '' );
			?>
			<label class="ppb-panels-checkbox-label">
				<input type="checkbox" <?php echo esc_html( $checked ) ?> name="panelsStyle[<?php echo esc_attr( $name ) ?>]"
				       data-style-field="<?php echo esc_attr( $name ) ?>"
				       data-style-field-type="<?php echo esc_attr( $attr['type'] ) ?>"/>
			</label>
			<?php
			break;

		case 'number' :
			?><input type="number" min="<?php echo $attr['min'] ?>" value="<?php echo esc_attr( $attr['default'] ) ?>"
			         name="panelsStyle[<?php echo esc_attr( $name ) ?>]"
			         data-style-field="<?php echo esc_attr( $name ) ?>"
			         data-style-field-type="<?php echo esc_attr( $attr['type'] ) ?>" />
			<?php
			if ( isset( $attr['help-text'] ) ) {
				// don't use div for this or else div will appear outside of <p>
				echo "<span class='small-help-text'>" . esc_html( $attr['help-text'] ) . '</span>';
			}
			break;

		case 'upload':
			?><input type="text" id="pp-pb-<?php esc_attr_e( $name ) ?>"
			         name="panelsStyle[<?php echo esc_attr( $name ) ?>]"
			         data-style-field="<?php echo esc_attr( $name ) ?>"
			         data-style-field-type="<?php echo esc_attr( $attr['type'] ) ?>" />
			<button class="button upload-button">Select Image</button><?php
			break;

		case 'uploadVid':
			?><input type="text" id="pp-pb-<?php esc_attr_e( $name ) ?>"
			         name="panelsStyle[<?php echo esc_attr( $name ) ?>]"
			         data-style-field="<?php echo esc_attr( $name ) ?>"
			         data-style-field-type="<?php echo esc_attr( $attr['type'] ) ?>" />
			<button class="button video-upload-button">Select Video</button><?php
			break;

		case 'textarea':
			?><textarea type="text" name="panelsStyle[<?php echo esc_attr( $name ) ?>]"
			            data-style-field="<?php echo esc_attr( $name ) ?>"
			            data-style-field-type="<?php echo esc_attr( $attr['type'] ) ?>" ></textarea> <?php
			break;

		case 'slider':
			?><input type="hidden" name="panelsStyle[<?php echo esc_attr( $name ) ?>]"
			         data-style-field="<?php echo esc_attr( $name ) ?>"
			         data-style-field-type="<?php echo esc_attr( $attr['type'] ) ?>" />
			<div data-style-field-type="<?php echo esc_attr( $attr['type'] ) ?>"></div><span class="slider-val"></span>
			<?php
			break;

		case 'px':
			?><input type="number" name="panelsStyle[<?php echo esc_attr( $name ) ?>]"
			         data-style-field="<?php echo esc_attr( $name ) ?>"
			         data-style-field-type="<?php echo esc_attr( $attr['type'] ) ?>" />px <?php
			break;
		default :
			?><input type="text" name="panelsStyle[<?php echo esc_attr( $name ) ?>]"
			         data-style-field="<?php echo esc_attr( $name ) ?>"
			         data-style-field-type="<?php echo esc_attr( $attr['type'] ) ?>" /> <?php
			break;
	}
}

function pootlepb_block_styles_dialog_form( $advanced = null ) {
	$fields = pootlepb_block_styling_fields();

	foreach ( $fields as $key => $field ) {

		if ( empty( $advanced ) ) {
			if ( ! empty( $field['advanced'] ) ) {
				continue;
			}
		} else {
			if ( empty( $field['advanced'] ) ) {
				continue;
			}
		}

		echo "<div class='field'>";
		echo '<label>' . esc_html( $field['name'] ) . '</label>';
		echo '<span>';

		switch ( $field['type'] ) {
			case 'color' :
				?><input dialog-field="<?php echo esc_attr( $key ) ?>" class="widget-<?php echo esc_attr( $key ) ?>" type="text"
				         data-style-field-type="color"/>
				<?php
				break;
			case 'border' :
				?><input dialog-field="<?php echo esc_attr( $key ) ?>-width" class="widget-<?php echo esc_attr( $key ) ?>-width" type="number"
				         min="0" max="100" step="1" value="" /> px
				<input dialog-field="<?php echo esc_attr( $key ) ?>-color" class="widget-<?php echo esc_attr( $key ) ?>-color" type="text"
				       data-style-field-type="color"/>
				<?php
				break;
			case 'number' :
				?><input dialog-field="<?php echo esc_attr( $key ) ?>" class="widget-<?php echo esc_attr( $key ) ?>" type="number"
				         min="<?php esc_attr_e( $field['min'] ) ?>" max="<?php esc_attr_e( $field['max'] ) ?>"
				         step="<?php esc_attr_e( $field['step'] ) ?>" value="" /> <?php esc_html_e( $field['unit'] ) ?>
				<?php
				break;
			case 'checkbox':
				?><input dialog-field="<?php echo esc_attr( $key ) ?>" class="widget-<?php echo esc_attr( $key ) ?>" type="checkbox"
				         value="<?php esc_attr_e( $field['value'] ) ?>" data-style-field-type="checkbox" />
				<?php
				break;
			case 'textarea':
				?><textarea dialog-field="<?php echo esc_attr( $key ) ?>" class="widget-<?php echo esc_attr( $key ) ?>"
				            data-style-field-type="text"></textarea>
				<?php
				break;
			default:
				?><input dialog-field="<?php echo esc_attr( $key ) ?>" class="widget-<?php echo esc_attr( $key ) ?>" type="text"
				         data-style-field-type="text"/>
				<?php
				break;
		}

		echo '</span>';
		echo '</div>';
	}
}

/**
 * Check if we're using a color in any of the style fields.
 *
 * @return bool
 * @since 0.1.0
 */
function pootlepb_style_is_using_color() {
	$fields = pootlepb_style_get_fields();

	foreach ( $fields as $id => $attr ) {
		if ( isset( $attr['type'] ) && 'color' == $attr['type'] ) {
			return true;
		}
	}

	return false;
}

/**
 * Convert the single string attribute of the grid style into an array.
 *
 * @param $panels_data
 *
 * @return mixed
 * @since 0.1.0
 */
function pootlepb_style_update_data( $panels_data ) {
	if ( empty( $panels_data['grids'] ) ) {
		return $panels_data;
	}

	$num_grids = count( $panels_data['grids'] );

	for ( $i = 0; $i < $num_grids; $i ++ ) {

		if ( isset( $panels_data['grids'][ $i ]['style'] ) && is_string( $panels_data['grids'][ $i ]['style'] ) ) {
			$panels_data['grids'][ $i ]['style'] = array( 'class' => $panels_data['grids'][ $i ]['style'] );
		}
	}

	return $panels_data;
}

add_filter( 'pootlepb_data', 'pootlepb_style_update_data' );
add_filter( 'pootlepb_prebuilt_layout', 'pootlepb_style_update_data' );

/**
 * Sanitize all the data that's come from post data
 *
 * @param $panels_data
 *
 * @since 0.1.0
 */
function pootlepb_style_sanitize_data( $panels_data ) {
	$fields = pootlepb_style_get_fields();

	if ( empty( $fields ) ) {
		return $panels_data;
	}
	if ( empty( $panels_data['grids'] ) || ! is_array( $panels_data['grids'] ) ) {
		return $panels_data;
	}

	$num_grids = count( $panels_data['grids'] );

	for ( $i = 0; $i < $num_grids; $i ++ ) {

		foreach ( $fields as $name => $attr ) {
			switch ( $attr['type'] ) {
				case 'checkbox':
					// Convert the checkbox value to true or false.
					$panels_data['grids'][ $i ]['style'][ $name ] = ! empty( $panels_data['grids'][ $i ]['style'][ $name ] );
					break;

				case 'number':
					$panels_data['grids'][ $i ]['style'][ $name ] = intval( $panels_data['grids'][ $i ]['style'][ $name ] );
					break;

				case 'url':
					$panels_data['grids'][ $i ]['style'][ $name ] = esc_url_raw( $panels_data['grids'][ $i ]['style'][ $name ] );
					break;

				case 'select' :
					// Make sure the value is in the options
					if ( ! in_array( $panels_data['grids'][ $i ]['style'][ $name ], array_keys( $attr['options'] ) ) ) {
						$panels_data['grids'][ $i ]['style'][ $name ] = false;
					}
					break;
			}
		}
	}

	return $panels_data;
}

add_filter( 'pootlepb_panels_data_from_post', 'pootlepb_style_sanitize_data' );