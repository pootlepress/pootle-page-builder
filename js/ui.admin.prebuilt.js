/**
 * Handles pre-built Panel layouts.
 *
 * @copyright Greg Priday 2013
 * @license GPL 2.0 http://www.gnu.org/licenses/gpl-2.0.html
 * @since 0.1.0
 */

jQuery(function ($) {
    $('#grid-prebuilt-dialog').show().ppbDialog({
        dialogClass: 'panels-admin-dialog',
        autoOpen: false,
        resizable: false,
        draggable: false,
        title: $('#grid-prebuilt-dialog').attr('data-title'),
        minWidth: 600,
        height: 350,
        buttons: [
            {
                text: panels.i10n.buttons.insert,
                click: function () {
                    var dialog = $(this).closest('.ui-dialog');
                    if (dialog.hasClass('panels-ajax-loading')) return;
                    dialog.addClass('panels-ajax-loading');

                    var s = $('#grid-prebuilt-input').find(':selected');
                    if (s.attr('data-layout-id') == null) {
                        $('#grid-prebuilt-dialog').ppbDialog('close');
                        return;
                    }

                    $.get(ajaxurl, {action: 'so_panels_prebuilt', layout: s.attr('data-layout-id')}, function (data) {
                        dialog.removeClass('panels-ajax-loading');

                        if (typeof data.name != 'undefined') {
                            if (confirm(panels.i10n.messages.confirmLayout)) {
                                // Clear the grids and load the prebuilt layout
                                panels.clearGrids();
                                panels.loadPanels(data);
                                $('#grid-prebuilt-dialog').ppbDialog('close');
                            }
                        }
                    });

                }
            }
        ]
    });

    // Turn the dropdown into a chosen selector
    $('#grid-prebuilt-dialog').find('select').chosen({
        search_contains: true,
        placeholder_text: $('#grid-prebuilt-dialog').find('select').attr('placeholder')
    });

    // Button for adding prebuilt layouts
    $('#add-to-pb-panel .prebuilt-set').click(function () {
            $('#grid-prebuilt-dialog').ppbDialog('open');
            return false;
        });
});