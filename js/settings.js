/**
 * Created by shramee on 13/5/15.
 * @since 0.1.0
 */
(function ($) {

    $(document).ready(function () {

        var dialog = $('<div />')
            .attr('id', 'ppb-hrad-unin-dialog')
            .appendTo($('body'))
            .dialog({
                autoOpen: false,
                modal: false,
                title: "Are you sure",
                width: 500,
                buttons: [
                    {
                        text: 'Cancel',
                        click: function () {
                            $('#pootlepb-hard-uninstall').prop('checked', false);
                            dialog.dialog('close');
                        }
                    },
                    {
                        text: 'Yes, I\'m sure',
                        click: function () {
                            dialog.dialog('close');
                        }
                    }
                ]
            })
        $('#pootlepb-hard-uninstall').change(function(){
            var $t = $(this);

            if ($t.prop('checked')) {
                dialog
                    .html('Are you really sure you want to enable this? When enabled this will delete ALL settings and layouts for page builder pages when you delete page builder from your plugins list.')
                    .dialog("option", "position", {my: "center", at: "center", of: window})
                    .dialog("open");

                if (!sure) {

                }
            }
        });

        //$('.colour').wpColorPicker();
    });
})(jQuery);
