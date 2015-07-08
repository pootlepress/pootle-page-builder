<?php
/**
 * Created by shramee
 * At: 7:59 PM 8/7/15
 */

/**
 * Add your own tabs to Content block editor panel
 *	'tabName' => array(
 *		'label' => 'Tab Name',
 *		'priority' => 10,
 *	),
 */
$pootlepb_content_block_tabs = apply_filters( 'pootlepb_content_block_tabs', $pootlepb_content_block_tabs );

$fields = pootlepb_row_settings_fields();

$sections = array();

foreach ( $fields as $k => $f ) {
	$sections[ $f['tab'] ][ $k ] = $f['priority'];
}

//print_awesome_r( $sections, 'Sections' );

if ( empty( $fields ) ) {
	_e( "Your theme doesn't provide any visual style fields. " );
	return;
}

$fields_output = '';

echo '<ul class="ppb-acp-sidebar">';

foreach ( $sections as $Sec => $secFields ) {

	asort( $secFields );

	$sec = strtolower( $Sec );

	echo '<li><a href="' . esc_attr( "#ppb-style-section-{$sec}" ) . '">' . $Sec . '</a></li>';

	ob_start();

	echo '<div id="' . esc_attr( "ppb-style-section-{$sec}" ) . '" class="ppb-style-section">';

	foreach ( $secFields as $name => $priority ) {

		$attr = $fields[ $name ];

		if ( 'html' == $attr['type'] ) {
			echo wp_kses( $attr['name'], wp_kses_allowed_html( 'post' ) );
			continue;
		}

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
