<?php
/**
 * Row settings
 */

/**
 * Add row styles.
 * @param $styles
 * @return mixed
 * @since 0.1.0
 */
function pootlepb_panels_row_styles( $styles ) {
	$styles['wide-grey'] = __( 'Wide Grey', 'vantage' );

	return $styles;
}

add_filter( 'pootlepb_row_styles', 'pootlepb_panels_row_styles' );

/**
 * Row styling from global settings
 * @param array $attr Row attributes
 * @param array $row Row settings
 * @return array Row attributes
 */
function pootlepb_panels_panels_row_attributes( $attr, $row ) {
	if ( ! empty( $row['style']['no_margin'] ) ) {
		if ( empty( $attr['style'] ) ) {
			$attr['style'] = '';
		}

		$attr['style'] .= 'margin-bottom: 0px;';

	} else {
		if ( empty( $attr['style'] ) ) {
			$attr['style'] = '';
		}

		$marginBottom = pootlepb_settings( 'margin-bottom' );

		if ( ! empty( $row['style']['margin_top'] ) ) {
			$attr['style'] .= "margin-top: {$row['style']['margin_top']}px;";
		}

		if ( ! empty( $row['style']['margin_bottom'] ) ) {
			$attr['style'] .= "margin-bottom: {$row['style']['margin_bottom']}px;";
		} elseif ( $marginBottom ) {
			$attr['style'] .= "margin-bottom: {$marginBottom}px;";
		} else {
			$attr['style'] .= 'margin-bottom: 0;';
		}
	}

	if ( isset( $row['style']['id'] ) && ! empty( $row['style']['id'] ) ) {
		$attr['id'] = $row['style']['id'];
	}

	return $attr;
}
add_filter( 'pootlepb_row_attributes', 'pootlepb_panels_panels_row_attributes', 10, 2 );
