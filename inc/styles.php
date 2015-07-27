<?php
/**
 * Code to handle the row styling
 * @since 0.1.0
 */

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

function pootlepb_row_dialog_fields_output( $tab = null ) {

	//Row settings panel fields
	$fields = pootlepb_row_settings_fields();

	foreach ( $fields as $key => $field ) {

		//Skip if current fields doesn't belong to the specified tab
		if ( ! empty( $tab ) && $tab != $field['tab'] ) { continue; }

		//Output html field
		if ( 'html' == $field['type'] ) {
			echo wp_kses( $field['name'], wp_kses_allowed_html( 'post' ) );
			continue;
		}

		echo '<div class="field field_' . esc_attr( $key ) . '">';

		echo '<label>' . esc_html( $field['name'] );
		echo '</label>';
		pootlepb_render_row_settings_field( $key, $field );
		if ( isset( $field['help-text'] ) ) {
			echo '<span class="dashicons dashicons-editor-help tooltip" data-tooltip="' . esc_html( $field['help-text'] ) . '"></span>';
		}
		echo '</div>';

	}
}

function pootlepb_render_row_settings_field( $key, $field ) {

	switch ( $field['type'] ) {
		case 'select':
			?>
			<select name="panelsStyle[<?php echo esc_attr( $key ) ?>]" id="pp-pb-<?php esc_attr_e( $key ) ?>"
			        data-style-field="<?php echo esc_attr( $key ) ?>"
			        data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>">
				<?php foreach ( $field['options'] as $ov => $on ) : ?>
					<option
						value="<?php echo esc_attr( $ov ) ?>" <?php if ( isset( $field['default'] ) ) { selected( $ov, $field['default'] ); } ?>  ><?php echo esc_html( $on ) ?></option>
				<?php endforeach ?>
			</select>
			<?php
			break;

		case 'checkbox' :
			$checked = ( isset( $field['default'] ) ? checked( $field['default'], true, false ) : '' );
			?>
			<label class="ppb-panels-checkbox-label">
				<input type="checkbox" <?php echo esc_html( $checked ) ?> name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
				       data-style-field="<?php echo esc_attr( $key ) ?>"
				       data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>"/>
			</label>
			<?php
			break;

		case 'number' :
			?><input type="number" min="<?php echo $field['min'] ?>" value="<?php echo esc_attr( $field['default'] ) ?>"
			         name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
			         data-style-field="<?php echo esc_attr( $key ) ?>"
			         data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>" />
			<?php
			if ( isset( $field['help-text'] ) ) {
				// don't use div for this or else div will appear outside of <p>
				echo "<span class='small-help-text'>" . esc_html( $field['help-text'] ) . '</span>';
			}
			break;

		case 'upload':
			?><input type="text" id="pp-pb-<?php esc_attr_e( $key ) ?>"
			         name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
			         data-style-field="<?php echo esc_attr( $key ) ?>"
			         data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>" />
			<button class="button upload-button">Select Image</button><?php
			break;

		case 'uploadVid':
			?><input type="text" id="pp-pb-<?php esc_attr_e( $key ) ?>"
			         name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
			         data-style-field="<?php echo esc_attr( $key ) ?>"
			         data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>" />
			<button class="button video-upload-button">Select Video</button><?php
			break;

		case 'textarea':
			?><textarea type="text" name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
			            data-style-field="<?php echo esc_attr( $key ) ?>"
			            data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>" ></textarea> <?php
			break;

		case 'slider':
			?><input type="hidden" name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
			         data-style-field="<?php echo esc_attr( $key ) ?>"
			         data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>" />
			<div class="ppb-slider"></div><span class="slider-val"></span>
			<?php
			break;

		case 'px':
			?><input type="number" name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
			         data-style-field="<?php echo esc_attr( $key ) ?>"
			         data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>" />px <?php
			break;

		case 'color':
			?><input type="text" name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
			         data-style-field="<?php echo esc_attr( $key ) ?>"
			         data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>" /> <?php
			break;

		case 'text':
			?><input type="text" name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
			         data-style-field="<?php echo esc_attr( $key ) ?>"
			         data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>" /> <?php
			break;
		default :

			/**
			 * Allows rendering custom fields
			 * @param string $key The ID of field
			 * @param array $field Field data
			 */
			do_action( "row_styling_custom_field_{$field['type']}", $key, $field );
	}
}

function pootlepb_block_dialog_fields_output( $tab = null ) {
	$fields = pootlepb_block_styling_fields();

	foreach ( $fields as $key => $field ) {

		if ( ! empty( $tab ) ) {
			if ( $tab != $field['tab'] ) {
				continue;
			}
		}

		echo "<div class='field'>";
		echo '<label>' . esc_html( $field['name'] ) . '</label>';
		echo '<span>';

		pootlepb_render_content_field( $key, $field );

		echo '</span>';
		echo '</div>';
	}
}

function pootlepb_render_content_field( $key, $field ) {
	switch ( $field['type'] ) {
		case 'color' :
			?><input dialog-field="<?php echo esc_attr( $key ) ?>" class="content-block-<?php echo esc_attr( $key ) ?>" type="text"
			         data-style-field-type="color"/>
			<?php
			break;
		case 'border' :
			?><input dialog-field="<?php echo esc_attr( $key ) ?>-width" class="content-block-<?php echo esc_attr( $key ) ?>-width" type="number"
			         min="0" max="100" step="1" value="" /> px
			<input dialog-field="<?php echo esc_attr( $key ) ?>-color" class="content-block-<?php echo esc_attr( $key ) ?>-color" type="text"
			       data-style-field-type="color"/>
			<?php
			break;
		case 'number' :
			?><input dialog-field="<?php echo esc_attr( $key ) ?>" class="content-block-<?php echo esc_attr( $key ) ?>" type="number"
			         min="<?php esc_attr_e( $field['min'] ) ?>" max="<?php esc_attr_e( $field['max'] ) ?>"
			         step="<?php esc_attr_e( $field['step'] ) ?>" value="" /> <?php esc_html_e( $field['unit'] ) ?>
			<?php
			break;
		case 'checkbox':
			?><input dialog-field="<?php echo esc_attr( $key ) ?>" class="content-block-<?php echo esc_attr( $key ) ?>" type="checkbox"
			         value="1" data-style-field-type="checkbox" />
			<?php
			break;
		case 'textarea':
			?><textarea dialog-field="<?php echo esc_attr( $key ) ?>" class="content-block-<?php echo esc_attr( $key ) ?>"
			            data-style-field-type="text"></textarea>
			<?php
			break;
		case 'upload':
			?><input dialog-field="<?php echo esc_attr( $key ) ?>" class="content-block-<?php echo esc_attr( $key ) ?>" type="text"
			         data-style-field-type="upload"/>
			<button class="button upload-button">Select Image</button><?php
			break;
		case 'slider':
			?><input dialog-field="<?php echo esc_attr( $key ) ?>" class="content-block-<?php echo esc_attr( $key ) ?>" type="hidden"
			         data-style-field-type="slider"/>
			<div class="ppb-slider"></div><span class="slider-val"></span>
			<?php
			break;
		case 'text':
			?><input dialog-field="<?php echo esc_attr( $key ) ?>" class="content-block-<?php echo esc_attr( $key ) ?>" type="text"
			         data-style-field-type="text"/>
			<?php
			break;
		default:
			/**
			 * Allows rendering custom fields
			 * @param string $key The ID of field
			 * @param array $field Field data
			 */
			do_action( "content_block_custom_field_{$field['type']}", $key, $field );
	}
}

/**
 * Check if we're using a color in any of the style fields.
 *
 * @return bool
 * @since 0.1.0
 */
function pootlepb_style_is_using_color() {
	$fields = pootlepb_row_settings_fields();

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
	$fields = pootlepb_row_settings_fields();

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