/**
 * Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */
jQuery(function($){
	var $bg = $( '#accordion-section-lib-panel-pootle-page-customizer-background' ),
		$bg_url = $( '#customize-control-pootle-page-customizer-background-image, .ppc-field.background-responsive-image .image-upload-path' ),
		$bg_options = $( '#customize-control-pootle-page-customizer-background-repeat, #customize-control-pootle-page-customizer-background-position, #customize-control-pootle-page-customizer-background-attachment' );

	$bg.find('select[data-customize-setting-link="pootle-page-customizer[background-type]"]' ).change( function () {
		var $t = $( this ),
			$all = $bg.find( '[id*="customize-control-pootle-page-customizer-background"]' )
				.not('#customize-control-pootle-page-customizer-background-type');
		$all.hide();

		switch ( $t.val() ) {
			case 'color':
				$( '#customize-control-pootle-page-customizer-background-color' ).show();
				break;
			case 'image':
				$( '#customize-control-pootle-page-customizer-background-image' ).show();
				$bg_options.show();
				break;
			case 'video':
				$( '#customize-control-pootle-page-customizer-background-video, #customize-control-pootle-page-customizer-background-responsive-image' ).show();
		}
	} ).change();
});