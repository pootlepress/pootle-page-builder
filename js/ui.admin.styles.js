/**
 * Handles row styling.
 *
 * @copyright Greg Priday 2014
 * @license GPL 2.0 http://www.gnu.org/licenses/gpl-2.0.html
 * @since 0.1.0
 */

jQuery(function ($) {
    // Create the dialog for setting up the style
    var buttons = {};
    buttons[panels.i10n.buttons.done] = function () {
        if( ! panels.bgVideoMobImgSet() ) {
            $('[href="#pootle-background-tab"]').click();
            return;
        }
        $('#grid-styles-dialog').ppbDialog('close');
    };


    $gridStylesDialog = $('#grid-styles-dialog');
    $gridStylesDialog.data('html', $('#grid-styles-dialog').html());
    $gridStylesDialog
        .show()
        .ppbDialog({
            dialogClass: 'panels-admin-dialog ppb-cool-panel-container',
            autoOpen: false,
            draggable: false,
            resizable: false,
            title: $('#grid-styles-dialog').attr('data-title'),
            height: 500,
            width: 700,
            open: function () {
                var $t = $(this);

                $t.find('.ppb-cool-panel-wrap').ppbTabs({
                    active: 0
                });

                $t.find('.field_row_height input').prop('disabled', false);
                if (0 < $('#grid-styles-dialog').data('container').find('.panel').length) {
                    $t
                        .find('.field_row_height input').val('')
                        .prop('disabled', true);
                }

                panels.addInputFieldEventHandlers($('#grid-styles-dialog'));

                var $bgToggle = $t.find('[data-style-field=background_toggle]'),
                    $bgVidFlds = $t.find('[data-style-field=bg_video]');
                panels.rowBgToggle();
                $bgToggle.on('change', panels.rowBgToggle);
                $bgVidFlds.on('change', panels.BGVidFld);
            },
            close: function () {
                var $bgToggle = $t.find('[data-style-field=background_toggle]'),
                    $bgVidFlds = $t.find('[data-style-field=bg_video]');
                $bgToggle.off('change', panels.rowBgToggle);
                $bgVidFlds.off('change', panels.BGVidMP4);

                // Copy the dialog values back to the container style value fields
                var container = $('#grid-styles-dialog').data('container');

                bg_color = $('#grid-styles-dialog [data-style-field=background]').val();

                container.css( 'border-left-color', bg_color );

                container.removeClass( 'hide-row-enabled' );
                if ( $('#grid-styles-dialog [data-style-field=hide_row]').prop('checked') ) {
                    container.addClass( 'hide-row-enabled' );
                }

                $('#grid-styles-dialog [data-style-field]').each(function () {
                    var $$ = $(this);
                    var cf = container.find('[data-style-field="' + $$.data('style-field') + '"]');

                    switch ($$.data('style-field-type')) {
                        case 'checkbox':
                            cf.val($$.is(':checked') ? 'true' : '');
                            break;
                        default :
                            cf.val($$.val());
                            break;
                    }
                });
            },
            buttons: buttons
        })
    ;

    panels.loadStyleValues = function (container) {
        var $gridStylesDialog = $('#grid-styles-dialog');
        $gridStylesDialog
            .data('container', container)
            .html($gridStylesDialog.data('html'));

        // Copy the values of the hidden fields in the container over to the dialog.
        container.find("[data-style-field]").each(function () {
            var $$ = $(this);

            // Save the dialog field
            var df = $('#grid-styles-dialog [data-style-field="' + $$.data('style-field') + '"]');
            switch (df.data('style-field-type')) {
                case 'checkbox':
                    df.attr('checked', $$.val() ? true : false);
                    break;
                case 'slider':
                    try {
                        df.siblings('.ppb-slider').slider('value', $$.val());
                    } catch(err) {
                        df.val($$.val());
                    }
                    break;
                default :
                    df.val($$.val());
                    break;
            }
        });

        $gridStylesDialog.ppbDialog('open');

        $('html').trigger( 'pootlepb_admin_row_styling_panel_done', [ $gridStylesDialog ] );

        // Now set up all the fields
        $('#grid-styles-dialog [data-style-field-type="color"]')
            .wpColorPicker()
            .closest('p').find('a').click(function () {
                $('#grid-styles-dialog').ppbDialog("option", "position", "center");
            });
    }

    panels.rowBgToggle = function () {
        var $dialog = $('#grid-styles-dialog'),
            $t = $dialog.find('[data-style-field=background_toggle]');

        $('.bg_section').hide();
        $($t.val()).show();
    };

    panels.BGVidFld = function () {

        var $t = $(this);

        if ('' == $.trim($t.val())) {
            return;
        }

        format = $t.val().substr($t.val().lastIndexOf('.') + 1);

        if ('mp4' != format && 'webm' != format) {
            panels.BGVidFormatWrong($t);
        } else {
            $t.css('background', '')
        }
    };

    panels.BGVidFormatWrong = function ($t) {

        $("<div title='Please Use a .mp4 or .webm video'>This field supports .mp4 and .webm formats only.</div>").ppbDialog({
            resizable: false,
            width: 400,
            buttons: {
                Ok: function () {
                    $(this).ppbDialog("close");
                }
            }
        });
        $t.val('');
        $t.css('background', '#ffbbb9')
    };
});