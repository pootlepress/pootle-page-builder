/**
 * Created by shramee on 19/07/16.
 */
var PPBfrontendUndo = function () {
	var $  = jQuery,
	    $b = $( 'body' ),
	    t  = this;
	$( '<div id="ppb-undo-notice" class="ppb-blue-notice"><span class="ppb-rotate dashicons dashicons-admin-generic"></span><h2></h2></div>' ).appendTo( $b );

	var $notice = $( '#ppb-undo-notice' ),
	    $h2     = $notice.find( 'h2' );

	t.now    = -1;
	t.data   = [];
	t.action = '';
	t.func = {
		msg : function( msg ) {
			$h2.html( msg );
			$notice.show();
		},
		saveState : function () {
			if ( t.data.length > 4 ) {
				t.data.shift();
				t.now--;
			}
			t.data.splice( t.now, 5 );
			t.now ++;
			t.data.push( jQuery.extend( true, {}, ppbData ) );
			t.lastAction = 'saveState';
		},
		undo      : function () {
			if ( t.lastAction != 'undo' ) {
				t.func.saveState();
			}
			if ( t.now > 0 ) {
				t.now--;
				ppbData = t.data[ t.now ];
				prevu.sync( t.func.sync );
				t.func.msg( 'Undoing...' );
				t.lastAction = 'undo';
			}
		},
		redo      : function () {
			if ( ( t.data.length + 1 ) > t.now ) {
				t.now ++;
				ppbData = t.data[ t.now ];
				prevu.sync( t.func.sync );
				t.func.msg( 'Redoing...' );
				t.lastAction = 'redo';
			}
		},
		sync      : function ( $r ) {
			var $ppb = $r.find( '#pootle-page-builder' ).addClass( 'updated' );

			$( '#pootle-page-builder' ).html( $ppb.html() );

			$ppb = $( '#pootle-page-builder.updated' );
			$( 'html' ).trigger( 'pootlepb_le_content_updated', [$ppb] );
			$ppb.removeClass( 'updated' );
			$ppb.sortable( prevu.rowsSortable );
			$notice.hide();
			$( '.panel-grid' ).prevuRowInit();

		}
	};

	$b.click( function ( e ) {
		if ( '#ppb-live-undo' == $( e.target ).attr( 'href' ) ) {
			e.preventDefault();
			t.func.undo();
		} else if ( '#ppb-live-redo' == $( e.target ).attr( 'href' ) ) {
			e.preventDefault();
			t.func.redo();
		}
	} );
};