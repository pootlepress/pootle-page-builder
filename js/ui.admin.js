/**
 * Initial setup for the panels interface
 *
 * @copyright Greg Priday 2013
 * @license GPL 2.0 http://www.gnu.org/licenses/gpl-2.0.html
 * @since 0.1.0
 */

jQuery(function ($) {
    function add_block_button_hide_text_for_small_cells() {
        $('#panels-container .cell-wrapper').each(function () {

            $t = $(this);
            $add_content = $t.find('.add-content-button');

            $add_content.find('span').css('display', 'inline');

            if ($t.width() < 120) {
                $add_content.find('span').css('display', 'none');
            }
        });
    }

    panels.animations = $('#panels').data('animations');

    $(window).bind('resize', function (event) {

        add_block_button_hide_text_for_small_cells()

        // ui-resizable elements trigger resize
        if ($(event.target).hasClass('ui-resizable')) return;

        // Resize all the grid containers
        $('#panels-container .grid-container').panelsResizeCells();
    });

    // Create a sortable for the grids
    $('#panels-container').sortable({
        items: '> .grid-container',
        handle: '.grid-handle',
        tolerance: 'pointer',
        stop: function () {
            $(this).find('.cell').each(function () {
                // Store which grid this is in by finding the index of the closest .grid-container
                $(this).find('input[name$="[grid]"]').val($('#panels-container .grid-container').index($(this).closest('.grid-container')));
            });

            $('#panels-container .grid-container').trigger('refreshcells');
        }
    });

    // Create the add grid dialog
    var gridAddDialogButtons = {};
    gridAddDialogButtons[panels.i10n.buttons.add] = function () {
        var num = Number($('#grid-add-dialog').find('input').val());

        if (isNaN(num)) {
            alert('Invalid Number');
            return false;
        }

        // Make sure the number is between 1 and 10.
        num = Math.min(10, Math.max(1, Math.round(num)));
        var gridContainer = window.panels.createGrid(num);

        panels.ppbGridEvents(gridContainer);

        if (panels.animations) gridContainer.hide().slideDown();
        else gridContainer.show();

        $('#grid-add-dialog').ppbDialog('close');
    };

    window.pootlePagePageSettingUploadButton = function () {

        $('#page-setting-dialog .upload-button').click(function () {

            var $textField = $(this).parent().find('input');
            var textFieldID = $textField.attr('id');

            window.formfield = textFieldID;

            window.send_to_editor = function (html) {

                if (formfield) {

                    // itemurl = $(html).attr( 'href' ); // Use the URL to the main image.

                    if ($(html).html(html).find('img').length > 0) {

                        itemurl = $(html).html(html).find('img').attr('src'); // Use the URL to the size selected.

                    } else {

                        // It's not an image. Get the URL to the file instead.

                        var htmlBits = html.split("'"); // jQuery seems to strip out XHTML when assigning the string to an object. Use alternate method.

                        itemurl = htmlBits[1]; // Use the URL to the file.

                        var itemtitle = htmlBits[2];

                        itemtitle = itemtitle.replace('>', '');
                        itemtitle = itemtitle.replace('</a>', '');

                    } // End IF Statement

                    var image = /(^.*\.jpg|jpeg|png|gif|ico*)/gi;

                    if (itemurl.match(image)) {
                        //btnContent = '<img src="'+itemurl+'" alt="" /><a href="#" class="mlu_remove button">Remove Image</a>';
                    } else {
                    }

                    $('#' + formfield).val(itemurl);
//                    $( '#' + formfield).siblings( '.screenshot').slideDown().html(btnContent);
                    tb_remove();

                } else {
                    window.original_send_to_editor(html);
                }

                // Clear the formfield value so the other media library popups can work as they are meant to. - 2010-11-11.
                formfield = '';

            };
            tb_show('', 'media-upload.php?post_id=0&amp;title=Background%20Image&amp;type=image&amp;TB_iframe=true');
            return false;
        });
    };

    $('#page-setting-dialog [data-style-field-type="color"]')
        .wpColorPicker()
        .closest('p').find('a').click(function () {
            $('#page-setting-dialog').ppbDialog("option", "position", "center");
        });

    // The done button
    var dialogButtons = {};
    var doneClicked = false;
    dialogButtons[panels.i10n.buttons['done']] = function () {
        doneClicked = true;

        // Change the title of the panel
        $('#widget-styles-dialog').ppbDialog('close');
    };

    // Create a dialog for this form
    $('#widget-styles-dialog')
        .ppbDialog({
            dialogClass: 'panels-admin-dialog',
            autoOpen: false,
            draggable: false,
            resizable: false,
            title: panels.i10n.messages.styleWidget,
            minWidth: 500,
            maxHeight: Math.min(Math.round($(window).height() * 0.875), 800),
            open: function () {
                // This fixes the A element focus issue
                $(this).closest('.ui-dialog').find('a').blur();

                var $hidden = window.$currentPanel.find('input[name$="[style]"]');
                var json = $hidden.val();
                var styleData = JSON.parse(json);

                // by default, set checkbox to unchecked,
                // so when a widget has no saved checkbox setting, and widget styling dialog is display,
                // it will be set to unchecked,
                // this is to set hide widget title checkbox
                $(this).find('input[type=checkbox]').prop('checked', false);

                // from style data in hidden field, set the widget style dialog fields with data
                for (var key in styleData) {
                    if (styleData.hasOwnProperty(key)) {

                        var $field = $(this).find('input[dialog-field="' + key + '"]');
                        if ($field.attr('data-style-field-type') == "color") {
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
            },
            close: function () {
                $(this).data('overlay').remove();

                var $currentPanel = window.$currentPanel;
                if (!doneClicked) {
                    $(this).trigger('panelsdone', $currentPanel, $('#widget-styles-dialog'));
                }

                // from values in dialog fields, set style data into hidden fields
                var styleData = {};
                $(this).find('input[dialog-field]').each(function () {
                    if ($(this).attr('type') == 'checkbox') {
                        // if the field is checkbox, only store value if it is checked
                        if ($(this).prop('checked')) {
                            var key = $(this).attr('dialog-field');
                            styleData[key] = $(this).val();
                        }
                    } else {
                        var key = $(this).attr('dialog-field');
                        styleData[key] = $(this).val();
                    }

                });

                $currentPanel.find('input[name$="[style]"]').val(JSON.stringify(styleData));

                var allData = JSON.parse($currentPanel.find('input[name$="[data]"]').val());
                if (typeof allData.info == 'undefined') {
                    allData.info = {};
                }

                allData.info.raw = $currentPanel.find('input[name$="[info][raw]"]').val();
                allData.info.grid = $currentPanel.find('input[name$="[info][grid]"]').val();
                allData.info.cell = $currentPanel.find('input[name$="[info][cell]"]').val();
                allData.info.id = $currentPanel.find('input[name$="[info][id]"]').val();
                allData.info.class = $currentPanel.find('input[name$="[info][class]"]').val();

                allData.info.style = styleData;
                $currentPanel.find('input[name$="[data]"]').val(JSON.stringify(allData));

                // Destroy the dialog and remove it
                activeDialog = undefined;
            },
            buttons: dialogButtons
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
                $(this).closest('.ui-dialog').ppbDialog('close');
            }
        });

    $('#widget-styles-dialog [data-style-field-type="color"]')
        .wpColorPicker()
        .closest('p').find('a').click(function () {
            $('#widget-styles-dialog').ppbDialog("option", "position", "center");
        });

    //
    // Hide Element Dialog
    //
    $('#hide-element-dialog').ppbDialog({
        dialogClass: 'panels-admin-dialog',
        autoOpen: false,
        width: 500,
        maxHeight: Math.round($(window).height() * 0.8),
        draggable: false,
        resizable: false,
        title: $('#hide-element-dialog').attr('data-title'),
        open: function () {
            var fieldValues = JSON.parse($('#hide-elements').val());
            if (typeof fieldValues != 'undefined' || fieldValues != null) {

                for (var fieldName in fieldValues) {

                    // Save the dialog field
                    var df = $('#hide-element-dialog [data-style-field="' + fieldName + '"]');
                    switch (df.data('style-field-type')) {
                        case 'checkbox':
                            df.attr('checked', fieldValues[fieldName]);
                            break;
                        default :
                            df.val(fieldValues[fieldName]);
                            break;
                    }
                }
            }

        },
        close: function () {
            // Copy the dialog values back to the hidden value
            var fieldValues = {};
            $('#hide-element-dialog [data-style-field]').each(function () {
                var $$ = $(this);
                var fieldName = $$.data('style-field');

                switch ($$.data('style-field-type')) {
                    case 'checkbox':
                        fieldValues[fieldName] = $$.is(':checked');
                        break;
                    default :
                        fieldValues[fieldName] = $$.val();
                        break;
                }
            });

            $('#hide-elements').val(JSON.stringify(fieldValues));
        },
        buttons: {
            'Done': function () {
                $('#hide-element-dialog').ppbDialog('close');
            }
        }
    });

    // Create the dialog that we use to add new grids
    $('#grid-add-dialog')
        .show()
        .ppbDialog({
            dialogClass: 'panels-admin-dialog',
            autoOpen: false,
            title: $('#grid-add-dialog').attr('data-title'),
            open: function () {
                $(this).find('input').val(2).select();
            },
            width: 430,
            buttons: gridAddDialogButtons
        })
        .on('keydown', function (e) {
            if (e.keyCode == $.ui.keyCode.ENTER) {
                // This is the same as clicking the add button
                gridAddDialogButtons[panels.i10n.buttons.add]();
                setTimeout(function () {
                    $('#grid-add-dialog').ppbDialog('close');
                }, 1)
            }
            else if (e.keyCode === $.ui.keyCode.ESCAPE) {
                $('#grid-add-dialog').ppbDialog('close');
            }
        });
    ;

    // Dialog for content loss warning
    $contentSwitchDialog = $('#content-loss-dialog');

    $contentSwitchDialog
        .ppbDialog({
            dialogClass: 'panels-admin-dialog',
            autoOpen: false,
            width: 550,
            title: $contentSwitchDialog.attr('data-title'),
            open: function () {
                $(this).find('input').val(2).select();
            },
            buttons: [
                {
                    text: $contentSwitchDialog.attr('data-button-i-know'),
                    class: 'button i-know',
                    click: function () {
                        activate_panels();
                        $(this).ppbDialog("close");
                    }
                },
                {
                    text: $contentSwitchDialog.attr('data-button-stop'),
                    class: 'button pootle stop',
                    click: function () {
                        $(this).ppbDialog("close");
                    }
                }
            ]
        });

    // Dialog for page builder layout loss warning
    $contentSwitchDialog = $('#layout-loss-dialog');

    $contentSwitchDialog
        .ppbDialog({
            dialogClass: 'panels-admin-dialog',
            autoOpen: false,
            width: 700,
            title: $contentSwitchDialog.attr('data-title'),
            open: function () {
                $(this).find('input').val(2).select();
            },
            buttons: [
                {
                    text: $contentSwitchDialog.attr('data-button-i-know'),
                    class: 'button i-know',
                    click: function () {

                        $('.switch-tmce').click();
                        $('#wp-content-wrap').addClass('tmce-active');

                        $(this).ppbDialog("close");

                        $('#pootlepb-panels').append(
                            $('<input type="hidden" value="1" name="pootlepb_noPB">').attr( 'id', 'pootlepb_noPB')
                        );

                    }
                },
                {
                    text: $contentSwitchDialog.attr('data-button-stop'),
                    class: 'button pootle stop',
                    click: function () {
                        $(this).ppbDialog("close");
                    }
                }
            ]
        });

    $('#no-empty-col-dialog')
        .ppbDialog({
            dialogClass: 'panels-admin-dialog',
            autoOpen: false,
            width: 500,
            title: $('#no-empty-col-dialog').attr('data-title')
        });

    $('#pootlepb-panels .handlediv').click(function () {
        // Trigger the resize to reorganise the columns
        setTimeout(function () {
            $(window).resize();
        }, 150);
    });

    // The button for adding a grid
    $('#panels .grid-add')
        .click(function () {
            $('#grid-add-dialog').ppbDialog('open');
            return false;
        });

    $('#add-to-pb-panel .page-settings').click(function () {
        $('#page-setting-dialog').ppbDialog('open');
        return false;
    });

    $('#add-to-pb-panel .hide-elements').click(function () {
        $('#hide-element-dialog').ppbDialog('open');
        return false;
    });

    // Handle filtering in the panels dialog
    $('#panels-text-filter-input')
        .keyup(function (e) {
            if (e.keyCode == 13) {
                // If we pressed enter and there's only one widget, click it
                if (p.length == 1) p.click();
                return;
            }

            var value = $(this).val().toLowerCase();
        })
        .click(function () {
            $(this).keyup()
        });

    $(window).resize(function () {
        // When the window is resized, we want to center any panels-admin-dialog dialogs
        $('.panels-admin-dialog').filter(':data(dialog)').ppbDialog('option', 'position', 'center');
    });

    // Handle switching between the page builder and other tabs
    // since version 4.1, html for editor tabs is different

    $('#wp-content-editor-tools')
        .find('.wp-switch-editor')
        .click(function () {
            var $$ = $(this);

            $('#wp-content-editor-container, #post-status-info').show();
            $('#pootlepb-panels').hide();
            $('#wp-content-wrap').removeClass('panels-active');

            $('#content-resize-handle').show();
        }).end()
        .prepend('<a id="content-tmce-editor" class="button pootle-switch-editor">Default Editor</a>')
        .prepend(
        $('<a id="content-panels" class="button pootle switch-panels">Page Builder</a>')
            .click(function () {

                if (( $('.wp-editor-area').val().replace(/(<([^>]+)>)/ig, "") || $('#tinymce').html() ) && ( typeof panelsData == 'undefined' || panelsData.grids.length == 0 )) {

                    //Warning for content loss
                    $('#content-loss-dialog').ppbDialog('open');

                } else {
                    activate_panels()
                }

            })
    );

    function activate_panels() {

        // load panels or create 1 lazily
        if (typeof window.PBPanelsNeedLoad != 'undefined' && window.PBPanelsNeedLoad) {

            // Either setup an initial grid or load one from the panels data
            if (typeof panelsData != 'undefined') {
                panels.loadPanels(panelsData);
            }

            window.PBPanelsNeedLoad = false;
        }

        var $$ = $(this);
        // This is so the inactive tabs don't show as active
        $('#wp-content-wrap').removeClass('tmce-active html-active');

        // Hide all the standard content editor stuff
        $('#wp-content-editor-container, #post-status-info').hide();

        // Show panels and the inside div
        $('#pootlepb-panels').show().find('> .inside').show();
        $('#wp-content-wrap').addClass('panels-active');

        // Triggers full refresh
        $(window).resize();
        $('#content-resize-handle').hide();

        $('#pootlepb_noPB').remove();

        return false;
    }

    $('#wp-content-editor-tools .wp-switch-editor').click(function () {
        // no longer need this fix
        // This fixes an occasional tab switching glitch
        //var $$ = $(this);
        //var p = $$.attr('id' ).split('-');
        //$( '#wp-content-wrap' ).addClass(p[1] + '-active');

        if ($(this).is('.switch-panels')) {
            $('#wp-content-media-buttons').hide();
        } else {
            $('#wp-content-media-buttons').show();
        }

    });

    // This is for the home page panel
    $('#panels-home-page #post-body').show();
    $('#panels-home-page #post-body-wrapper').css('background', 'none');

    // Move the panels box into a tab of the content editor
    $('#pootlepb-panels')
        .insertAfter('#wp-content-editor-container')
        .addClass('wp-editor-container')
        .hide()
        .find('.hndle span').remove().end()
        .find('.hndle').removeClass('hndle').addClass('pootlepb-toolbar').append($('#add-to-pb-panel'));
    // When the content panels button is clicked, trigger a window resize to set up the columns
    $('#content-panels').click(function () {
        $(window).resize();
    });

    $('#content-tmce-editor').click(function () {

        //If no panels data no dialog
        if (0 == $('.grid-container .grid').length) {

            $('.switch-tmce').click();
            $('#wp-content-wrap').addClass('tmce-active');

            return;

        }
        $('#layout-loss-dialog').ppbDialog('open');
    });

    // Click again after the panels have been set up
    setTimeout(function () {
        if (typeof panelsData != 'undefined') {
            // this is a page that is created before
            if (panelsData.grids.length == 0) {
                // no grid was created
                //$('#content-tmce').click(); // this line will cause issue
                // create grid when click to PB tab
                window.PBPanelsNeedLoad = true;
            } else {
                // has grid, load and show it
                window.PBPanelsNeedLoad = true;
                $('#content-panels').click();
            }
        } else {
            // this is new page, or a page created when PB is deactivated
            //$('#content-tmce').click(); // this line will cause issue
            // create grid when click to PB tab
            window.PBPanelsNeedLoad = false;
        }

    }, 150);

    if ($('#panels-home-page').length) {
        // Lets do some home page settings
        $('#content-tmce, #content-html').remove();
        $('#content-panels').hide();

        // Initialize the toggle switch
        $('#panels-toggle-switch')
            .mouseenter(function () {
                $(this).addClass('subtle-move');
            })
            .click(function () {
                $(this).toggleClass('state-off').toggleClass('state-on').removeClass('subtle-move');
                $('#panels-home-enabled').val($(this).hasClass('state-off') ? 'false' : 'true');
            });

        // Handle the previews
        $('#post-preview').click(function (event) {

            var form = $('#panels-container').closest('form');

            var originalAction = form.attr('action');

            form.attr('action', panels.previewUrl).attr('target', '_blank').submit().attr('action', originalAction).attr('target', '_self');

            event.preventDefault();
        });
    }

    // Add a hidden field to show that the JS is complete. If this doesn't run we assume that JS is broken and the interface hasn't loaded properly
    $('#panels').append('<input name="panels_js_complete" type="hidden" value="1" />');

    panels.ppbGridEvents = function (container) {

        var numPanels = container.find('.panel-wrapper').length;
        container
            .find('.grid').css({marginBottom: '3px'})
            .find('.cell').css('padding-bottom', '3px')
            .find('.add-content-button').hide();
        var delay=500, ppbSetTimeout;
        container.find('.cell').hover(
            //MOUSE IN
            function () {
                var $t = $(this),
                    $gc = $(this).closest('.grid-container');

                setTimeoutConst = setTimeout(function(){
                    var this_cells = $t.find('.panel').length,
                        most_cells = 0;
                    $gc.find('.cell').each(function() {
                        var $t = $(this);
                        if( $t.find('.panel').length > most_cells ) {
                            most_cells = $t.find('.panel').length
                        }
                    });

                    if ( this_cells < most_cells ) {
                        $t.find('.add-content-button')
                            .css({
                                top: 10 + ( this_cells * 51 ),
                                bottom: '10px'
                            })
                            .show();
                        return;
                    }
                    if ( 0 != $t.find('.panel').length ) {
                        $gc
                            .find('.grid').animate(
                                { marginBottom: '61px' },
                                160,
                                'linear',
                                function () {
                                    $t.find('.add-content-button')
                                        .show();
                                }
                            )
                            .find('.cell').animate(
                                { paddingBottom: '61px' },
                                160,
                                'linear'
                            );
                    } else {
                        $t.find('.add-content-button').show();
                    }
                }, 500);

            },
            //MOUSE OUT
            function () {
                clearTimeout( setTimeoutConst );
                var $t = $(this).closest('.grid-container'),
                    numPanels = $t.find('.panel').length;
                $t.css("cursor", "default");

                panels.removePaddingAnimated( $t );
            }
        );
    };
    panels.ppbGridExpandHandler = function ( $t ) {
        var numPanels = $t.find('.panel-wrapper').length;
        if (numPanels > 0) {
        }
    }
    panels.removePaddingAnimated = function ( $t ) {
        var $grids = $t.find('.grid'),
            $cells = $t.find('.cell');
        $grids
            .stop()
            .animate( {
                marginBottom: '3'
            }, 160, 'linear' );
        $cells
            .stop()
            .animate(
            {
                paddingBottom: '3'
            }, {
                duration: 160,
                easing: 'linear',
                progress: function () {
                    $(this).find('.add-content-button').attr('style', '').hide();
                }
            }
        );

    };

    /**
     * Adds some text to the data...
     *
     * @param data The data to modify
     */
    panels.yoastSEOContent = function( data ) {
        if ( typeof panelsData != 'undefined' ) {
            $.each( panelsData.widgets, function ( k, v ) {
                data += v.text;
            } );
        }
        return data;

    };

    /*
     * Yoast SEO js filter
     */
    PootlePbSEO = function() {
        if ( 'undefined' == typeof YoastSEO || ! YoastSEO.app ) return;
        YoastSEO.app.registerPlugin( 'pootlepb', {status: 'ready'} );
        YoastSEO.app.registerModification( 'content', panels.yoastSEOContent, 'pootlepb' );
    };
    PootlePbSEO();
    $( document ).ready( PootlePbSEO );

});