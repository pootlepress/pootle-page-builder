/**
 * Created by shramee <shramee.srivastav@gmail.com> on 4/12/15.
 */
jQuery( function ( $ ) {
	var ppbPhoto = {};
	ppbPhoto.tab = $( '#pootle-ppb-photo-addon-tab' );
	ppbPhoto.source = $( '.content-block-ppb-photo-addon_source_type' );
	ppbPhoto.show = $( '.content-block-ppb-photo-addon_show' );
	ppbPhoto.imgs = $( '[dialog-field="ppb-photo-addon_source_data"]' );
	ppbPhoto.imgShow = $( '.photo-images' );
	ppbPhoto.tax = $( '.content-block-ppb-photo-addon_source_taxes' );
	/**
	 * Set the title of the panel
	 * @since 0.1.0
	 */
	var $html = $( 'html' );

	$html.on('pootlepb_admin_content_block_title', function (e, $t, data) {

		if( typeof data == 'undefined' ) {
			return;
		}
		if( typeof data.info != 'undefined' ) {
			if ( data.info.style['ppb-photo-addon_show'] ) {
				$t.find('h4').html('Photos - ' + data.info.style['ppb-photo-addon_show'] );
			}
		} else if( $t.data('dialog') ) {
			var $d = $t.data('dialog');
			if ( $d.find('.content-block-ppb-photo-addon_show').is(':checked') ) {
				$t.find('h4').html('Photos - ' + $d.find('.content-block-ppb-photo-addon_show' ).val() );
			}
		}
	});

	$html.on( 'pootlepb_admin_input_field_event_handlers', function ( e, $t ) {
		var $slImg = $t.find( '.photo-select-images' ),
			$sUImg = $t.find( '.photo-select-unsplash' );
		$slImg.off( 'click' );
		$slImg.click( ppbPhoto.selectImg );
		$sUImg.off( 'click' );
		$sUImg.click( ppbPhoto.searchUnplash );
		ppbPhoto.tab.find( '.content-block-ppb-photo-addon_gallery_link' ).val( 'lightbox' );
	} );

	// Create the media frame.
	ppbPhoto.frame = wp.media.frames.ppbPhotoFrame = wp.media( {
		title : 'Choose Photos',
		button : { text : 'Done' },
		multiple : true
	} );
	// When an image is selected, run a callback.
	ppbPhoto.frame.on( 'select', function () {
		var attachment = ppbPhoto.frame.state().get( 'selection' ).toJSON();

		//Get all selected images url in an object
		$.each( attachment, function ( k, v ) {
			v = v.url;
			ppbPhoto.addImgPrevu( v );
		} );

		//Save selected images
		ppbPhoto.imgsFromWrap();
	} );

	ppbPhoto.selectImg = function ( e ) {
		e.preventDefault();
		ppbPhoto.frame.open();
	};

	ppbPhoto.searchUnplash = function ( e ) {
		e.preventDefault();
		ShrameeUnsplashImages(
			function ( urls ) {
				$.each( urls, function ( k, url ) {
					ppbPhoto.addImgPrevu( url );
				} );
				ppbPhoto.imgsFromWrap();
			},
			$(this).siblings( 'input[type="search"]' ).val()
		);
	};

	ppbPhoto.addImgPrevu = function ( url ) {
		var $img = $( '<div class="img" style="background-image:url(' + url + ');" data-img-url="' + url + '">' );

		$img.html( $('<span>').click( ppbPhoto.removeImg ) );

		ppbPhoto.imgShow.append( $img );
	};

	ppbPhoto.imgs.change( function () {
		var $t =$( this );

		if( $t.val() ) {
			var imgs = JSON.parse( $t.val() );

			ppbPhoto.imgShow.html( '' );

			$.each( imgs, function ( k, v ) {
				ppbPhoto.addImgPrevu( v );
			} );
			ppbPhoto.imgShow.sortable( {
				items : '.img',
				stop : ppbPhoto.imgsFromWrap
			} );
			ppbPhoto.imgShow.append();
		} else {
			ppbPhoto.imgShow.html( '' );
		}
	} );

	ppbPhoto.imgsFromWrap = function () {
		ppbPhoto.imgSelected = {};
		var i = 0;
		ppbPhoto.imgShow.children( '.img' ).each( function () {
			$t = $( this );
			ppbPhoto.imgSelected[i] = $t.data( 'img-url' );
			i ++;
		} );
		ppbPhoto.imgs
			.val( JSON.stringify( ppbPhoto.imgSelected ) )
			.change();
	};

	ppbPhoto.show.change( function () {
		var $t = ppbPhoto.show,
			$pre = $t.val();
		if ( ! $pre ) {
			ppbPhoto.tab.find('.field:not(.field-ppb-photo-addon_show)' ).slideUp();
		} else if ( 'slider' == $pre ) {
			ppbPhoto.source.find('option[value="unsplash"]').prop('disabled', false);
			ppbPhoto.tab.find('[class*="field-ppb-photo-addon_gallery_"]' ).slideUp();
			ppbPhoto.tab.find('.field:not([class*="field-ppb-photo-addon_gallery_"])' ).slideDown();
		} else {
			ppbPhoto.source.val('').find('option[value="unsplash"]').prop('disabled', true);
			ppbPhoto.tab.find('[class*="field-ppb-photo-addon_slider_"]' ).slideUp();
			ppbPhoto.tab.find('.field:not([class*="field-ppb-photo-addon_slider_"])' ).slideDown();
		}
	} );

	$html.on('pootlepb_admin_editor_panel_done', function() {
		if ( ! $('.content-block-ppb-photo-addon_source_type' ).val()
		     && ! $('.content-block-ppb-photo-addon_source_data' ).val() ) {
			ppbPhoto.tab.find( '[class*="field-ppb-photo-addon_slider_"] input[type="checkbox"]:not(.content-block-ppb-photo-addon_slider_attr_full_width)' ).prop( 'checked', true );
			ppbPhoto.tab.find( '.content-block-ppb-photo-addon_gallery_link' ).val( 'lightbox' );
		}
	});

	ppbPhoto.source.change( function () {
		var $t = ppbPhoto.source;
		$( '.photo-source' ).slideUp();
		$( '.photo-' + $t.val().replace(/\W+/g, "-") ) .slideDown();
	} );

	ppbPhoto.tax.change( function () {
		var $t = ppbPhoto.tax;
		$( '.photo_tax_terms' ).slideUp();
		$( '.photo_tax_terms_' + $t.val() ) .slideDown();
	} );

	ppbPhoto.removeImg = function () {
		var $t = $( this ).parent();
		console.log( $t.data('img-url') );
		$t.remove();
		ppbPhoto.imgsFromWrap();
	};
} );