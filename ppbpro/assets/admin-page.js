/**
 * Plugin options page script
 *
 * @package Pootle_Page_Builder_Pro
 * @developer shramee <shramee.srivastav@gmail.com>
 */
jQuery( function ( $ ) {
	$( '.ppb-addon-card a.activate' ).click( function () {
		var $t = $( this );
		$t.closest( '.ppb-addon-card' ).addClass( 'active' );
		$t.siblings( 'input' ).val( 'active' );
	} )

	$( '.ppb-addon-card a.deactivate' ).click( function () {
		var $t = $( this );
		$t.closest( '.ppb-addon-card' ).removeClass( 'active' );
		$t.siblings( 'input' ).val( '' );
	} )
} );