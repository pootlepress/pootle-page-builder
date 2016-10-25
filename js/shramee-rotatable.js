(function($) {
	index = 0;
	$.fn.rotatable = function ( props ) {
		var $t = this;

		if ( typeof props != 'object' ) {
			props = {};
		}
		var funcs = [ 'start', 'rotate', 'stop', ];
		for( var i = 0; i < funcs.length; i++ ) {
			if ( typeof props[ funcs[ i ] ] != 'function' ) {
				props[ funcs[ i ] ] = function () {};
			}
		}

		if ( typeof props.handle == 'string' ) {
			props.handle = $( props.handle );
		} else if ( props.handle instanceof HTMLElement ) {
			props.handle = $( props.handle );
		}

		if ( ! ( props.handle instanceof jQuery ) || ! props.handle.length ) {
			var handleId = 'rotatable-handle-' + (
					index ++
				);

			// Create handle dynamically
			$t.append(
				$( '<div></div>' )
					.attr( 'id', handleId )
					.css( {
						position: 'absolute',
						bottom: '5px',
						right: '50%',
						height: 10,
						width: 10,
						margin: - 5,
						background: 'orange'
					} )
			);

			props.handle = $( '#' + handleId );
		}

		var center = {
			top: $t.offset().top + $t.height() / 2,
			left: $t.offset().left + $t.width() / 2,
		};


		$t.css( 'position', 'relative' );

		props.handle
		     .addClass( 'rotatable-handle' )
		     .draggable( {
			     opacity: 0.01,
			     revert: true,
			     helper: 'clone',
			     start: function ( e, ui ) {
				     e.target = $t;
				     props.start( e );
			     },
			     stop: function ( e, jqui ) {
				     var
					     y = jqui.offset.left - center.left,
					     x = jqui.offset.top - center.top,
					     r = Math.sqrt( x * x + y * y ),
					     angle = Math.asin( y / r ),
					     ui = {};
				     angle *= - 1; // HTML rotate is opposite of math coordinates based anticlockwise rotation
				     ui.rad = angle;
				     angle *= 180 / Math.PI; // Convert to degrees from radians

				     if ( x < 0 ) { // For x coord < 0 actual angle is 180 - angle
					     angle = 180 - angle;
					     ui.rad = Math.PI - ui.rad;
				     }
				     angle = Math.round( angle );
				     ui.deg = angle;
				     ui.css = 'rotate(' + angle + 'deg)';
				     e.target = $t;
				     props.stop( e, ui );
			     },
			     drag: function ( e, jqui ) {
				     var
					     y = jqui.offset.left - center.left,
					     x = jqui.offset.top - center.top,
					     r = Math.sqrt( x * x + y * y ),
					     angle = Math.asin( y / r ),
					     ui = {};
				     angle *= - 1; // HTML rotate is opposite of math coordinates based anticlockwise rotation
				     ui.rad = angle;
				     angle *= 180 / Math.PI; // Convert to degrees from radians

				     if ( x < 0 ) { // For x coord < 0 actual angle is 180 - angle
					     angle = 180 - angle;
					     ui.rad = Math.PI - ui.rad;
				     }
				     //console.log(x, y, r, angle);
				     ui.deg = angle;
				     angle = Math.round( angle );
				     ui.css = 'rotate(' + angle + 'deg)';

				     $t.css( {
					     '-webkit-transform': ui.css,
					     'transform': ui.css
				     } );
				     e.target = $t;
				     props.rotate( e, ui );
			     }
		     } );
		return this;
	};
	$( '.rotatable' ).each( function () {
		$( this ).rotatable();
	} );
} )( jQuery );