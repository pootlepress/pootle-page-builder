/**
 * Created by Shramee on April 02, 2015.
 */
(function ($) {

    $(document).ready(function () {
	    $( '#ppc-tabs-wrapper' ).tabs();
	    //Hiding background customizing options if .ppc-field.background-image .image-upload-path is blank
	    $bg_url = $( '.ppc-field.background-image .image-upload-path, .ppc-field.background-responsive-image .image-upload-path' );
	    $bg_options = $( '.ppc-field.background-repeat, .ppc-field.background-position, .ppc-field.background-attachment' );

	    $bg_options.hide();
	    $bg_url.each( function () {
		    if ( $(this).val() != '' ) {
			    $bg_options.show();
		    }
	    } );

	    $bg_url.change( function () {
		    if ( $bg_url.val() == '' ) {
			    $bg_options.hide();
		    } else {
			    $bg_options.show();
		    }
	    } );


	    //wpColorPicker
	    $( '.ppc-field .color-picker-hex' ).libColorPicker();

	    // Uploading Fields aka media selection
	    var file_frame;
	    $( '.ppc-field .upload-button' ).live( 'click', function ( event ) {
		    event.preventDefault();

		    $textField = $( this ).siblings( 'input' );

		    // If the media frame already exists, reopen it.
		    if ( file_frame ) {
			    file_frame.open();
			    return;
		    }

		    // Create the media frame.
		    file_frame = wp.media.frames.file_frame = wp.media( {
			    title : $( this ).data( 'uploader_title' ),
			    button : {
				    text : $( this ).data( 'uploader_button_text' ),
			    },
			    multiple : false  // Set to true to allow multiple files to be selected
		    } );

		    // When an image is selected, run a callback.
		    file_frame.on( 'select', function () {
			    // We set multiple to false so only get one image from the uploader
			    attachment = file_frame.state().get( 'selection' ).first().toJSON();

			    // Do something with attachment.id and/or attachment.url here
			    $textField.val( attachment.url )
			    $textField.change();

		    } );

		    // Finally, open the modal
		    file_frame.open();
	    } );

	    // Uploading Fields aka media selection
	    var video_file_frame;
	    $( '.ppc-field .video-upload-button' ).on( 'click', function ( event ) {
		    event.preventDefault();

		    $textField = $( this ).siblings( 'input' );

		    // If the media frame already exists, reopen it.
		    if ( video_file_frame ) {
			    video_file_frame.open();
			    return;
		    }

		    // Create the media frame.
		    video_file_frame = wp.media.frames.video_file_frame = wp.media( {
			    title : 'Choose MP4/WEBM Background Video File',
			    library : {
				    type : 'video'
			    },
			    button : {
				    text : 'Set As Background Video'
			    },
			    multiple : false
		    } );

		    // When an image is selected, run a callback.
		    video_file_frame.on( 'select', function () {
			    // We set multiple to false so only get one image from the uploader
			    attachment = video_file_frame.state().get( 'selection' ).first().toJSON();

			    // Do something with attachment.id and/or attachment.url here
			    $textField.val( attachment.url );
			    $textField.change();

		    } );

		    // Finally, open the modal
		    video_file_frame.open();
	    } );

	    $( '#_pootle-page-customizer-Background-background-type' ).change( function () {
		    var $t = $( this ),
			    $all = $( '.field-section-Background' ).not('.background-type');
		    $all.hide();
		    console.log( $t.val() );
		    switch ( $t.val() ) {
			    case 'color':
				    $( '.background-color' ).show();
				    break;
			    case 'image':
				    $( '.background-image' ).show();
				    $( '.ppc-field.background-image .image-upload-path' ).change();
				    break;
			    case 'video':
				    $( '.background-video, .background-responsive-image' ).show();
				    $( '.ppc-field.background-responsive-image .image-upload-path' ).change();
				    break;
		    }

	    } ).change();
    } );
})(jQuery);