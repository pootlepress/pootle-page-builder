/**
 * Created by shramee on 13/5/15.
 * @since 0.1.0
 */
(function ($) {

	$(document).ready(function () {

		var dialog = $( '<div />' )
			.attr( 'id', 'ppb-hard-unin-dialog' )
			.appendTo( $( 'body' ) )
			.ppbDialog( {
				autoOpen	: false,
				title		: "Are you sure",
				width		: 500,
				buttons		: [
					{
						text	: 'Cancel',
						click	: function () {
							$( '#pootlepb-hard-uninstall' ).prop( 'checked', false );
							dialog.ppbDialog( 'close' );
						}
					},
					{
						text	: 'Yes, I\'m sure',
						click	: function () {
							dialog.ppbDialog( 'close' );
						}
					}
				]
			} );
		$( '#pootlepb-hard-uninstall' ).change( function () {
			var $t = $( this );

			if ( $t.prop( 'checked' ) ) {
				dialog
					.html( 'Are you really sure you want to enable this? When enabled this will delete ALL settings and layouts for page builder pages when you delete page builder from your plugins list.' )
					.ppbDialog( "option", "position", { my : "center", at : "center", of : window } )
					.ppbDialog( "open" );
			}
		} );


		var $proAddons = $( '.modules-pro' ).find( '.ppb-addon-card' );

		$proAddons.find( ' a.activate' ).click( function () {
			var $t = $( this );
			$t.closest( '.ppb-addon-card' ).addClass( 'active' );
			$t.siblings( 'input' ).val( 'active' );
		} );
		$proAddons.find( ' a.deactivate' ).click( function () {
			var $t = $( this );
			$t.closest( '.ppb-addon-card' ).removeClass( 'active' );
			$t.siblings( 'input' ).val( '' );
		} );
	} );
})(jQuery);