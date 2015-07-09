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
        $('#grid-styles-dialog').dialog('close');
    };


    $gridStylesDialog = $('#grid-styles-dialog');
    $gridStylesDialog.data('html', $('#grid-styles-dialog').html());
    $gridStylesDialog
        .show()
        .dialog({
            dialogClass: 'panels-admin-dialog ppb-cool-panel-container',
            autoOpen: false,
            modal: false, // Disable modal so we don't mess with media editor. We'll create our own overlay.
            draggable: false,
            resizable: false,
            title: $('#grid-styles-dialog').attr('data-title'),
            height: 500,
            width: 700,
            open: function () {
                $t = $(this);

                $t.find('.ppb-cool-panel-wrap').tabs({
                    active: 0
                });

                $t.find('.field_row_height input').prop('disabled', false);
                if (0 < $('#grid-styles-dialog').data('container').find('.panel').length) {
                    $t
                        .find('.field_row_height input').val('')
                        .prop('disabled', true);
                }

                var overlay = $('<div class="ppb-panels ui-widget-overlay ui-widget-overlay ui-front"></div>').css('z-index', 80001);
                $t.data('overlay', overlay).closest('.ui-dialog').before(overlay);

                window.setRowOptionUploadButton($('#grid-styles-dialog'));

                var $bgToggle = $t.find('[data-style-field=background_toggle]'),
                    $bgVidFlds = $t.find('[data-style-field=bg_video]');
                panels.rowBgToggle();
                $bgToggle.on('change', panels.rowBgToggle);
                $bgVidFlds.on('change', panels.BGVidFld);
            },
            close: function () {
                $(this).data('overlay').remove();

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
        $('#grid-styles-dialog')
            .data('container', container)
            .html($('#grid-styles-dialog').data('html'));

        // Copy the values of the hidden fields in the container over to the dialog.
        container.find("[data-style-field]").each(function () {
            var $$ = $(this);

            // Save the dialog field
            var df = $('#grid-styles-dialog [data-style-field="' + $$.data('style-field') + '"]');
            switch (df.data('style-field-type')) {
                case 'checkbox':
                    df.attr('checked', $$.val() ? true : false);
                    break;
                default :
                    df.val($$.val());
                    break;
            }
        });

        $('#grid-styles-dialog').dialog('open');

        // Now set up all the fields
        $('#grid-styles-dialog [data-style-field-type="color"]')
            .wpColorPicker()
            .closest('p').find('a').click(function () {
                $('#grid-styles-dialog').dialog("option", "position", "center");
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

        $("<div title='Please Use a .mp4 or .webm video'>This field supports .mp4 and .webm formats only.</div>").dialog({
            modal: true,
            resizable: false,
            width: 400,
            buttons: {
                Ok: function () {
                    $(this).dialog("close");
                }
            }
        });
        $t.val('');
        $t.css('background', '#ffbbb9')
    };

    panels.rowVisualStylesInit
});