/**
 * Intercepts the standard WordPress gallery insert and edit.
 *
 * @copyright Greg Priday 2013
 * @license GPL 2.0 http://www.gnu.org/licenses/gpl-2.0.html
 * @since 0.1.0
 */
jQuery(function ($) {
    if (typeof wp == 'undefined' || !wp.media || !wp.media.editor) return;

    var originalInsert = wp.media.editor.insert;

    wp.media.editor.insert = function (h) {

        // Check that panels tab is active and that no dialogs are open.
        if (!$('#wp-content-wrap').hasClass('panels-active')) return originalInsert(h);
        if ($('.panel-dialog:visible').length > 0) return originalInsert(h);

        if (h.indexOf('[gallery') !== -1) {
            // Get the IDs of the gallery
            var attachments = wp.media.gallery.attachments(wp.shortcode.next('gallery', h).shortcode);
            var ids = attachments.models.map(function (e) {
                return e.id
            });

            // Create a new gallery panel
            var panel = panelsCreatePanel('SiteOrigin_Panels_Widgets_Gallery', {
                'ids': ids.join(',')
            });

            // The panel couldn't be created. Possible the widgets gallery isn't being used.
            if (panel == null) originalInsert(h);
            else panels.addPanel(panel, null, null, true);

            return;
        }
        else if (h.indexOf('<a ') !== -1 || h.indexOf('<img ') !== -1) {
            // Figure out how we can add this to panels
            var $el = $(h);

            var panel;
            if ($el.prop("tagName") == 'A' && $el.children().eq(0).prop('tagName') == 'IMG') {
                // This is an image with a link
                panel = panelsCreatePanel('SiteOrigin_Panels_Widgets_Image', {
                    'href': $el.attr('href'),
                    'src': $el.children().eq(0).attr('src')
                });
            }
            else if ($el.prop("tagName") == 'IMG') {
                // This is just an image tag
                panel = panelsCreatePanel('SiteOrigin_Panels_Widgets_Image', {
                    'src': $el.attr('src')
                });
            }
            else if ($el.prop('tagName') == 'A' && ($el.attr('href').indexOf('.mp4') !== -1 || $el.attr('href').indexOf('.avi') !== -1)) {
                panel = panelsCreatePanel('SiteOrigin_Panels_Widgets_Video', {
                    'url': $el.attr('href')
                });
            }

            // The panel couldn't be created. Possible the widgets gallery isn't being used.
            if (panel == null) originalInsert(h);
            else panels.addPanel(panel, null, null, true);

            return;
        }
        else {
            // Create a new gallery panel
            var panel = panelsCreatePanel('WP_Widget_Text', {
                'text': h
            });

            // The panel couldn't be created. Possible the widgets gallery isn't being used.
            if (panel == null) originalInsert(h);
            else panels.addPanel(panel, null, null, true);
        }

        // Incase we've added any new panels
        originalInsert(h);
    }

});

jQuery(function ($) {
    // When the user clicks on the select button, we need to display the gallery editing
    $('body').on({
        click: function (event) {
            // Make sure the media gallery API exists
            if (typeof wp === 'undefined' || !wp.media || !wp.media.gallery) return;
            event.preventDefault();

            // Activate the media editor
            var $$ = $(this);

            var dialog = $('.panels-admin-dialog:visible');

            var val = dialog.find('*[name$="[ids]"]').val();
            if (val.indexOf('{demo') === 0 || val.indexOf('{default') === 0) val = '-'; // This removes the demo or default content
            if (val == '' && $('#post_ID').val() == null) val = '-';

            var frame = wp.media.gallery.edit('[gallery ids="' + val + '"]');

            // When the gallery-edit state is updated, copy the attachment ids across
            frame.state('gallery-edit').on('update', function (selection) {
                var ids = selection.models.map(function (e) {
                    return e.id
                });

                dialog.find('input[name$="[ids]"]').val(ids.join(','));
            });

            frame.on('escape', function () {
                // Reopen the dialog
                dialog.find('.ui-dialog-content').ppbDialog('open');
            });

            return false;
        }
    }, '.so-gallery-widget-select-attachments');
})