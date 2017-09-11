<?php
/**
 * Handles style and settings fields rendering
 * @since 0.1.0
 */

/**
 * Renders content block fields
 * @param string $tab The tab to render the fields for
 * @since 0.1.0
 */
function pootlepb_block_dialog_fields_output( $tab = null ) {

	//Content block panel fields
	$fields = pootlepb_block_styling_fields();

	//Prioritize array
	pootlepb_prioritize_array( $fields );

	foreach ( $fields as $field ) {

		$key = $field['id'];

		if ( ! empty( $tab ) ) {
			if ( $tab != $field['tab'] ) {
				continue;
			}
		}

		//Output html field
		if ( 'html' == $field['type'] ) {
			echo $field['name'];
			continue;
		}

		echo "<div class='field field-" . $key . " field_type-" . $field['type'] . "'>";
		echo '<label>' . esc_html( $field['name'] );
		if ( isset( $field['help-text'] ) ) {
			echo '<span class="dashicons dashicons-editor-help tooltip" data-tooltip="' . esc_html( $field['help-text'] ) . '"></span>';
		}
		echo '</label>';

		echo '<span>';
		pootlepb_render_content_block_field( $key, $field );
		echo '</span>';

		echo '</div>';
	}
}

/**
 * Renders field for content panel
 * @param string $key Field id
 * @param array $field Field data
 */
function pootlepb_render_content_block_field( $key, $field ) {
	$placeholder = '';
	if ( ! empty( $field['placeholder'] ) ) {
		$placeholder = "placeholder='{$field['placeholder']}'";
	}
	switch ( $field['type'] ) {
		case 'color' :
			?><input dialog-field="<?php echo esc_attr( $key ) ?>" class="content-block-<?php echo esc_attr( $key ) ?>" type="text"
			         data-style-field-type="<?php echo $field['type']; ?>"/>
			<?php
			break;
		case 'select':
			?>
			<select <?php echo $placeholder ?> dialog-field="<?php echo esc_attr( $key ) ?>" class="content-block-<?php echo esc_attr( $key ) ?>"
			                                   data-style-field-type="<?php echo $field['type']; ?>">
				<?php foreach ( $field['options'] as $ov => $on ) : ?>
					<option
						value="<?php echo esc_attr( $ov ) ?>" <?php if ( isset( $field['default'] ) ) { selected( $ov, $field['default'] ); } ?>  ><?php echo esc_html( $on ) ?></option>
				<?php endforeach ?>
			</select>
			<?php
			break;
		case 'radio':
			foreach ( $field['options'] as $ov => $on ) {
				echo '<label>';
				?>
				<input name="ppb-content-panel-radio-<?php echo esc_attr( $key ) ?>" <?php echo $placeholder ?>
				       type="<?php echo $field['type']; ?>" data-style-field-type="<?php echo $field['type']; ?>"
				       dialog-field="<?php echo esc_attr( $key ) ?>"
				       class="content-block-<?php echo esc_attr( $key ) ?>"
				       value="<?php echo esc_attr( $ov ) ?>">
				<?php
				echo wp_kses( $on, wp_kses_allowed_html( 'post' ) );
				echo '</label>';
			}
			?>
			<?php
			break;
		case 'multi-select':
			?>
			<select <?php echo 'data-' . $placeholder ?> dialog-field="<?php echo esc_attr( $key ) ?>"
			        class="ppb-chzn-multi" class="content-block-<?php echo esc_attr( $key ) ?>"
			        data-style-field-type="<?php echo $field['type']; ?>" multiple="multiple">
				<?php foreach ( $field['options'] as $ov => $on ) : ?>
					<option
						value="<?php echo esc_attr( $ov ) ?>" <?php if ( isset( $field['default'] ) ) { selected( $ov, $field['default'] ); } ?>  ><?php echo esc_html( $on ) ?></option>
				<?php endforeach ?>
			</select>
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
			$field = wp_parse_args( $field, array(
				'min'   => '-9999',
				'max'   => '9999',
				'step'  => '1',
				'unit'  => '',
			) );
			?><input <?php echo $placeholder ?> dialog-field="<?php echo esc_attr( $key ) ?>" class="content-block-<?php echo esc_attr( $key ) ?>" type="number"
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
			?><textarea <?php echo $placeholder ?> dialog-field="<?php echo esc_attr( $key ) ?>" class="content-block-<?php echo esc_attr( $key ) ?>"
			            data-style-field-type="text"></textarea>
			<?php
			break;
		case 'upload':
			?><input <?php echo $placeholder ?> dialog-field="<?php echo esc_attr( $key ) ?>" class="content-block-<?php echo esc_attr( $key ) ?>" type="text"
			         data-style-field-type="upload"/>
			<span class="dashicons dashicons-no-alt" onclick="jQuery(this).siblings('input').val('').change();">Remove Image</span><br>
			<button class="button upload-button">Select Image</button>
			<small>OR</small>
			<span class="search-unsplash-wrap" style="display: none;">
				<input placeholder="Search Free Photos..." type="search">
				<span class="dashicons dashicons-search unsplash-button">Search Free Photos</span>
				<span class="dashicons dashicons-editor-help tooltip-unsplash" data-tooltip="All stock photos are sourced from Unsplash.com and are licensed under Creative Commons Zero which means you can copy, modify, distribute and use the photos for free, including commercial purposes, without asking permission from or providing attribution to the photographer or Unsplash"></span>
			</span>
			<?php
			break;
		case 'slider':
			$field = wp_parse_args( $field, array(
				'min' => '0',
				'default' => '0',
				'max' => '1',
				'step' => '0.05',
				'unit' => '%',
				'show_actual' => false,
			) );
			?><input dialog-field="<?php echo esc_attr( $key ) ?>" class="content-block-<?php echo esc_attr( $key ) ?>" type="hidden"
			         data-style-field-type="slider"/>
			<div class="ppb-slider"
			     <?php
			     if ( ! empty( $field['show_actual'] ) ) {
				     echo 'data-show-actual-val="1"';
			     }
			     ?>
			     data-min="<?php echo $field['min'] ?>"
			     data-max="<?php echo $field['max'] ?>"
			     data-default="<?php echo $field['default'] ?>"
			     data-step="<?php echo $field['step'] ?>"
			     data-unit="<?php echo $field['unit'] ?>"
				></div><span class="slider-val"></span>
			<?php
			break;
		case 'text':
			?><input <?php echo $placeholder ?> dialog-field="<?php echo esc_attr( $key ) ?>" class="content-block-<?php echo esc_attr( $key ) ?>" type="text"
			         data-style-field-type="text"/>
			<?php
			break;
		default:
			/**
			 * Allows rendering custom fields
			 * @param string $key The ID of field
			 * @param array $field Field data
			 */
			do_action( "pootlepb_content_block_custom_field_{$field['type']}", $key, $field );
	}
}

/**
 * Renders row settings fields
 * @param string $tab The tab to render the fields for
 * @since 0.1.0
 */
function pootlepb_row_dialog_fields_output( $tab = null ) {

	//Row settings panel fields
	$fields = pootlepb_row_settings_fields();

	//Prioritize array
	pootlepb_prioritize_array( $fields );

	foreach ( $fields as $field ) {

		$key = $field['id'];

		//Skip if current fields doesn't belong to the specified tab
		if ( ! empty( $tab ) && $tab != $field['tab'] ) { continue; }

		//Output html field
		if ( 'html' == $field['type'] ) {
			echo $field['name'];
			continue;
		}

		echo '<div class="field field_' . esc_attr( $key ) . ' field_type_' . esc_attr( $field['type'] ) . '">';

		echo '<label>' . esc_html( $field['name'] );

		if ( isset( $field['help-text'] ) ) {
			echo '<span class="dashicons dashicons-editor-help tooltip" data-tooltip="' . esc_html( $field['help-text'] ) . '"></span>';
		}
		echo '</label>';

		echo '<span>';
		pootlepb_render_row_settings_field( $key, $field );
		echo '</span>';
		echo '</div>';

	}
}

/**
 * Renders field for row settings panel
 * @param string $key Field id
 * @param array $field Field data
 */
function pootlepb_render_row_settings_field( $key, $field ) {
	$placeholder = '';
	if ( ! empty( $field['placeholder'] ) ) {
		$placeholder = " placeholder='{$field['placeholder']}'";
	}
	switch ( $field['type'] ) {
		case 'select':
			?>
			<select  <?php echo $placeholder ?> name="panelsStyle[<?php echo esc_attr( $key ) ?>]" id="pp-pb-<?php esc_attr_e( $key ) ?>"
			                                    data-style-field="<?php echo esc_attr( $key ) ?>"
			                                    data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>">
				<?php foreach ( $field['options'] as $ov => $on ) : ?>
					<option
						value="<?php echo esc_attr( $ov ) ?>" <?php if ( isset( $field['default'] ) ) {
						selected( $ov, $field['default'] );
					} ?>  ><?php echo esc_html( $on ) ?></option>
				<?php endforeach ?>
			</select>
			<?php
			break;
		case 'radio':
			foreach ( $field['options'] as $ov => $on ) {
				echo '<label>';
				?>
				<input type="radio" value="<?php echo $ov ?>"
				       name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
				       data-style-field="<?php echo esc_attr( $key ) ?>"
				       data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>"/>
				<?php
				echo wp_kses( $on, wp_kses_allowed_html( 'post' ) );
				echo '</label>';
			}
			break;
		case 'multi-select':
			?>
			<select <?php echo 'data-' . $placeholder ?> class="ppb-chzn-multi" name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
			        data-style-field="<?php echo esc_attr( $key ) ?>" multiple="multiple" id="pp-pb-<?php esc_attr_e( $key ) ?>"
			        data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>">
				<?php foreach ( $field['options'] as $ov => $on ) : ?>
					<option
						value="<?php echo esc_attr( $ov ) ?>" <?php if ( isset( $field['default'] ) ) {
						selected( $ov, $field['default'] );
					} ?>  ><?php echo esc_html( $on ) ?></option>
				<?php endforeach ?>
			</select>
			<?php
			break;
		case 'checkbox' :
			$checked = ( isset( $field['default'] ) ? checked( $field['default'], true, false ) : '' );
			?>
			<label class="ppb-panels-checkbox-label">
				<input type="checkbox" <?php echo esc_html( $checked ) ?>
				       name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
				       data-style-field="<?php echo esc_attr( $key ) ?>"
				       data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>"/>
			</label>
			<?php
			break;
		case 'number' :
			$field = wp_parse_args( $field, array(
				'min'  => '-9999',
				'max'  => '9999',
				'step' => '1',
				'unit' => '',
			) );
			?><input <?php echo $placeholder ?> type="number" name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
			         max="<?php echo $field['max'] ?>" min="<?php echo $field['min'] ?>"
			         step="<?php echo $field['step'] ?>" data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>"
			         data-style-field="<?php echo esc_attr( $key ) ?>" /><?php
			if ( ! empty( $field['unit'] ) ) {
				?><span class="unit"><?php esc_html_e( $field['unit'] ) ?></span><?php
			}
			break;
		case 'upload':
			?><input <?php echo $placeholder ?> type="text" id="pp-pb-<?php esc_attr_e( $key ) ?>"
			         name="panelsStyle[<?php echo esc_attr( $key ) ?>]" value=""
			         data-style-field="<?php echo esc_attr( $key ) ?>"
			         data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>" />
			<span class="dashicons dashicons-no-alt" onclick="jQuery(this).siblings('input').val('').change();">Remove Image</span><br>
			<button class="button upload-button">Select Image</button>
			<small>OR</small>
			<span class="search-unsplash-wrap" style="display: none;">
				<input placeholder="Search Free Photos..." type="search">
				<span class="dashicons dashicons-search unsplash-button">Search Free Photos</span>
				<span class="dashicons dashicons-editor-help tooltip-unsplash" data-tooltip="All stock photos are sourced from Unsplash.com and are licensed under Creative Commons Zero which means you can copy, modify, distribute and use the photos for free, including commercial purposes, without asking permission from or providing attribution to the photographer or Unsplash"></span>
			</span>
			<?php
			break;
		case 'uploadVid':
			?><input <?php echo $placeholder ?> type="text" id="pp-pb-<?php esc_attr_e( $key ) ?>"
			         name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
			         data-style-field="<?php echo esc_attr( $key ) ?>"
			         data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>" />
			<button class="button video-upload-button">Select Video</button><?php
			break;
		case 'textarea':
			?><textarea <?php echo $placeholder ?> name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
			            data-style-field="<?php echo esc_attr( $key ) ?>" type="text"
			            data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>" ></textarea> <?php
			break;
		case 'slider':
			$field = wp_parse_args( $field, array(
				'min' => '0',
				'default' => '0',
				'max' => '1',
				'step' => '0.05',
				'unit' => '%',
				'show_actual' => false,
			) );
			?><input type="hidden" name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
			         data-style-field="<?php echo esc_attr( $key ) ?>"
			         data-style-field-type="slider"/>
			<div class="ppb-slider"
			     <?php
			     if ( ! empty( $field['show_actual'] ) ) {
				     echo 'data-show-actual-val="1"';
			     }
			     ?>
			     data-min="<?php echo $field['min'] ?>"
			     data-max="<?php echo $field['max'] ?>"
			     data-default="<?php echo $field['default'] ?>"
			     data-step="<?php echo $field['step'] ?>"
			     data-unit="<?php echo $field['unit'] ?>"
				></div><span class="slider-val"></span>
			<?php
			break;
		case 'px':
			?><input <?php echo $placeholder ?> type="number" name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
			         data-style-field="<?php echo esc_attr( $key ) ?>"
			         data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>" />px <?php
			break;
		case 'color':
			?><input type="text" name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
			         data-style-field="<?php echo esc_attr( $key ) ?>"
			         data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>" /> <?php
			break;
		case 'text':
			?><input <?php echo $placeholder ?> type="text" name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
			         data-style-field="<?php echo esc_attr( $key ) ?>"
			         data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>" /> <?php
			break;
		default :
			/**
			 * Allows rendering custom fields
			 * @param string $key The ID of field
			 * @param array $field Field data
			 */
			do_action( "pootlepb_row_settings_custom_field_{$field['type']}", $key, $field );
	}
}

/**
 * Check if we're using a color in any of the style fields.
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
 * @param $panels_data
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
 * @param $panels_data
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