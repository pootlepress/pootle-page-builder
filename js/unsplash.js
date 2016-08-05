/**
 * Shramee unsplash image select
 */
function ShrameeUnsplashImageDialog( $ ) {
	var $dlg = $( '#shramee-unsplash-wrap,#shramee-unsplash-overlay' );
	if ( ! $dlg.length ) {
		var $dlog = $( '<div/>' );
		$dlog
			.attr( 'id', 'shramee-unsplash-wrap' )
			.css( 'display', 'none' )
			.append(
				$( '<div/>' )
					.attr( 'id', 'shramee-unsplash-search-field-wrap' )
					.append(
						$( '<input>' )
							.attr( { placeholder: 'Search images', id: 'shramee-unsplash-search-field' } )
					)
					.append(
						$( '<a/>' )
							.attr( { href: '#', id: 'shramee-unsplash-search' } )
							.html( '<span class="dashicons dashicons-search"></span>Search images' )
					)
			)
			.append(
				$( '<div/>' ).attr( 'id', 'shramee-unsplash-images' )
			);
		$( 'body' )
			.append( '<style>#shramee-unsplash-overlay,#shramee-unsplash-wrap{position:fixed;top:0;right:0;bottom:0;left:0;z-index:999997;background: rgba( 0,0,0,0.25 )}#shramee-unsplash-wrap{text-align:center;margin:auto; top: 25px;left: 25px;right: 25px;bottom: 25px;background:#fff;padding:25px;overflow:auto}#shramee-unsplash-wrap:before{content:"Search for images on unsplash";display:block;margin:-25px -25px 25px;padding:7px;background:#ddd;text-align:left}#shramee-unsplash-wrap *{vertical-align:middle}#shramee-unsplash-search-field{width:50%;min-width:340px;padding: 7px 43px 7px 11px;-webkit-border-radius:3px;border-radius:3px;border:1px solid #aaa;box-shadow:0 0 2px 0px rgba(0,0,0,0.25);-webkit-box-shadow:0 0 2px 0px rgba(0,0,0,0.25);}#shramee-unsplash-search{font-size:0;position:relative;left:-43px;color:#333;display:inline-block;padding:11px}#shramee-unsplash-wrap .image{height:250px;width:250px;display:inline-block;cursor:pointer;background: center/cover;}#shramee-unsplash-images{margin:25px auto}#shramee-unsplash-images .image{margin:2px}</style>' )
			.append( $( '<div/>' ).attr( 'id', 'shramee-unsplash-overlay' ) )
			.append( $dlog );
		$dlg = $( '#shramee-unsplash-wrap,#shramee-unsplash-overlay' );

		$( '#shramee-unsplash-overlay' ).click( function ( e ) {
			$dlg.hide();
		} );

			var
				$btn  = $( '#shramee-unsplash-search' ),
				$f    = $( '#shramee-unsplash-search-field' ),
				$imgs = $( '#shramee-unsplash-images' );
		$f.off( 'focus' );
		$f.keypress(function(e) {
			if(e.which == 13) {
				$btn.click();
			}
		});
		$btn.click( function ( e ) {
			e.preventDefault();
			var
				url = 'https://api.unsplash.com/photos/search?client_id=6e7fb4dfb5dfbdcd500ce33d8a6fed84ea535704a33aa57efd9e60b9a032a5bb&per_page=25&query=',
				qry = $f.val().replace( ' ', ',' );

			$imgs.html( '<h4>Searching images...</h4>' );
			$.ajax( url + qry )
				.done( function ( json ) {
					$imgs.html( '' );
					if ( ! json || ! json.length ) {
						$imgs.html( '<p>Couldn\'t find any images matching <b>' + qry + '</b>...</p>' );
					}
					$.each( json, function ( i, v ) {
						$imgs
							.append(
								$( '<div/>' )
									.addClass( 'image' )
									.css( 'background-image', 'url(' + v.urls.small + ')' )
									.data( 'image', v.urls.regular )
									.data( 'ratio', v.height / v.width )
							);
					} );
				} );
		} );
		$imgs.click( function ( e ) {
			var url = $( e.target ).data( 'image' );
			$imgs.html( '<h4>Type in search keywords above...</h4>' );
			$dlg.hide();
			if ( 'function' == typeof ShrameeUnsplashImageDialog.callback ) {
				ShrameeUnsplashImageDialog.callback( url );
				ShrameeUnsplashImageDialog.callback = null;
			}
		} );
	}
	return $dlg;
}
function ShrameeUnsplashImage( callback, keywords ){
	var $   = jQuery;

	ShrameeUnsplashImageDialog.callback = callback;
	ShrameeUnsplashImageDialog( $ ).show();
	var $search = $( '#shramee-unsplash-search-field-wrap' ).show();

	if ( typeof keywords == 'string' ) {
		$search.hide();
		$( '#shramee-unsplash-search-field' ).val( keywords );
		$( '#shramee-unsplash-search' ).click();
	}

}
function cancelBubble(e) {
	var evt = e ? e:window.event;
	if (evt.stopPropagation)    evt.stopPropagation();
	if (evt.cancelBubble!=null) evt.cancelBubble = true;
}