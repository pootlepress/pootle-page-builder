/**
 * Shramee unsplash image select
 */
function ShrameeUnsplashImageDialog( $ ) {
	var $dlg = $( '#ppb-unsplash-wrap' );
	if ( ! $dlg.length ) {
		var $dlog = $( '<div/>' );
		$dlog
			.attr( 'id', 'ppb-unsplash-wrap' )
			.css( 'display', 'none' )
			.append(
				$( '<input>' )
					.attr( { placeholder: 'Search images', id: 'shramee-unsplash-search-field' } )
			)
			.append(
				$( '<a/>' )
					.attr( { href: '#', id: 'shramee-unsplash-search' } )
					.html( '<span class="dashicons dashicons-search"></span>Search images' )
			)
			.append(
				$( '<div/>' ).attr( 'id', 'shramee-unsplash-images' )
			);
		$( 'body' )
			.append( $( '<style/>' ).html( '#ppb-unsplash-tab{text-align:center;} #ppb-unsplash-tab *{vertical-align:middle;} #ppb-unsplash-search-field{width:50%;min-width:340px;} #ppb-unsplash-search{font-size:0;position:relative;left:-43px;color:#333;display:inline-block;padding:7px 9px;} #ppb-unsplash-tab .image{height:250px;width:250px;display:inline-block;cursor:pointer;} #ppb-unsplash-images{margin:25px auto;} #ppb-unsplash-images .image{margin:2px;}' ) )
			.append( $dlog );
		$dlg = $( '#ppb-unsplash-wrap' );

		var $btn  = $( '#ppb-unsplash-search' ),
		    $f    = $( '#ppb-unsplash-search-field' ),
		    $imgs = $( '#ppb-unsplash-images' );
		$btn.click( function ( e ) {
			e.preventDefault();
			var
				url = 'https://api.unsplash.com/photos/search?client_id=6e7fb4dfb5dfbdcd500ce33d8a6fed84ea535704a33aa57efd9e60b9a032a5bb&per_page=25&query=',
				qry = $f.val().replace( ' ', ',' );

			$imgs.html( '' );
			$.ajax( url + qry )
				.done( function ( json ) {
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
			ShrameeUnsplashImage.callback( url );
			$dlg.hide();
		} );
	}
	return $dlg;
}
function ShrameeUnsplashImage( callback ){
	var $   = jQuery,
	    $wr = ShrameeUnsplashImageDialog( $ ).show();
	this.callback = callback;
}