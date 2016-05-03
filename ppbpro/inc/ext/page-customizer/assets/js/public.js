/**
 * Created by shramee on 22/9/15.
 */
jQuery(document).ready(function( $ ){
	if ( 'undefined' != typeof pageCustomizerFixedBackground && pageCustomizerFixedBackground ) {
		var $fixBg = $( '#page-customizer-bg-fixed' );
		$fixBg
			.prependTo( $('body') )
			.removeClass('ppc-no-show').show();
		if ( 'video' == $fixBg.prop('tagName') ) {
			$fixBg.get( 0 ).play();
		}
	}
});