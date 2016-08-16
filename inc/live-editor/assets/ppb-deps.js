/**
 * @developer wpdevelopment.me <shramee@wpdvelopment.me>
 */
( function ( $ ) {

	if ( 'function' != typeof panels.addInputFieldEventHandlers )
	panels.addInputFieldEventHandlers = function ( $this ) {

		$( 'html' ).trigger( 'pootlepb_admin_input_field_event_handlers', [$this] );

		$this.find( 'input[data-style-field-type="color"]' ).each( function () {
			$t = $( this );
			var wpPkrContnr = $t.closest( '.wp-picker-container' );
			if ( wpPkrContnr.length == 0 ) {
				$t.wpColorPicker( {
					change: function ( e, ui ) {
						$( this ).change();
					}
				} );
			}
		} );

		/* Removing existing event handlers */
		$this.find( '.upload-button, .unsplash-button, .video-upload-button' ).off( 'click' );

		// Uploading Fields aka media selection
		var ppbFileFrame,
			ppbMP4VideoFrame,
			ppbWebmVidFrame;
		$this.find( '.upload-button' ).on( 'click', function ( event ) {
			event.preventDefault();

			var $textField = $( this ).siblings( 'input[data-style-field-type="upload"]' );

			// If the media frame already exists, reopen it.
			if ( ppbFileFrame ) {
				ppbFileFrame.open();
				return;
			}

			// Create the media frame.
			ppbFileFrame = wp.media.frames.ppbFileFrame = wp.media( {
				title: 'Choose Background Image',
				button: {text: 'Set As Background Image'},
				multiple: false  // Set to true to allow multiple files to be selected
			} );

			// When an image is selected, run a callback.
			ppbFileFrame.on( 'select', function () {
				attachment = ppbFileFrame.state().get( 'selection' ).first().toJSON();
				$textField.val( attachment.url ).change();
			} );

			// Finally, open the modal
			ppbFileFrame.open();
		} );

		$this.find( 'input[data-style-field-type="upload"] ~ input[type="search"]' ).attr( 'style', '' );
		$this.find( 'input[data-style-field-type="upload"]' ).off( 'change' ).change( function() {
			var $t = $( this );
			if ( $t.val() ) {
				$t.show();
				$t.css( 'background-image', 'url("' + $t.val() + '")' );
			} else {
				$t.hide();
			}
		} );
		$this.find( '.unsplash-button' ).attr( 'style', '' ).on( 'click', function ( e ) {
			e.preventDefault();
			var
				$textFields = $( this ).siblings( 'input' ),
				$textField = $textFields.filter( '[type="text"]' ),
				$searchField = $textFields.filter( '[type="search"]' );
			if ( ! $searchField.is(':visible') ) {
				$searchField.show();
				return;
			}
			ShrameeUnsplashImage(
				function ( url ) {
					$textField.val( url ).change();
					$searchField.val('');
				},
				$searchField.val()
			);
		} );

		$this.find( '.video-upload-button' ).on( 'click', function ( event ) {
			event.preventDefault();

			var $textField = $( this ).siblings( 'input' );

			// If the media frame already exists, reopen it.
			if ( ppbMP4VideoFrame ) {
				ppbMP4VideoFrame.open();
				return;
			}

			// Create the media frame.
			ppbMP4VideoFrame = wp.media.frames.ppbMP4VideoFrame = wp.media( {
				title: 'Choose MP4/WEBM Background Video File',
				library: {
					type: 'video'
				},
				button: {
					text: 'Set As Background Video'
				},
				multiple: false
			} );

			// When an image is selected, run a callback.
			ppbMP4VideoFrame.on( 'select', function () {
				// We set multiple to false so only get one image from the uploader
				attachment = ppbMP4VideoFrame.state().get( 'selection' ).first().toJSON();

				// Do something with attachment.id and/or attachment.url here
				$textField.val( attachment.url );
				$textField.change();

			} );

			// Finally, open the modal
			ppbMP4VideoFrame.open();
		} );

		//Updates value for slider controls
		slider_val_update = function ( valu, $t, $f, $spn ) {
			var max = $t.data( 'max' );
			//Update values on slide
			$f.val( valu );

			if ( $t.data( 'show-actual-val' ) ) {
				$spn.text( valu + $t.data( 'unit' ) );
			} else {
				$spn.text( Math.round( valu / max * 100 ) + '%' );
			}
		};

		//Slider controls init
		$this.find( '.ppb-slider' ).each( function () {
			var $t = $( this ),
				$f = $t.siblings( 'input' ),
				$spn = $( this ).siblings( '.slider-val' ),
				max = $t.data( 'max' ),
				valu;

			//Update span
			if ( '' != $f.val() ) {
				valu = $f.val();
			} else {
				valu = $t.data( 'default' );
			}
			slider_val_update( valu, $t, $f, $spn );

			//Init slider
			$t.slider( {
				min: $t.data( 'min' ),
				max: max,
				step: $t.data( 'step' ),
				value: valu,
				slide: function ( e, ui ) {
					slider_val_update( ui.value, $t, $f, $spn );
				},
				change: function ( e, ui ) {
					slider_val_update( ui.value, $t, $f, $spn );
				}
			} );
		} );

		$this.find( '.ppb-chzn-multi' ).each( function () {
			var $t = $( this );
			$t
				.chosen( {
					width: '250px',
					placeholder_text_multiple: $t.attr( 'placeholder' )
				} );
		} )
	};

	if ( 'function' != typeof panels.bgVideoMobImgSet )
	panels.bgVideoMobImgSet = function () {

		$( '#row-bg-video-notice' ).remove();
		$( '#pp-pb-bg_mobile_image' ).attr( 'style', '' );

		if ( '.bg_video' == $( '#pp-pb-background_toggle' ).val() && $( '#pp-pb-bg_video' ).val() ) {

			if ( '' == $( '#pp-pb-bg_mobile_image' ).val().replace( ' ', '' ) ) {

				var notice = $( '<div/>' ).attr( 'id', 'row-bg-video-notice' ).addClass( 'ppb-alert' )
				                          .html( '<span class="dashicons dashicons-no"></span>Please select an image to display instead of the background video for mobile devices.' )
				$( '.bg_section.bg_video' ).append( notice );
				$( '#pp-pb-bg_mobile_image' ).css( {backgroundColor: '#fcc', borderColor: '#f88'} );

				return false;
			}
		}

		return true;
	};

	panels.getStylesFromFields = function ( $styleForm, styleData ) {
		// from values in dialog fields, set style data into hidden fields
		styleData = styleData ? styleData : {};
		$styleForm.find( '[dialog-field]' ).each( function () {
			var $t = $( this ),
				key = $t.attr( 'dialog-field' ),
				type = $t.attr( 'type' );

			if ( type == 'checkbox' ) {
				styleData[key] = '';
				if ( $t.prop( 'checked' ) ) {
					styleData[key] = $t.val();
				}
			} else if ( type == 'radio' ) {
				if ( $t.prop( 'checked' ) ) {
					styleData[key] = $t.val();
				}
			} else {
				styleData[key] = $t.val();
			}

		} );

		return styleData;
	};

	if ( 'function' != typeof panels.setStylesToFields )
	panels.setStylesToFields = function ($styleForm, styleData) {
		// by default, set checkbox to unchecked,
		$styleForm.find('input[type=checkbox]')
		          .prop('checked', false)
		          .change();
		$styleForm.find('input[type=text], input[type=hidden], select, input[type=number], textarea')
		          .val('')
		          .change()
		          .trigger('chosen:updated');

		$styleForm.find(':radio[value=""]')
		          .prop('checked', true)
		          .change();

		// from style data in hidden field, set the widget style dialog fields with data
		for (var key in styleData) {
			if (styleData.hasOwnProperty(key)) {

				var $field = $styleForm.find('[dialog-field="' + key + '"]');

				if ($field.attr('data-style-field-type') == "color" ) {
					$field.wpColorPicker('color', styleData[key]);
				} else if ($field.attr('data-style-field-type') == "slider" ) {
					$field.siblings('.ppb-slider').slider('value',styleData[key]);
				} else if ($field.attr('data-style-field-type') == "radio" ) {
					$field.filter('[value="' + styleData[key] + '"]').prop('checked', true);
				} else if ($field.attr('data-style-field-type') == "checkbox") {
					if (styleData[key] == $field.val()) {
						$field.prop('checked', true);
					} else {
						$field.prop('checked', false);
					}
				} else {
					$field.val(styleData[key]);
				}
				$field.change().trigger('chosen:updated');

			}
		}

	};

} )( jQuery );