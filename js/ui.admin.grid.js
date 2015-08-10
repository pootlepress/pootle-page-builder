/**
 * Grid layout for the Panel interface
 *
 * @copyright Greg Priday 2013
 * @license GPL 2.0 http://www.gnu.org/licenses/gpl-2.0.html
 * @since 0.1.0
 */

(function ($) {

    // The initial values
    var gridId = 0;
    var cellId = 0;

    /**
     * Visually resize the height of all cells, this should happen after a new panel is added.
     * @since 0.1.0
     */
    $.fn.panelsResizeCells = function () {

        return $(this).each(function () {
            var $$ = $(this);

            $$.find('.grid, .grid .cell, .grid .cell .cell-wrapper').css('height', 'auto');
            var totalWidth = $$.find('.grid').outerWidth();

            if ($$.find('.grid .cell').length > 1) {
                $$.find('.grid .cell').each(function () {
                    if ($(this).is('.first, .last')) totalWidth -= 6;
                    else totalWidth -= 12;
                });
            }

            var left = 0;
            var maxHeight = 0;
            $$.find('.grid .cell').each(function () {
                maxHeight = Math.max(maxHeight,
                    // The height of a panel is 54 (49 height with 5 bottom margin) and an extra 14 for top and bottom cell-wrapper paddings
                    //($(this).find('.panel').length * 54) + 14
                    $(this).height()
                );

                $(this)
                    .width( Math.floor( ( totalWidth-1 ) * Number($(this).attr('data-percent') ) ) )
                    .css('left', left);
                left += $(this).width() + 12;
            });

            // Resize all the grids and cell wrappers
            $$.find('.grid, .grid .cell, .grid .cell .cell-wrapper').css('height', Math.max(maxHeight, 1));
        });

    };

    /**
     * Create a new grid
     *
     * @param cells
     * @param weights
     * @param style
     *
     * @return {*}
     * @since 0.1.0
     */
    panels.createGrid = function (cells, weights, style) {

        if (weights == null || weights.length == 0) {
            weights = [];
            for (var i = 0; i < cells; i++) {
                weights[i] = 1;
            }
        }

        if (typeof style == 'undefined') style = {background:''};

        var weightSum = 0;
        for (var index in weights) {
            weightSum += weights[index];
        }

        // Create a new grid container
        var container = $('<div />').css('border-left-color', style.background).addClass('grid-container').appendTo('#panels-container');

        if ( undefined != typeof style.hide_row && style.hide_row ) {
            container.addClass( 'hide-row-enabled' );
        }

        // Add the hidden field to store the grid order
        container.append($('<input type="hidden" name="grids[' + gridId + '][cells]" />').val(cells));

        // Load the inputs required for the style
        var styleInput = {};
        for (var fieldName in panelsStyleFields) {

            styleInput[fieldName] = $('<input type="hidden" name="grids[' + gridId + '][style][' + fieldName + ']" data-style-field="' + fieldName + '" />').appendTo(container);
            if (typeof style[fieldName] != 'undefined') {
                if (panelsStyleFields[fieldName]['type'] == 'checkbox') {
                    styleInput[fieldName].val(style[fieldName] ? 'true' : '');
                }
                else {
                    styleInput[fieldName].val(style[fieldName]);
                }
            } else {
                styleInput[fieldName].val(panelsStyleFields[fieldName]['default']);
            }
        }

        container
            .append(
            $('<div class="controls" />')
                // Add the move/reorder area
                .append(
                    $('<div class="row-button sort-button grid-handle"></div>')
                )
                .append(
                    $('<div class="row-button row-bg-preview sort-button dashicons-before dashicons-visibility"></div>')
                    .attr('data-tooltip', 'Row is hidden')
                )
                // Add the add column button
                .append(
                $('<div class="row-button add-col-button dashicons-before dashicons-plus"></div>')
                    .attr('data-tooltip', 'Add Column')
                )
                // Add the remove column button
                .append(
                $('<div class="row-button remove-col-button dashicons-before dashicons-minus"></div>')
                    .attr('data-tooltip', 'Remove Column')
                )
                // Add the duplicate button
                .append(
                $('<div class="row-button duplicate-button dashicons-before dashicons-admin-page"></div>')
                    .attr('data-tooltip', 'Duplicate')
                )
                // Add the button for selecting the row style
                .append(
                    $('<div class="row-button style-button dashicons-before dashicons-admin-generic panels-visual-style"></div>')
                        .attr('data-tooltip', 'Row display settings')
                )
                // Add the remove button
                .append(
                    $('<div class="row-button delete-button dashicons-before dashicons-dismiss"></div>')
                        .attr('data-tooltip', panels.i10n.buttons['delete'])
                )
        );

        panels.setupGridButtons(container);

        // Hide the row style button if none are available
        if (!Object.keys(panelsStyleFields).length) {
            container.find('.panels-visual-style').remove();
        }

        var grid = $('<div />').addClass('grid').appendTo(container);

        for (var i = 0; i < cells; i++) {

            var element_class = 'cell';

            if ( 0.25 > ( weights[i]/weightSum ) ) {
                element_class += ' small-cell';
            }

            var cell = $(
                '<div class="' + element_class + '" data-percent="' + (weights[i] / weightSum) + '">' +
                '<div class="cell-wrapper panels-container">' +
                '<div class="add-content-button dashicons-before dashicons-plus"> </div>' +
                '</div>' +
                '<div class="cell-width"><div class="cell-width-left"></div><div class="cell-width-right"></div><div class="cell-width-line"></div><div class="cell-width-value"><span></span></div></div>' +
                '</div>'
            );
            if (i == 0) cell.addClass('first');
            if (i == cells - 1) cell.addClass('last');
            grid.append(cell);

            // Add the cell information fields
            cell
                .append($('<input type="hidden" name="grid_cells[' + cellId + '][weight]" />').val(weights[i] / weightSum))
                .append($('<input type="hidden" name="grid_cells[' + cellId + '][grid]" />').val(gridId))
                .data('cellId', cellId)

            cellId++;
        }
        grid.append($('<div />').addClass('clear'));
        gridId++;

        // Setup the grid
        panels.setupGrid(container);

        // setup add widget icons click handler
        container.find('.cell-wrapper .add-content-button').each(function () {
            $(this).click(function () {

                panels.removePaddingAnimated( $(this).closest('.grid-container') );

                panels.addContentModule(this);
            });
        });

        container.find('> .grid > .cell').each(function () {
            $(this).find('> .cell-wrapper > .panel').each(function () {
                var $panel = $(this);
                panels.setupPanelButtons($panel);
            });
        });

        panels.checkAddRowButtonColor();

        return container;
    };

    panels.setupGridButtons = function ($gridContainer) {

        $('html').trigger( 'pootlepb_admin_setup_row_buttons', [ $gridContainer ] );

        $gridContainer.find('> .controls > .add-col-button').click(function () {

            var $gridContainer = $(this).closest('.grid-container'),
                cells = $gridContainer.find('.cell'),
                numCells = Math.min( 10, cells.length + 1),
                //Getting old row data
                $old_inputs = $gridContainer.find('input[type=hidden][name^="grids["]'),
                //Getting old row bg color
                bg_color = $old_inputs.filter( '[name$="[style][background]"]').val(),
                //Creating new row with bg color
                $newRow = window.panels.createGrid( numCells, null, {background: bg_color} );

            //Updating old column count
            $old_inputs.filter( '[name$="][cells]"]').val( numCells );
            //Removing all new row data
            $newRow.find('input[type=hidden][name^="grids["]').remove();
            //Putting old row data (with updated column count) in the new row
            $newRow.prepend($old_inputs);

            for( var y = 0; y < cells.length; y++ ) {
                var $newCell = $newRow.find('.cell .cell-wrapper').eq(y),
                    $oldCell = $gridContainer.find('.cell .cell-wrapper').eq(y);

                $newCell.append($oldCell.contents());
            }

            $newRow.insertAfter($gridContainer);
            $('.panels-tooltip').remove();
            $gridContainer.remove();
            $(window).resize();
            panels.ppbGridEvents($newRow);

        });

        $gridContainer.find('> .controls > .remove-col-button').click(function () {
            var $gridContainer = $(this).closest('.grid-container'),
                cells = $gridContainer.find('.cell'),
                indexOfCellToRemove = null;

            cells.each( function( i ){
                if ( $(this).find('.panel').length < 1 ) {
                    indexOfCellToRemove = i;
                }
            });

            if ( null == indexOfCellToRemove ) {
                $('#no-empty-col-dialog').ppbDialog('open');
                return;
            } else if ( 1 == cells.length ) {
                return;
            } else {
                cells.eq(indexOfCellToRemove).remove();
            }

            var numCells = Math.max( 1, cells.length - 1),
                //Getting old row data
                $old_inputs = $gridContainer.find('input[type=hidden][name^="grids["]'),
                //Getting old row bg color
                bg_color = $old_inputs.filter( '[name$="[style][background]"]').val(),
                //Creating new row with bg color
                $newRow = window.panels.createGrid( numCells, null, {background: bg_color} );

            //Updating old column count
            $old_inputs.filter( '[name$="][cells]"]').val( numCells );
            //Removing all new row data
            $newRow.find('input[type=hidden][name^="grids["]').remove();
            //Putting old row data (with updated column count) in the new row
            $newRow.prepend($old_inputs);

            for( var y = 0; y < cells.length - 1; y++ ) {
                var $newCell = $newRow.find('.cell .cell-wrapper').eq(y),
                    $oldCell = $gridContainer.find('.cell .cell-wrapper').eq(y);

                $newCell.append($oldCell.contents());
            }

            $newRow.insertAfter($gridContainer);
            $('.panels-tooltip').remove();
            $gridContainer.remove();
            $(window).resize();
            panels.ppbGridEvents($newRow);

        });

        $gridContainer.find('> .controls > .duplicate-button').click(function () {

            var rowCount = $('#panels-container .grid-container').length;
            var widgetCount = $('#panels-container .grid-container .panel').length;
            var columnCount = $('#panels-container .grid-container .cell').length;

            var $gridContainer = $(this).closest('.grid-container');
            var $newGridContainer = $gridContainer.clone();
            $newGridContainer.insertAfter($gridContainer);

            // reindex grids[n]...
            $newGridContainer.find('input[type=hidden][name^="grids["]').each(function () {
                var first = 'grids[';
                var idx = $(this).attr('name').indexOf(']');
                if (idx >= 0) {
                    var last = $(this).attr('name').substr(idx);

                    var newName = first + rowCount + last;

                    $(this).attr('name', newName);

                }
            });

            // reindex widgets[n]...
            $newGridContainer.find('.panel').each(function () {

                $(this).find('input[type=hidden][name^="widgets["]').each(function () {
                    var first = 'widgets[';
                    var idx = $(this).attr('name').indexOf(']');
                    if (idx >= 0) {
                        var last = $(this).attr('name').substr(idx);

                        var newName = first + widgetCount + last;

                        $(this).attr('name', newName);

                    }
                });

                $(this).find('input[type=hidden][name="widgets[' + widgetCount + '][info][id]"]').val(widgetCount);
                $(this).find('input[type=hidden][name="panel_order[]"]').val(widgetCount);

                ++widgetCount;
            });

            // reindex grid_cells[n]...
            $newGridContainer.find('.cell').each(function () {
                $(this).find('input[type=hidden][name^="grid_cells["]').each(function () {
                    var first = 'grid_cells[';
                    var idx = $(this).attr('name').indexOf(']');
                    if (idx >= 0) {
                        var last = $(this).attr('name').substr(idx);

                        var newName = first + columnCount + last;

                        $(this).attr('name', newName);

                    }
                });

                ++columnCount;
            });

            $('#panels-container .grid-container').each(function () {
                $(this).find('.cell').each(function () {
                    // Store which grid this is in by finding the index of the closest .grid-container
                    $(this).find('input[name$="[grid]"]').val($('#panels-container .grid-container').index($(this).closest('.grid-container')));
                });

            });

            $newGridContainer.find('.ui-resizable-handle').remove();

            panels.setupGrid($newGridContainer);

            panels.setupGridButtons($newGridContainer);

            // setup add widget icons click handler
            $newGridContainer.find('.cell-wrapper .add-content-button').each(function () {
                $(this).click(function () {
                    panels.removePaddingAnimated( $(this).closest('.grid-container') );

                    panels.addContentModule(this);
                });
            });

            $newGridContainer.find('> .grid > .cell').each(function () {
                $(this).find('> .cell-wrapper > .panel').each(function () {
                    var $panel = $(this);
                    panels.setupPanelButtons($panel);
                });
            });

            panels.ppbGridEvents($newGridContainer);

        });

        $gridContainer.find('> .controls > .style-button').click(function () {
            // This is where we need to handle the new style dialog
            var $container = $(this).closest('.grid-container');
            panels.loadStyleValues($container);
        });

        $gridContainer.find('> .controls > .delete-button').click(function () {

            var that = this;

            var $container = $(this).closest('.grid-container');

            $('#remove-row-dialog').ppbDialog({
                dialogClass: 'panels-admin-dialog',
                autoOpen: true,
                title: $('#remove-row-dialog').attr('data-title'),
                buttons: {
                    Yes: function () {

                        $(that).removeTooltip();

                        // Create an array that represents this grid
                        var containerData = [];

                        $container.find('.cell').each(function (i, el) {
                            containerData[i] = {
                                'weight': Number($(this).attr('data-percent')),
                                'widgets': []
                            };
                            $(this).find('.panel').each(function (j, el) {
                                containerData[i]['widgets'][j] = {
                                    type: $(this).attr('data-type'),
                                    data: $(this).panelsGetPanelData()
                                }
                            })
                        });

                        var styleData = {};
                        $container.find('> [data-style-field]').each(function () {
                            var fieldName = $(this).attr('data-style-field');
                            var fieldValue = $(this).val();
                            styleData[fieldName] = fieldValue;
                        });

                        // Register this with the undo manager
                        window.panels.undoManager.register(
                            that,
                            function (containerData, position, styleData) {
                                // Read the grid
                                var weights = [];
                                for (var i = 0; i < containerData.length; i++) {
                                    weights[i] = containerData[i].weight;
                                }

                                var gridContainer = window.panels.createGrid(weights.length, weights);

                                // apply back style
                                for (var fieldName in styleData) {
                                    if (styleData.hasOwnProperty(fieldName)) {
                                        gridContainer.find('> [data-style-field=' + fieldName + ']').val(styleData[fieldName]);
                                    }
                                }

                                // Now, start adding the widgets
                                for (var i = 0; i < containerData.length; i++) {
                                    for (var j = 0; j < containerData[i].widgets.length; j++) {
                                        // Readd the panel
                                        var theWidget = containerData[i].widgets[j];
                                        var panel = panelsCreatePanel(theWidget.type, theWidget.data);
                                        window.panels.addPanel(panel, gridContainer.find('.panels-container').eq(i));
                                    }
                                }

                                // Finally, reposition the gridContainer
                                if (position != gridContainer.index()) {
                                    var current = $('#panels-container .grid-container').eq(position);
                                    if (current.length) {
                                        gridContainer.insertBefore(current);
                                        $('#panels-container').sortable("refresh")
                                        $('#panels-container').find('.cell').each(function () {
                                            // Store which grid this is in by finding the index of the closest .grid-container
                                            $(this).find('input[name$="[grid]"]').val($('#panels-container .grid-container').index($(this).closest('.grid-container')));
                                        });

                                        $('#panels-container .panels-container').trigger('refreshcells');
                                    }
                                }

                                // We don't want to animate the new widgets
                                $('#panels-container .panel').removeClass('new-panel');

                                if (panels.animations) gridContainer.hide().slideDown();
                                else gridContainer.show();

                            },
                            [containerData, $container.index(), styleData],
                            'Remove Columns'
                        );

                        // Create the undo notification
                        $('#panels-undo-message').remove();
                        $('<div id="panels-undo-message" class="updated"><p>' + panels.i10n.messages.deleteColumns + '. <a href="#" class="undo">' + panels.i10n.buttons.undo + '</a></p></div>')
                            .appendTo('body')
                            .hide()
                            .fadeIn()
                            .find('a.undo')
                            .click(function () {
                                window.panels.undoManager.undo();
                                $('#panels-undo-message').fadeOut(function () {
                                    $(this).remove()
                                });
                                return false;
                            })
                        ;

                        // Finally, remove the grid container
                        var remove = function () {
                            // Remove the container
                            $container.remove();

                            // Refresh everything
                            $('#panels-container')
                                .sortable("refresh")
                                .find('.panels-container').trigger('refreshcells');
                        };

                        if (panels.animations) $container.slideUp(remove);
                        else {
                            $container.hide();
                            remove();
                        }

                        panels.checkAddRowButtonColor( true );

                        $(this).ppbDialog('close');
                    },
                    Cancel: function () {
                        $(this).ppbDialog('close');
                    }
                }


            })


        })
    };

    panels.gridDataFromGrid = function ($t) {
        $t.find('input[type=hidden][name^="grids["]').each(function () {
            var first = 'grids[';
            var idx = $(this).attr('name').indexOf(']');
            if (idx >= 0) {
                var last = $(this).attr('name').substr(idx);

                var newName = first + rowCount + last;

                $(this).attr('name', newName);

            }
        });
    };

    /**
     * Setup a grid container after its been created.
     *
     * @param $$
     * @since 0.1.0
     */
    panels.setupGrid = function ($$) {
        // Hide the undo message
        $('#panels-undo-message').fadeOut(function () {
            $(this).remove()
        });

        $$.panelsResizeCells();

        $$.find('.grid .cell').not('.first').each(function () {

            var $gridContainer = $(this).closest('.grid-container');

            var sharedCellWidth, sharedCellLeft;

            $(this).resizable({
                handles: 'w',
                containment: 'parent',
                start: function (event, ui) {
                    sharedCellWidth = $(this).prev().outerWidth();
                    sharedCellLeft = $(this).prev().position().left;
                },
                stop: function (event, ui) {
                    $gridContainer.find('.grid .cell').not('.first').resizable('disable').resizable('enable');
                },
                resize: function (event, ui) {
                    var c = $(this);
                    var p = $(this).prev();

                    p.css('width', c.position().left - p.position().left - 12);

                    var totalWidth = 0;
                    $gridContainer.find('.grid .cell')
                        .each(function () {
                            totalWidth += $(this).width();
                        })
                        .each(function () {
                            var percent = $(this).width() / totalWidth,
                                $t = $(this);
                            $t.find('.cell-width-value span')
                              .html(Math.round(percent * 1000) / 10 + '%');
                            $t
                                .attr('data-percent', percent)
                                .removeClass('small-cell')
                                .find('input[name$="[weight]"]').val(percent);
                            if ( 0.25 > percent ) {
                                $t.addClass('small-cell');
                            }
                        });

                    $gridContainer.panelsResizeCells();
                }
            });
        });

        // Enable double clicking on the resizer
        $$.find('.grid .cell .ui-resizable-handle').dblclick(function () {

            var $gridContainer = $(this).closest('.grid-container');

            var c1 = $(this).closest('.cell');
            var c2 = c1.prev();
            var totalPercent = Number(c1.attr('data-percent')) + Number(c2.attr('data-percent'));
            c1.attr('data-percent', totalPercent / 2).find('input[name$="[weight]"]').val(totalPercent / 2);
            c2.attr('data-percent', totalPercent / 2).find('input[name$="[weight]"]').val(totalPercent / 2);
            c1.add(c2).find('.cell-width-value span').html(Math.round(totalPercent / 2 * 1000) / 10 + '%');
            $gridContainer.panelsResizeCells();

            return false;
        });

        $$.find('.grid .cell')
            .click(function () {
                $('.grid .cell').removeClass('cell-selected');
                $(this).addClass('cell-selected');
            })
            .each(function () {
                var percent = Number($(this).attr('data-percent'));
                $(this).find('.cell-width-value span').html(Math.round(percent * 1000) / 10 + '%');
            })
            .find('.panels-container')
            // Handles the content blocks sorting
            .sortable({
                placeholder: "ui-state-highlight",
                connectWith: ".panels-container",
                items: '> .panel',
                tolerance: 'pointer',
                change: function (ui) {
                    var thisContainer = $('#panels-container .ui-state-highlight').closest('.cell').get(0);
                    if (typeof this.lastContainer != 'undefined' && this.lastContainer != thisContainer) {
                        // Resize the new and the last containers
                        $(this.lastContainer).closest('.grid-container').panelsResizeCells();
                        $(thisContainer).closest('.grid-container').panelsResizeCells();
                        thisContainer.click();
                    }

                    // Refresh all the cell sizes after we stop sorting
                    this.lastContainer = thisContainer;
                },
                sort: function( e, ui ){
                    var $target = $(e.target);
                    if (!/html|body/i.test($target.offsetParent()[0].tagName)) {
                        var top = e.pageY - $target.offsetParent().offset().top - (ui.helper.outerHeight(true) / 2);
                        ui.helper.css({'top' : top + 'px'});
                    }
                },
                helper: function (e, el) {
                    return el.clone().css('opacity', panels.animations ? 0.9 : 1).addClass('panel-being-dragged');
                },
                stop: function (ui, el) {
                    $('#panels-container .grid-container').each(function () {
                        $(this).panelsResizeCells();
                    });
                },
                receive: function () {
                    $(this).trigger('refreshcells');
                }
            })
            .bind('refreshcells', function () {
                // Set the cell for each panel
                $('#panels-container .panel').each(function () {
                    var container = $(this).closest('.grid-container');
                    $(this).find('input[name$="[info][grid]"]').val($('#panels-container .grid-container').index(container));
                    $(this).find('input[name$="[info][cell]"]').val(container.find('.cell').index($(this).closest('.cell')));

                    var dataJson = $(this).find('input[type=hidden][name$="[data]"]').val();
                    if (dataJson != null && dataJson != '') {
                        var dataObject = JSON.parse(dataJson);

                        if (typeof dataObject.info == 'undefined') {
                            dataObject.info = {};
                        }
                        dataObject.info.raw = $(this).find('input[name$="[info][raw]"]').val();
                        dataObject.info.grid = $(this).find('input[name$="[info][grid]"]').val();
                        dataObject.info.cell = $(this).find('input[name$="[info][cell]"]').val();
                        dataObject.info.id = $(this).find('input[name$="[info][id]"]').val();
                        dataObject.info.class = $(this).find('input[name$="[info][class]"]').val();

                        // serialize back data
                        dataJson = JSON.stringify(dataObject);
                        $(this).find('input[type=hidden][name$="[data]"]').val(dataJson);

                    }
                });

                $('#panels-container .cell').each(function () {
                    $(this).find('input[name$="[grid]"]').val($('#panels-container .grid-container').index($(this).closest('.grid-container')));
                });
            });
    };

    /**
     * Clears all the grids
     * @since 0.1.0
     */
    panels.clearGrids = function () {
        $('#panels-container .grid-container').remove();
    };

    /**
     * Add the content module in the cell
     * @since 0.1.0
     */
    panels.addContentModule = function (tis) {

        var $t = $(tis);

        $('.cell').removeClass('cell-selected')
        $t.closest('.cell').addClass('cell-selected')

        var panel = panelsCreatePanel('Pootle_PB_Content_Block');
        panels.addPanel(panel, null, null, true);

    };

    panels.checkAddRowButtonColor = function ( delay ) {

        if( delay ) {
            delay = 1;
        } else {
            delay = 0;
        }

        if( $( '#panels-container .grid-container').length < 1 + delay ) {

            $('#add-to-pb-panel  .grid-add').addClass('pootle');
            $('#ppb-hello-user').show();
        } else {

            $('#add-to-pb-panel  .grid-add').removeClass('pootle');
            $('#ppb-hello-user').hide();
        }

    };

    $(document).ready( function(){
        panels.checkAddRowButtonColor();
    } )

})(jQuery);