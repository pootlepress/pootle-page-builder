/**
 * Created by shramee on 09/08/16.
 */
jQuery( function ( $ ) {
	$( '.ppb-enabled-modules-list' ).sortable( {
		connectWith : '.ppb-disabled-modules-list',
		items       : '.ppb-module',
		receive: function( event, ui ) {
			ui.item.find('input').attr( 'name', 'ppb_enabled_addons[]' );
		}
	} );
	$( '.ppb-disabled-modules-list' ).sortable( {
		connectWith : '.ppb-enabled-modules-list',
		items       : '.ppb-module',
		receive: function( event, ui ) {
			ui.item.find('input').attr( 'name', 'ppb_disabled_addons[]' );
		}
	} );

} );