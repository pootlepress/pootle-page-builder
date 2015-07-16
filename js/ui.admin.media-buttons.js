(function ($) {

    panels.setCoolStyleButtons = function ( $this ) {

        /* Removing existing event handlers */
        $this.find('.upload-button').off('click');
        $this.find('.video-upload-button').off('click');

        // Uploading Fields aka media selection
        var ppbFileFrame,
            ppbMP4VideoFrame,
            ppbWebmVidFrame;
        $this.find('.upload-button').on('click', function (event) {
            event.preventDefault();

            $textField = $(this).siblings('input');

            // If the media frame already exists, reopen it.
            if (ppbFileFrame) {
                ppbFileFrame.open();
                return;
            }

            // Create the media frame.
            ppbFileFrame = wp.media.frames.ppbFileFrame = wp.media({
                title: 'Choose Background Image',
                button: {text: 'Set As Background Image'},
                multiple: false  // Set to true to allow multiple files to be selected
            });

            // When an image is selected, run a callback.
            ppbFileFrame.on('select', function () {
                // We set multiple to false so only get one image from the uploader
                attachment = ppbFileFrame.state().get('selection').first().toJSON();

                // Do something with attachment.id and/or attachment.url here
                $textField.val(attachment.url);
                $textField.change();

            });

            // Finally, open the modal
            ppbFileFrame.open();
        });

        $this.find('.video-upload-button').on('click', function (event) {
            event.preventDefault();

            $textField = $(this).siblings('input');

            // If the media frame already exists, reopen it.
            if (ppbMP4VideoFrame) {
                ppbMP4VideoFrame.open();
                return;
            }

            // Create the media frame.
            ppbMP4VideoFrame = wp.media.frames.ppbMP4VideoFrame = wp.media({
                title: 'Choose MP4/WEBM Background Video File',
                library: {
                    type: 'video'
                },
                button: {
                    text: 'Set As Background Video'
                },
                multiple: false
            });

            // When an image is selected, run a callback.
            ppbMP4VideoFrame.on('select', function () {
                // We set multiple to false so only get one image from the uploader
                attachment = ppbMP4VideoFrame.state().get('selection').first().toJSON();

                // Do something with attachment.id and/or attachment.url here
                $textField.val(attachment.url);
                $textField.change();

            });

            // Finally, open the modal
            ppbMP4VideoFrame.open();
        });

        $this.find('.ppb-slider').each(function() {
            var $t = $(this),
                $f = $t.siblings('input'),
                $spn = $(this).siblings('.slider-val');
            $spn.text(($f.val() * 100) + '%');
            $t.slider({
                min: 0,
                max: 1,
                step: 0.05,
                value: $f.val(),
                slide: function (e, ui) {
                    $f.val(ui.value);
                    $spn.text(Math.round(ui.value * 100) + '%');
                }
            });
        });

    };

})(jQuery);
