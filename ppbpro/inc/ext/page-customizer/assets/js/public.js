/**
 * Created by shramee on 22/9/15.
 */
jQuery(document).ready(function( $ ){
	if ( 'string' == typeof window.pageCustoVideoUrl ) {
		$( '#page-customizer-bg-video' )
			.prependTo( $('body') )
			.removeClass('ppc-no-show').show().get(0).play();
	}
});