/**
 * Main admin control for the Panel interface
 *
 * @copyright Greg Priday 2013
 * @license GPL 2.0 http://www.gnu.org/licenses/gpl-2.0.html
 * @since 0.1.0
 */

(function ($) {

    var newPanelIdInit = 0;
    panels.undoManager = new UndoManager();

    /**
     * A jQuery function to get widget data from a panel object
     * @since 0.1.0
     */
    $.fn.panelsGetPanelData = function () {
        var $$ = $(this);
        var data = {};
        var parts;

        if (typeof $$.data('dialog') != 'undefined' && !$$.data('dialog').hasClass('ui-dialog-content-loading')) {
            $$.data('dialog').find('*[name^=widgets]').not('[data-info-field]').each(function () {
                var $$ = $(this);
                var name = /widgets\[[0-9]+\]\[(.*)\]/.exec($$.attr('name'));

                name = name[1];

                parts = name.split('][');

                parts = parts.map(function (e) {
                    if (!isNaN(parseFloat(e)) && isFinite(e)) return parseInt(e);
                    else return e;
                });

                var previousSub = null;
                var sub = data;
                for (var i = 0; i < parts.length; i++) {
                    if (i == parts.length - 1) {

                        if ($$.attr('type') == 'checkbox') {

                            // for multi select checkboxes,
                            // for example, categories in post slider in WooSlider widget
                            if (parts[i] == '') {

                                if (previousSub != null && i > 0) {
                                    if (!$.isArray(previousSub[parts[i - 1]])) {
                                        previousSub[parts[i - 1]] = [];
                                    }
                                    if ($$.is(':checked')) previousSub[parts[i - 1]].push($$.val());

                                } else {
                                    if ($$.is(':checked')) sub[parts[i]] = true;
                                }


                            } else {
                                if ($$.is(':checked')) {
                                    sub[parts[i]] = $$.val() != '' ? $$.val() : true;
                                } else {
                                    // if it is text widget check box, handle differently, since this widget only checks
                                    // for "isset" of the $instance['filter']
                                    if (typeof $$.attr('id') != 'undefined' && $$.attr('id').indexOf('widget-text-') == 0) {
                                        // do nothing
                                    } else {
                                        // added this line of code for Pootle Post Loop Widget checkbox
                                        sub[parts[i]] = $$.val() == '1' ? '0' : false;
                                    }

                                }
                            }
                        } else {
                            sub[parts[i]] = $$.val();
                        }

                    }
                    else {
                        if (typeof sub[parts[i]] == 'undefined') {
                            sub[parts[i]] = {};
                        }
                        previousSub = sub;
                        sub = sub[parts[i]];
                    }
                }
            });

        }
        else if ($$.find('input[name$="[data]"]').val() == '') {
            data = {};
        }
        else {
            data = JSON.parse($$.find('input[name$="[data]"]').val());
        }

        return data;
    };

    /**
     * Create and return a new panel object
     *
     * @param type
     * @param data
     *
     * @return {*}
     * @since 0.1.0
     */
    panelsCreatePanel = function (type, data) {

        var newPanelId = newPanelIdInit++;

        var dialogWrapper = $(this);

        // Hide the undo message
        $('#panels-undo-message').fadeOut(function () {
            $(this).remove()
        });
        var panel = $('<div class="panel new-panel"><div class="panel-wrapper"><div class="title"><h4></h4><span class="actions"></span></div></div></div>');

        window.activeDialog = undefined;

        // normalize data.info.style to be object
        var widgetStyleJson = "{}";
        if (typeof data != 'undefined') {
            if (typeof data.info == 'undefined') {
                data.info = {};
            }
            if (typeof data.info.style == 'string') {
                widgetStyleJson = data.info.style;

                data.info.style = JSON.parse(data.info.style);

            } else if (typeof data.info.style == 'object') {
                widgetStyleJson = JSON.stringify(data.info.style);
            } else {
                widgetStyleJson = "{}";
            }
        }

        panel
            .attr('data-type', type)
            .append($('<input type="hidden" name="widgets[' + newPanelId + '][data]" type="hidden">').val(JSON.stringify(data)))
            .append($('<input type="hidden" name="widgets[' + newPanelId + '][info][raw]" type="hidden">').val(0))
            .append($('<input type="hidden" name="widgets[' + newPanelId + '][info][grid]" type="hidden">'))
            .append($('<input type="hidden" name="widgets[' + newPanelId + '][info][cell]" type="hidden">'))
            .append($('<input type="hidden" name="widgets[' + newPanelId + '][info][id]" type="hidden">').val(newPanelId))
            .append($('<input type="hidden" name="widgets[' + newPanelId + '][info][class]" type="hidden">').val(type))
            .append($('<input type="hidden" name="widgets[' + newPanelId + '][info][style]" type="hidden">').val(widgetStyleJson))
            .append($('<input type="hidden" name="panel_order[]" type="hidden">').val(newPanelId))
            .data({
                // We need this data to update the title
                'title-field': undefined,
                'title': 'Editor',
                'raw': false
            })
            .end().find('.title h4').html('Editor');

        // Set the title
        panel.panelsSetPanelTitle(data);

        // Add the action buttons
        panel
            .find('.title .actions')
            .append(
            $('<a data-tooltip="' + panels.i10n.buttons.delete + '"></a>').addClass('dashicons-before dashicons-dismiss delete')
        )
            .append(
            $('<a data-tooltip="' + panels.i10n.buttons.duplicate + '"></a>').addClass('dashicons-before dashicons-admin-page duplicate')
        )
            .append(
            $('<a data-tooltip="' + panels.i10n.buttons.edit + '"></a>').addClass('dashicons-before dashicons-edit edit')
        );

        panels.setupPanelButtons(panel);

        return panel;
    };

    panels.setupPanelButtons = function ($panel) {

        $panel.find('> .panel-wrapper > .title > h4').click(function () {
            $(this).closest('.panel').find('a.edit').click();
            return false;
        });

        $panel.find('> .panel-wrapper > .title > .actions > .edit').click(function () {

            var $currentPanel = $(this).closest('.panel');

            var type = $currentPanel.attr('data-type');

            if (typeof activeDialog != 'undefined') return false;

            // The done button
            var doneClicked = false;

            window.$currentPanel = $(this).parents('.panel');

            // Create a dialog for this form
            activeDialog = $('<div class="panel-dialog dialog-form"></div>')
                .data('widget-type', type)
                .addClass('ui-dialog-content-loading')
                .addClass('widget-dialog-' + type.toLowerCase())
                .dialog({
                    dialogClass: 'panels-admin-dialog ppb-add-content-panel ppb-cool-panel-container',
                    autoOpen: false,
                    modal: false, // Disable modal so we don't mess with media editor. We'll create our own overlay.
                    draggable: false,
                    resizable: false,
                    title: "Editor",
                    height: $(window).height() - 50,
                    width: $(window).width() - 50,
                    create: function (event, ui) {
                        $(this).closest('.ui-dialog').find('.show-in-panels').show();
                    },
                    open: function () {
                        // This fixes the A element focus issue
                        $(this).closest('.ui-dialog').find('a').blur();

                        var overlay = $('<div class="ppb-panels-ui-widget-overlay ui-widget-overlay ui-front"></div>').css('z-index', 80001);
                        $(this).data('overlay', overlay).closest('.ui-dialog').before(overlay);

                    },
                    close: function () {

                        if (!doneClicked) {
                            $(this).trigger('panelsdone', $currentPanel, activeDialog);
                        }

                        //Set the widget styles
                        panels.pootlePageSetWidgetStyles($('.pootle-style-fields'));

                        $currentPanel.panelsSetPanelTitle( $currentPanel.panelsGetPanelData() );

                        // Destroy the dialog and remove it
                        $(this).data('overlay').remove();
                        $(this).dialog('destroy').remove();
                        activeDialog = undefined;
                    },
                    buttons: [
                        {
                            text: panels.i10n.buttons['done'],
                            class: 'button pootle stop',
                            click: function () {
                                doneClicked = true;
                                var editor = $('#ppbeditor');
                                if( ! editor.is(':visible') ) {
                                    editor.val(tinyMCE.get('ppbeditor').getContent());
                                }

                                $(this).trigger('panelsdone', $currentPanel, activeDialog);

                                var panelData = $currentPanel.panelsGetPanelData();

                                $currentPanel.find('input[name$="[data]"]').val(JSON.stringify(panelData));
                                $currentPanel.find('input[name$="[info][raw]"]').val(1);

                                // Change the title of the panel
                                activeDialog.dialog('close');
                            }
                        }
                    ]
                })
                .keypress(function (e) {
                    if (e.keyCode == $.ui.keyCode.ENTER) {
                        if ($(this).closest('.ui-dialog').find('textarea:focus').length > 0) return;

                        // This is the same as clicking the add button
                        $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane .ui-button:eq(0)').click();
                        e.preventDefault();
                        return false;
                    }
                    else if (e.keyCode === $.ui.keyCode.ESCAPE) {
                        $(this).closest('.ui-dialog').dialog('close');
                    }
                });

            // This is so we can access the dialog (and its forms) later.
            $currentPanel.data('dialog', activeDialog);

            // Load the widget form
            var widgetClass = type;
            try {
                widgetClass = widgetClass.replace('\\\\', '\\');
            }
            catch (err) {
                return;
            }

            instance = $currentPanel.panelsGetPanelData();

            var data = {
                'action': 'pootlepb_editor_form',
                'widget': widgetClass,
                'instance': JSON.stringify(instance),
                'raw': $currentPanel.find('input[name$="[info][raw]"]').val()
            };

            if ("Pootle_Text_Widget" == widgetClass || "Pootle_PB_Content_Block" == widgetClass) {

                var text = '',
                    filter = 1;

                if (typeof instance.text != 'undefined') {
                    text = instance.text;
                    filter = instance.filter;
                }

                var newPanelId = $currentPanel.find('> input[name$="[info][id]"]').val();

                var editor_cache = panels.editor_form_cache;

                activeDialog
                    .html( editor_cache )
                    .dialog("option", "position", {my: "center", at: "center", of: window})
                    .dialog("open");

                tinymce.execCommand( 'mceRemoveEditor', false, 'ppbeditor' );
                tinymce.execCommand( 'mceAddEditor', false, 'ppbeditor' );

                content = tinyMCE.get('ppbeditor').setContent( text );

                var editor = $('#ppbeditor'),
                    name = editor.attr('name');

                editor.attr('name', name.replace(/\{\$id\}/g, newPanelId));

                $('.ppb-add-content-panel')
                    .tabs({
                        activate: function (e, ui) {
                            var $t = $(this),
                                title = $t.find('.ui-tabs-active a').html(),
                                $target = $(e.toElement);
                            $('.ppb-add-content-panel .ui-dialog-titlebar .ui-dialog-title').html(title);

                            //panels.ppbContentModule( e, ui, $t, $currentPanel );
                        },
                        active: 0
                    })
                    .addClass("ui-tabs-vertical ui-helper-clearfix")
                    .find('input[data-style-field-type="color"]').each(function () {
                        $t = $(this);
                        var wpPkrContnr = $t.closest('.wp-picker-container');
                        if ( wpPkrContnr.length > 0 ) {
                            wpPkrContnr.after($t);
                            wpPkrContnr.remove();
                        }
                        $t.wpColorPicker();
                    });

                var $t = $('.ppb-add-content-panel'),
                    title = $t.find('.ui-tabs-active a').html();
                $('.ppb-add-content-panel .ui-dialog-titlebar .ui-dialog-title').html(title);

                $(".ppb-cool-panel-wrap li").removeClass("ui-corner-top").addClass("ui-corner-left");

                //Get style data in fields
                panels.pootlePageGetWidgetStyles($('.pootle-style-fields'));

                // This is to refresh the dialog positions
                $(window).resize();
                $(document).trigger('panelssetup', $currentPanel, activeDialog);
                $('#panels-container .panels-container').trigger('refreshcells');

                // This gives panel types a chance to influence the form
                activeDialog.removeClass('ui-dialog-content-loading').trigger('panelsopen', $currentPanel, activeDialog);
            } else {

                $.post(
                    ajaxurl,
                    data,
                    function (result) {
                        // the newPanelId is defined at the top of this function.
                        try {
                            var newPanelId = $currentPanel.find('> input[name$="[info][id]"]').val();

                            result = result.replace(/\{\$id\}/g, newPanelId);
                        }
                        catch (err) {
                            result = '';
                        }

                        activeDialog
                            .html(result)
                            .dialog("option", "position", {my: "center", at: "center", of: window})
                            .dialog("open");

                        $('.ppb-add-content-panel')
                            .tabs({
                                activate: function (e, ui) {
                                    var $t = $(this),
                                        title = $t.find('.ui-tabs-active a').html(),
                                        $target = $(e.toElement);
                                    $('.ppb-add-content-panel .ui-dialog-titlebar .ui-dialog-title').html(title);

                                    //panels.ppbContentModule( e, ui, $t, $currentPanel );
                                },
                                active: 0
                            })
                            .addClass("ui-tabs-vertical ui-helper-clearfix")
                            .find('input').each(function () {
                                $t = $(this);
                                if ($t.attr('data-style-field-type') == 'color') {
                                    $t.wpColorPicker();
                                }
                            });

                        var $t = $('.ppb-add-content-panel'),
                            title = $t.find('.ui-tabs-active a').html();
                        $('.ppb-add-content-panel .ui-dialog-titlebar .ui-dialog-title').html(title);

                        //$('.ppb-cool-panel-wrap [selected]').click();

                        $(".ppb-cool-panel-wrap li").removeClass("ui-corner-top").addClass("ui-corner-left");

                        //Get style data in fields
                        panels.pootlePageGetWidgetStyles($('.pootle-style-fields'));

                        $(window).resize();

                        // This is to refresh the dialog positions
                        $(window).resize();
                        $(document).trigger('panelssetup', $currentPanel, activeDialog);
                        $('#panels-container .panels-container').trigger('refreshcells');

                        // This gives panel types a chance to influence the form
                        activeDialog.removeClass('ui-dialog-content-loading').trigger('panelsopen', $currentPanel, activeDialog);
                    },
                    'html'
                );
            }

            return false;
        });

        $panel.find('> .panel-wrapper > .title > .actions > .duplicate').click(function () {

            var $currentPanel = $(this).closest('.panel');

            // Duplicate the widget
            var data = JSON.parse($currentPanel.find('input[name*="[data]"]').val());

            if (typeof data.info == 'undefined') {
                data.info = {};
                data.info.raw = $currentPanel.find('input[name*="[info][raw]"]').val();
                data.info.grid = $currentPanel.find('input[name*="[info][grid]"]').val();
                data.info.cell = $currentPanel.find('input[name*="[info][cell]"]').val();
                data.info.id = $currentPanel.find('input[name*="[info][id]"]').val();
                data.info.class = $currentPanel.find('input[name*="[info][class]"]').val();
            }
            if (typeof data.info.style == 'undefined') {
                data.info.style = JSON.parse($currentPanel.find('input[name*="[info][style]"]').val());
            }

            var duplicatePanel = panelsCreatePanel($currentPanel.attr('data-type'), data);
            window.panels.addPanel(duplicatePanel, $currentPanel.closest('.panels-container'), null, false);
            duplicatePanel.removeClass('new-panel');

            return false;
        });

        $panel.find('> .panel-wrapper > .title > .actions > .style').click(function () {

            var $currentPanel = $(this).closest('.panel');

            // style dialog
            if (typeof activeDialog != 'undefined') return false;

            window.$currentPanel = $currentPanel;

            $('#widget-styles-dialog').dialog('open');

            return false;
        });

        $panel.find('> .panel-wrapper > .title > .actions > .delete').click(function () {
            var $currentPanel = $(this).closest('.panel');

            $('#remove-widget-dialog').dialog({
                dialogClass: 'panels-admin-dialog',
                autoOpen: false,
                modal: false, // Disable modal so we don't mess with media editor. We'll create our own overlay.
                title: $('#remove-widget-dialog').attr('data-title'),
                open: function () {
                    var overlay = $('<div class="ppb-panels ui-widget-overlay ui-widget-overlay ui-front"></div>').css('z-index', 80001);
                    $(this).data('overlay', overlay).closest('.ui-dialog').before(overlay);
                },
                close: function () {
                    $(this).data('overlay').remove();
                },
                buttons: {
                    Yes: function () {

                        // The delete button
                        var deleteFunction = function ($panel) {
                            // Add an entry to the undo manager

                            panels.undoManager.register(
                                this,
                                function (type, data, container, position) {
                                    // Readd the panel
                                    var panel = panelsCreatePanel(type, data, container);
                                    panels.addPanel(panel, container, position, true);

                                    // We don't want to animate the undone panels
                                    $('#panels-container .panel').removeClass('new-panel');
                                },
                                [$panel.attr('data-type'), $panel.panelsGetPanelData(), $panel.closest('.panels-container'), $panel.index()],
                                'Remove Panel'
                            );

                            // Create the undo notification
                            $('#panels-undo-message').remove();
                            $('<div id="panels-undo-message" class="updated"><p>' + panels.i10n.messages.deleteWidget + '. <a href="#" class="undo">' + panels.i10n.buttons.undo + '</a></p></div>')
                                .appendTo('body')
                                .hide()
                                .slideDown()
                                .find('a.undo')
                                .click(function () {
                                    panels.undoManager.undo();
                                    $('#panels-undo-message').fadeOut(function () {
                                        $(this).remove();
                                    });
                                    return false;
                                })
                            ;

                            var remove = function () {
                                // Remove the panel and refresh the grid container cell sizes
                                var gridContainer = $panel.closest('.grid-container');
                                $panel.remove();
                                gridContainer.panelsResizeCells();
                            };

                            if (panels.animations) $panel.addClass('removed').slideUp(remove);
                        };

                        deleteFunction($currentPanel);

                        $(this).dialog('close');
                    },
                    Cancel: function () {
                        $(this).dialog('close');
                    }
                }

            });

            $('#remove-widget-dialog').dialog('open');

            return false;
        });
    };

    /**
     * Add a widget to the interface.
     *
     * @param panel The new panel (Widget) we're adding.
     * @param container The container we're adding it to
     * @param position The position
     * @param bool animate Should we animate the panel
     * @since 0.1.0
     */
    panels.addPanel = function (panel, container, position, animate) {

        if (container == null) container = $('#panels-container .cell.cell-selected .panels-container').eq(0);
        if (container.length == 0) container = $('#panels-container .cell .panels-container').eq(0);
        if (container.length == 0) {
            // There are no containers, so lets add one.
            panels.createGrid(1, [1]);
            container = $('#panels-container .cell .panels-container').eq(0);
        }

        if (position == null) container.append(panel);
        else {
            var current = container.find('.panel').eq(position);
            if (current.length == 0) container.append(panel);
            else {
                panel.insertBefore(current);
            }
        }

        container.sortable("refresh").trigger('refreshcells');
        container.closest('.grid-container').panelsResizeCells();
        if (animate) {
            if (panels.animations)
                $('#panels-container .panel.new-panel')
                    .hide()
                    .slideDown(450, function () {
                        panel.find('a.edit').click();
                    })
                    .removeClass('new-panel');
            else {
                $('#panels-container .panel.new-panel').show().removeClass('new-panel');
                panel.find('a.edit').click();
            }
        }
    }

    /**
     * Set the title of the panel
     * @since 0.1.0
     */
    $.fn.panelsSetPanelTitle = function (data) {
        var $t = $(this);

        if ( typeof data == 'undefined' || typeof data.text == 'undefined' || data.text == '') {
            $t.find('h4').html('Editor');
            return;
        }

        var text = data.text.replace( /<p>/g, '' ),
            title = 'Editor';

        switch ( text.replace( ' ', '' ).charAt(0) ) {
            case '[':
                if ( text.match( /\[.+]/gi ) && 0 < text.match( /\[.+]/gi ).length ) {
                    var shortcode = text.match( /\[.+]/g )[0].replace( /[\[]/g, '' ).split( /[^\w]/g )[0];
                    title = 'Shortcode: <span class="extra">' + shortcode + '</span>';
                    title = ppbSmartTitle.processShortcodes(shortcode, text, title);
                } else {
                    title = ppbSmartTitle.detectText(text, title);
                }
                break;
            case '<':
                var $txt = $( '<p>' + text );

                if ( 0 == text.indexOf('<a ') && 1 == $txt.find('a').eq(0).find('img').length ) {
                    var imgMatch = $txt.find('a').eq(0).find('img');
                    title = 'Image<span class="extra">: ' + $(imgMatch[0]).attr('alt') + '</span>';
                } else {
                    title = ppbSmartTitle.detectText(text, title);
                }
                break;
            case 'h':
                //Maybe Video
                if ( 0 == text.indexOf('https://www.vimeo.com/') || 0 == text.indexOf('https://vimeo.com/') || 0 == text.indexOf('https://player.vimeo.com/') ) {
                    title = 'Video<span class="extra">: Vimeo</span>';
                } else if ( 0 == text.indexOf('https://www.youtube.com/') ||  0 == text.indexOf('https://youtube.com/' ) ) {
                    title = 'Video<span class="extra">: YouTube</span>';
                } else {
                    title = ppbSmartTitle.detectText(text, title);
                }
                break;
            default:
                //Text
                title = ppbSmartTitle.detectText(text, title);
        }
        $t.find('h4').html(title);
    };

    ppbSmartTitle = {
        detectText : function( text, title ) {

            //Remove shortcodes
            text = text.replace( /\[.+]/gi, '' );

            //Removing HTML tags
            text = $('<p>' + text + '</p>').text();

            if ( text ) {
                if (text.length > 10) text = text.substring(0, 14);
                return 'Text<span class="extra">: ' + text + '...</span>';
            } else {
                return title;
            }
        },
        processShortcodes : function( code, text, title ) {
            code = code.toLowerCase();
            if ( 'caption' == code ) {
                $txt = $( '<p>' + text );
                var imgMatch = $txt.find('a').eq(0).find('img');
                title = 'Image<span class="extra">: ' + $(imgMatch[0]).attr('alt') + '</span>';
            } else if ( 'embed' == code ) {
                var end = text.indexOf('[/embed]');
                text = text.substring(7, end);

                if ( text.match(/vimeo\.com/i) ) {
                    title = 'Video<span class="extra">: Vimeo</span>';
                } else if ( 0 < text.match(/youtube.com\//i).length ) {
                    title = 'Video<span class="extra">: YouTube</span>';
                }
            }

            return title;
        }
    };

    /**
     * Loads panel data
     *
     * @param data
     * @since 0.1.0
     */
    panels.loadPanels = function (data) {
        panels.clearGrids();

        if (typeof data != 'undefined' && typeof data.grids != 'undefined') {
            // Create all the content
            for (var gi in data.grids) {
                var cellWeights = [];

                // Get the cell weights
                for (var ci in data.grid_cells) {
                    if (Number(data.grid_cells[ci]['grid']) == gi) {
                        cellWeights[cellWeights.length] = Number(data.grid_cells[ci].weight);
                    }
                }

                // Create the grids
                var grid = panels.createGrid(Number(data.grids[gi]['cells']), cellWeights, data.grids[gi]['style']);

                // Add panels to the grid cells
                for (var pi in data.widgets) {

                    if (typeof  data.widgets[pi]['info'] == 'undefined') continue;

                    if (Number(data.widgets[pi]['info']['grid']) == gi) {
                        var pd = data.widgets[pi];
                        var panel = panelsCreatePanel(pd['info']['class'], pd);
                        grid
                            .find('.panels-container').eq(Number(data.widgets[pi]['info']['cell']))
                            .append(panel)
                    }
                }
                panels.ppbGridEvents(grid);

            }

            if (typeof pootlePBShowWrap == "function") {
                pootlePBShowWrap();
            }
        }

        $('#panels-container .panels-container')
            .sortable('refresh')
            .trigger('refreshcells');

        // Remove the new-panel class from any of these created panels
        $('#panels-container .panel').removeClass('new-panel');

        // Make sure everything is sized properly
        $('#panels-container .grid-container').each(function () {
            $(this).panelsResizeCells();
        });
    };

    panels.pootlePageGetWidgetStyles = function ($styleForm) {
        var $styleDataField = window.$currentPanel.find('input[name$="[style]"]');
        var json = $styleDataField.val();
        var styleData = JSON.parse(json);

        // by default, set checkbox to unchecked,
        // so when a widget has no saved checkbox setting, and widget styling dialog is display,
        // it will be set to unchecked,
        // this is to set hide widget title checkbox
        $styleForm.find('input[type=checkbox]').prop('checked', false);
        $styleForm.find('input[type=text], input[type=number], textarea').val('').change();

        // from style data in hidden field, set the widget style dialog fields with data
        for (var key in styleData) {
            if (styleData.hasOwnProperty(key)) {

                var $field = $styleForm.find('[dialog-field="' + key + '"]');

                if ($field.attr('data-style-field-type') == "color" ) {
                    $field.wpColorPicker('color', styleData[key]);
                } else if ($field.attr('data-style-field-type') == "checkbox") {
                    if (styleData[key] == $field.val()) {
                        $field.prop('checked', true);
                    } else {
                        $field.prop('checked', false);
                    }
                } else {
                    $field.val(styleData[key]);
                }

            }
        }

    }

    panels.pootlePageSetWidgetStyles = function ($styleForm) {
        var $currentPanel = window.$currentPanel;

        // from values in dialog fields, set style data into hidden fields
        var styleData = {};
        $styleForm.find('[dialog-field]').each(function () {
            var $t = $(this);

            if ($t.attr('type') == 'checkbox') {
                // if the field is checkbox, only store value if it is checked
                if ($t.prop('checked')) {
                    var key = $t.attr('dialog-field');
                    styleData[key] = $t.val();
                }
            } else {
                var key = $t.attr('dialog-field');
                styleData[key] = $t.val();
            }

        });

        $currentPanel.find('input[name$="[style]"]').val(JSON.stringify(styleData));
    };

    $(document).ready(function () {
        panels.editor_form_cache = $('.ppb-hidden-editor-container').contents();
    });

})(jQuery);