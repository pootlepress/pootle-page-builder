/**
 * Plugin front end scripts
 *
 * @package Pootle_Page_Builder_Live_Editor
 * @version 1.0.0
 * @developer shramee <shramee.srivastav@gmail.com>
 */
/**
 * Moves the elements in array
 * @param oldI
 * @param newI
 * @returns {Array}
 */
Array.prototype.ppbPrevuMove = function (oldI, newI) {
	this.splice(newI, 0, this.splice(oldI, 1)[0]);
	return this;
};
ppbIpad = {};
logPPBData = function ( a, b, c ) {

	//Comment the code below to log console
	if ( 'undefined' == typeof ppbPrevuDebug || ! ppbPrevuDebug ) return;

	if ( a ) { console.log( a ); }

	var log = {
			content : [],
			cells : [],
			rows : []
		},
		$ = jQuery;

	$.each( ppbData.widgets, function ( i, v ) {
		if ( ! v || ! v.info ) {
			log.content.push( 'Content ' + i + ' undefined info' );
		} else {
			log.content.push( 'Content ' + i + ' in Grid: ' + v.info.grid + ' Cell: ' + v.info.cell + ' Text: \'' + $( v.text ).text().substring( 0, 16 ) + "'" );
		}
	} );
	$.each( ppbData.grid_cells, function ( i, v ) {
		log.cells.push( 'Cell ' + i + ' in Grid: ' + v.grid + ' with Weight: ' + v.weight );
	} );
	$.each( ppbData.grids, function ( i, v ) {
		if ( ! v.style ) {
			log.rows.push( 'Row ' + i + ' original id' + v.id + ' Contains: ' + v.cells + ' cells' + ' with Styles undefined' );
		} else {
			log.rows.push( 'Row ' + i + ' original id' + v.id + ' Contains: ' + v.cells + ' cells' + ' with BG: ' + v.style.background + ' ' + v.style.background_image );
		}
	} );

	if ( log.hasOwnProperty( c ) ) {
		console.log( log[c] );
	} else {
		console.log( log );
	}

	if ( b ) { console.log( b ); }
};

jQuery( function ( $ ) {
	$.each( ppbData.grids, function ( i, v ) {
		ppbData.grids[ i ].id = i;
	} );
	$.each( ppbData.grid_cells, function ( i, v ) {
		ppbData.grid_cells[ i ].id = i;
	} );

	$.fn.prevuRowInit = function () {
		var $t = $( this );
		$t.find('.panel-grid-cell-container > .panel-grid-cell').sortable( prevu.contentSortable );
		$t.find('.panel-grid-cell-container > .panel-grid-cell').resizable( prevu.resizableCells );
		tinymce.init( prevu.tmce );
		$ppb.sortable( "refresh" );
	};

	var $contentPanel = $( '#pootlepb-content-editor-panel' ),
		$rowPanel = $( '#pootlepb-row-editor-panel' ),
		$addRowDialog = $( '#pootlepb-add-row' ),
		$setTitleDialog = $( '#pootlepb-set-title' ),
		$ppbIpadColorDialog = $('#ppb-ipad-color-picker'),
		$ppb = $( '#pootle-page-builder' ),
		dialogAttr = {
			dialogClass : 'ppb-cool-panel',
			autoOpen : false,
			draggable : false,
			resizable : false,
			title : 'Edit content block',
			height : $( window ).height() - 50,
			width : $( window ).width() - 50,
			buttons : {
				Done : function () {}
			}
		};

	prevu = {
		noRedirect: false,
		debug: true,
		unSavedChanges: false,

		syncAjax : function () {
			return jQuery.post( ppbAjax.url, ppbAjax, function ( response ) {
				console.log( response );
				var $response = $( $.parseHTML( response ) );
				if ( 'function' == typeof prevu.ajaxCallback ) {
					prevu.ajaxCallback( $response, ppbAjax );
					ppbCorrectOnResize();
				}
				$( 'style#pootle-live-editor-styles' ).html( $response.find( 'style#pootle-live-editor-styles' ).html() );
				if ( ppbAjax.publish ) {
					prevu.unSavedChanges = false;
					if( ! prevu.noRedirect )
					window.location = response;
				}
				ppbAjax.publish = 0;
			} );
		},

		sync : function ( callback, publish ) {
			prevu.ajaxCallback = callback;
			prevu.unSavedChanges = true;
			prevu.saveTmceBlock( $( '.mce-edit-focus' ) );
			ppbAjax.data = ppbData;

			if ( publish ) {
				ppbAjax.publish = publish;
				if ( ppbAjax.title ) {
					var butt = {};
					butt[ publish ] = function () {
						ppbAjax.title = $( '#ppble-live-page-title' ).val();
						prevu.syncAjax();
					};
					$setTitleDialog.parent().attr( 'data-action', publish );
					$setTitleDialog.ppbDialog( 'open' );
					$setTitleDialog.ppbDialog( 'option', 'buttons', butt );
					return;
				}
			} else {
				delete ppbAjax.publish;
			}
			prevu.syncAjax();
		},

		reset : function( nosort ) {
			var allIDs = {}, remove = [];

			if ( ! nosort ) {
				prevu.resort();
			}

			$.each( ppbData.widgets, function ( i, v ) {
				if ( v && v.info ) {
					var $t = $( '.ppb-edit-block[data-i_bkp="' + v.info.id + '"]' ),
						$p = $t.closest( '.ppb-block' ),
						id = 'panel-' + ppbAjax.post + '-' + v.info.grid + '-' + v.info.cell + '-';

					for ( var loopI = 0; loopI < 25; loopI ++ ) {
						if ( ! allIDs.hasOwnProperty( id + loopI ) ) {
							allIDs[ id + loopI ] = 1;
							id = id + loopI ;
							break;
						}
					}
					$t.data( 'index', i ).attr( 'data-index', i );
					$p.attr( 'id', id );
					ppbData.widgets[ i ].info.id = i;
				} else {
					remove.push( i );
				}
			} );

			$.each( remove, function ( i,v ) {
				delete ppbData.widgets[ v ];
			} );

			$.each( ppbData.grids, function ( i, v ) {
				var $t = $( '.ppb-edit-row[data-i_bkp="' + v.id + '"]' ),
					$p = $t.closest( '.ppb-row' ),
					id = 'pg-' + ppbAjax.post + '-';
				$t.data( 'index', i ).attr( 'data-index', i );
				$p.attr( 'id', 'pg-' + ppbAjax.post + '-' + i );
				allIDs[id + i] = 1;
				ppbData.grids[ i ].id = i;
			} );

			$.each( ppbData.grid_cells, function ( i, v ) {
				var gi;
				if ( v.hasOwnProperty( 'old_grid' ) ) {
					gi = v.old_grid; delete v.old_grid;
				} else {
					gi = v.grid;
				}

				var id = 'pgc-' + ppbAjax.post + '-',
					old_id = id + gi + '-', $p;
				id += v.grid + '-';

				for ( var loopI = 0; loopI < 25; loopI ++ ) {
					if ( ! allIDs.hasOwnProperty( id + loopI ) ) {
						id += loopI;
						allIDs[ id ] = 1;
						break;
					}
				}

				old_id += loopI;

				$p = $( '#' + old_id );

				$p.data( 'newID', id );
				ppbData.grid_cells[ i ].id = i;
			} );

			$('.ppb-live-edit-object').each( function() {
				var $t = $( this ),
					i = $t.data('index');
				$t.data( 'i_bkp', i ).attr( 'data-i_bkp', i );
			} );

			$('.ppb-col').each( function() {
				var $t = $(this ),
					id = $t.data( 'newID' );
				$( this ).attr( 'id', id );
				$t.removeData( 'newID' );
			} );
		},

		resort : function() {
			ppbData.widgets.sort( function ( a, b ) {
				if ( ! a.info ) { return 1 }
				if ( ! b.info ) { return - 1 }
				var ag = parseInt( a.info.grid ),
					ac = parseInt( a.info.cell ),
					ai = parseInt( a.info.id ),
					bg = parseInt( b.info.grid ),
					bc = parseInt( b.info.cell ),
					bi = parseInt( b.info.id );
				return ( ag*10000 + ac*1000 + ai ) - ( bg*10000 + bc*1000 + bi );
			} );
			ppbData.grid_cells.sort( function ( a, b ) {
				var ag = parseInt( a.grid ),
					ai = parseInt( a.id ),
					bg = parseInt( b.grid ),
					bi = parseInt( b.id );
				return ( ag*100 + ai ) - ( bg*100 + bi );
			} );
			prevu.unSavedChanges = true;
		},

		rowBgToggle : function () {
			var $t = $rowPanel.find('[data-style-field=background_toggle]');
			$('.bg_section').hide();
			$($t.val()).show();
		},

		editPanel : function () {
			if ( 'undefined' == typeof ppbData.widgets[window.ppbPanelI] ) {
				return;
			}

			// Add event handlers
			panels.addInputFieldEventHandlers( $contentPanel );

			var dt = ppbData.widgets[window.ppbPanelI],
				st = JSON.parse( dt.info.style );

			panels.setStylesToFields( $contentPanel, st );

			tinyMCE.get( 'ppbeditor' ).setContent( dt.text );

			$( 'html' ).trigger( 'pootlepb_admin_editor_panel_done', [$contentPanel, st] );
		},

		savePanel : function () {
			var st = JSON.parse( ppbData.widgets[window.ppbPanelI].info.style );

			st = panels.getStylesFromFields( $contentPanel, st );

			ppbData.widgets[window.ppbPanelI].text = tinyMCE.get( 'ppbeditor' ).getContent();
			ppbData.widgets[window.ppbPanelI].info.style = JSON.stringify( st );
			prevu.sync( function ( $r, qry ) {
				var info = ppbData.widgets[window.ppbPanelI].info,
					$t = $( '.ppb-block.active' ),
					id = $t.attr( 'id' ),
					$blk = $r.find( '#' + id ),
					style = $blk.closest( '.panel-grid-cell' ).children( 'style' ).html(),
					$cell = $t.closest( '.panel-grid-cell' );

				$blk.addClass( 'pootle-live-editor-new-content-block' );

				$t.replaceWith( $blk );

				$blk = $( '.pootle-live-editor-new-content-block' );
				$( 'html' ).trigger( 'pootlepb_le_content_updated', [$blk] );
				$blk.removeClass( 'pootle-live-editor-new-content-block' );


				if ( $cell.children( 'style' ).length ) {
					$cell.children( 'style' ).html( style );
				} else if ( style ) {
					var $style = $( '<style>' ).html( style );
					$cell.prepend( $style );
				}

				tinymce.init( prevu.tmce );
			} );

			$contentPanel.ppbDialog( 'close' );
		},

		editRow : function () {
			var $bgToggle = $rowPanel.find('[data-style-field=background_toggle]');
			prevu.rowBgToggle();
			$bgToggle.on('change', prevu.rowBgToggle);


			if ( 'undefined' == typeof ppbData.grids[window.ppbRowI] ) {
				return;
			}

			var dt = ppbData.grids[window.ppbRowI],
				st = dt.style;
			$rowPanel.find( '[data-style-field]' ).each( function () {
				var $t = $( this ),
					key = $t.attr( 'data-style-field' );

				if ( 'undefined' == typeof st[key] ) {
					st[key] = '';
				}

				if ( $t.attr( 'type' ) == "checkbox" ) {
					if ( st[key] ) $t.prop( 'checked', true );
				} else if ( $t.attr( 'data-style-field-type' ) == 'slider' ) {
					$t.siblings( '.ppb-slider' ).slider( 'value', st[key] );
				} else if ( $t.attr( 'data-style-field-type' ) == 'color' ) {
					$t.wpColorPicker( 'color', st[key] );
				} else {
					$t.val( st[key] );
				}
				$t.change();
			} );
		},

		saveRow : function () {
			var dt = ppbData.grids[window.ppbRowI],
				st = ppbData.grids[window.ppbRowI].style;

			$rowPanel.find( '[data-style-field]' ).each( function () {
				var $t = $( this ),
					key = $t.attr( 'data-style-field' );

				if ( $t.attr( 'type' ) == "checkbox" ) {
					st[key] = '';
					if ( $t.prop( 'checked' ) ) st[key] = 1;
					$t.prop( 'checked', false );
				} else {
					st[key] = $t.val();
					$t.val( '' );
				}
				$t.change();
			} );

			ppbData.grids[window.ppbRowI].style = st;
			prevu.sync( function ( $r, qry ) {
				var id = '#pg-' + qry.post + '-' + window.ppbRowI,
					$ro = $r.find( id );

				$ro.addClass( 'pootle-live-editor-new-content-block' );

				$( id ).replaceWith( $ro );

				$ro = $( '.pootle-live-editor-new-content-block' );
				$( 'html' ).trigger( 'pootlepb_le_content_updated', [$ro] );
				$ro.removeClass( 'pootle-live-editor-new-cell' );


				$( id ).prevuRowInit();
			} );
			$rowPanel.ppbDialog( 'close' );
		},

		addRow : function ( e, ui ) {
			window.ppbRowI = ppbData.grids.length;
			var num_cells;

			logPPBData( 'Adding row' );

			num_cells = parseInt( $('#ppb-row-add-cols' ).val() );
			var row = {
				id: window.ppbRowI,
				cells: num_cells,
				style: { background: "", background_image: "", background_image_repeat: "", background_image_size: "cover", background_parallax: "", background_toggle: "", bg_color_wrap: "", bg_image_wrap: "", bg_mobile_image: "", bg_overlay_color: "", bg_overlay_opacity: "0.5", bg_video: "", bg_video_wrap: "", bg_wrap_close: "", class: "", col_class: "", col_gutter: "1", full_width: "", hide_row: "", margin_bottom: "0", margin_top: "0", row_height: "0", style: ""}
			}, cells, block;

			ppbData.grids.push( row );

			cells = {
				grid: window.ppbRowI,
				weight: ( 1 / row.cells )
			};

			block = {
				text : '<h2>Hi there,</h2><p>I am a new content block, go ahead, edit me and make me cool...</p>',
				info : {
					class: 'Pootle_PB_Content_Block',
					grid: window.ppbRowI,
					style: '{"background-color":"","background-transparency":"","text-color":"","border-width":"","border-color":"","padding":"","rounded-corners":"","inline-css":"","class":"","wc_prods-add":"","wc_prods-attribute":"","wc_prods-filter":null,"wc_prods-ids":null,"wc_prods-category":null,"wc_prods-per_page":"","wc_prods-columns":"","wc_prods-orderby":"","wc_prods-order":""}'
				}
			};

			for ( var i = 0; i < row.cells; i++ ) {
				var id = ppbData.grid_cells.length;
				cells.id = id;
				ppbData.grid_cells.push( $.extend( true, {}, cells ) );

				id = ppbData.widgets.length;
				block.info.cell = i;
				block.info.id = id;
				ppbData.widgets.push( $.extend( true, {}, block ) );
			}

			logPPBData( 'Row added' );

			$addRowDialog.ppbDialog( 'close' );

			prevu.sync( function( $r, qry ) {
				var $ro = $r.find( '#pg-' + qry.post + '-' + window.ppbRowI ),
					$cols = $ro.find( '.panel-grid-cell-container > .panel-grid-cell' );
				$cols.css( 'width', ( 100 - num_cells + 1 ) / num_cells + '%' );
				$('.pootle-live-editor.add-row' ).before( $ro );
				$ro.prevuRowInit();

			} );
		},

		rowsSortable : {
			items: "> .panel-grid",
			handle: ".ppb-edit-row .dashicons-before:first",
			start: function ( e, ui ) {
				$(this).data('draggingRowI', ui.item.index());
			},
			update: function ( e, ui ) {
				var $t = $( this ),
					olI = $t.data('draggingRowI'),
					newI = ui.item.index(),
					diff = -1;

				if ( newI == olI ) { return; }

				ppbData.grids.ppbPrevuMove( olI, newI );

				var range = [ olI, newI ].sort( function ( a, b ) { return a - b } );

				if ( newI < olI ) {
					diff = 1
				}

				$.each( ppbData.widgets, function ( i, v ) {
					if ( v && v.info ) {
						var gi = parseInt( v.info.grid );
						if ( range[0] <= gi && range[1] >= gi ) {
							if ( gi == olI ) {
								ppbData.widgets[ i ].info.grid = newI;
							} else {
								ppbData.widgets[ i ].info.grid = gi + diff;
							}
						}
					}
				} );

				$.each( ppbData.grid_cells, function ( i,v ) {
					if ( v ) {
						var gi = parseInt( v.grid );
						ppbData.grid_cells[ i ].old_grid = gi;
						if ( range[0] <= gi && range[1] >= gi ) {
							if ( gi == olI ) {
								ppbData.grid_cells[ i ].grid = newI;
							} else {
								ppbData.grid_cells[ i ].grid = gi + diff;
							}
						}
					}
				} );

				prevu.resort();
				prevu.sync( function() { prevu.reset( 'noSort' ) } );

				logPPBData( 'Moved row ' + olI + ' => ' + newI );
			}
		},

		contentSortable : {
			tolerance: 'pointer',
			connectWith: '.panel-grid-cell',
			handle: '.ppb-edit-block .dashicons-before:first',
			items: '> .ppb-block',
			helper: function (e, el) {
				el.parents().css( 'z-index', 999 );
				return el.clone().css('opacity', 0.7 ).addClass('panel-being-dragged');
			},
			start: function ( e, ui ) {
				var $t = ui.item,
					index = $ppb.find( '.ppb-block' ).index( $t );
				$t.data( 'index', index );
			},
			update: function (e, ui) {
				if( ui.item.parent().attr('id') != $( this ).attr('id') ) return;
				var $t = ui.item,
					olI = $t.data('index'),
					nuI = $ppb.find( '.ppb-block' ).index( $t ),
					gi = $t.closest( '.ppb-row' ).siblings('.ppb-edit-row').data( 'index' ),
					ci = $t.closest( '.panel-grid-cell' ).index();

				$t.siblings( '.add-content' ).appendTo( $t.parent() );
				$t.parents().css( 'z-index', '' );

				logPPBData( 0, 0, 'content' );

				ppbData.widgets[ olI ].info.grid = gi;
				ppbData.widgets[ olI ].info.cell = ci;
//				ppbData.widgets[ olI ].info.id = nuI;
				ppbData.widgets.ppbPrevuMove( olI, nuI );
				logPPBData( 0, 0, 'content' );
				prevu.reset( 'noResort' );
				prevu.resort();
				logPPBData( 0, 0, 'content' );
			}
		},

		resizableCells : {
			handles: 'w',
			stop: function (event, ui) {
				$( this ).parent().removeClass( 'ppb-cols-resizing' );
			},
			resize: function (event, ui) {
				var $t = $( this ),
					$prev = $t.prev(),
					widthTaken = 0,
					widthNow = ui.size.width,
					originalWidth = ui.originalSize.width;

				$t.parent().addClass( 'ppb-cols-resizing' );

				$prev.siblings('.panel-grid-cell' ).each( function() {
					var $t = $( this );
					widthTaken += 1 + $t.outerWidth() + parseInt( $t.css('margin-left') ) + parseInt( $t.css('margin-right') );
				} );

				widthTaken += parseInt( $prev.css('padding-left') ) + parseInt( $prev.css('margin-left') ) +
				              parseInt( $prev.css('padding-right') ) + parseInt( $prev.css('margin-right') );

				$prev.css(
					'width',
					( $t.parent().width() - widthTaken - 1 )
				);

				prevu.resizableCells.correctCellData( $t  );
				prevu.resizableCells.correctCellData( $prev  );

				prevu.unSavedChanges = true;

				if ( originalWidth < widthNow ) {
					//Increasing width
					if ( $t.parent().width() * 0.93 < widthTaken ) {
						$t.resizable( 'widget' ).trigger( 'mouseup' );
					}
				} else {
					//Decreasing width
					if ( $t.parent().width() * 0.07 > $t.width() ) {
						$t.resizable( 'widget' ).trigger( 'mouseup' );
					}
				}
			},
			correctCellData: function ( $t ) {
				var width = $t.outerWidth(),
					pWidth = $t.parent().width() + 1,
					i = $('.panel-grid-cell-container > .panel-grid-cell' ).index( $t ),
					weight = Math.floor( 10000 * width / pWidth ) / 10000;

				$t.find('.pootle-live-editor.resize-cells' ).html('<div class="weight">' + (Math.round( 1000 * weight ) / 10) + '%</div>');

				ppbData.grid_cells[i].weight = weight;
				return weight;
			}
		},

		insertImage : function( $t ) {
			// If the media frame already exists, reopen it.
			if (prevu.insertImageFrame) {
				prevu.insertImageFrame.open();
				return;
			}

			// Create the media frame.
			prevu.insertImageFrame = wp.media({
				library: { type: 'image' },
				displaySettings: true,
				displayUserSettings: false,
				title: 'Choose Image',
				button: {text: 'Insert in Content Block'},
				multiple: false
			});
			prevu.insertImageFrame.on( 'attach', function() {
				$( '.setting[data-setting="url"]' ).before(
					'<label class="setting" data-setting="url">' +
					'<span class="name">Size</span>' +
					'<input type="text" value="http://wp/ppb/wp-content/uploads/2016/02/p03hbzwm.jpg" readonly="">' +
					'</label>'
				);
			} );
			// When an image is selected, run a callback.
			prevu.insertImageFrame.on('select', function () {
				// We set multiple to false so only get one image from the uploader
				attachment = prevu.insertImageFrame.state().get('selection').first().toJSON();

				// Do something with attachment.id and/or attachment.url here
				console.log( attachment );
				var $img = $( '<img>' ).attr( 'src', attachment.url );

				$t.append( $( '<p>' ).html( $img ) );
				tinyMCE.triggerSave();

			});

			// Finally, open the modal
			prevu.insertImageFrame.open();
		},
		saveTmceBlock : function ( $ed ) {
			if ( ! $ed || ! $ed.length ) return;
			var blockI = $ed.siblings( '.pootle-live-editor' ).data( 'index' );
			console.log( blockI );
			ppbData.widgets[blockI].text = $ed.html();
			prevu.unSavedChanges = true;
		},
		tmce : $.extend( true, {}, tinyMCEPreInit.mceInit.ppbeditor )
	};

	prevu.showdown = new showdown.Converter();

	dialogAttr.open = prevu.editPanel;
	dialogAttr.buttons.Done = prevu.savePanel;
	$contentPanel.ppbTabs().ppbDialog( dialogAttr );

	dialogAttr.title = 'Edit row';
	dialogAttr.open = prevu.editRow;
	dialogAttr.buttons.Done = prevu.saveRow;
	dialogAttr.height = 520;
	dialogAttr.width = 700;
	$rowPanel.ppbTabs().ppbDialog( dialogAttr );
	panels.addInputFieldEventHandlers( $rowPanel );

	dialogAttr.title = 'Add row';
	dialogAttr.dialogClass = dialogAttr.open = null;
	dialogAttr.buttons.Done = prevu.addRow;
	dialogAttr.height = 250;
	dialogAttr.width = 320;
	$addRowDialog.ppbDialog( dialogAttr );

	dialogAttr.title = 'Set title of the page';
	dialogAttr.buttons.Done = function () {
		ppbAjax.title = $( '#ppble-live-page-title' ).val();
		prevu.syncAjax();
	};
	$setTitleDialog.ppbDialog( dialogAttr );

	$('.panel-grid-cell-container > .panel-grid-cell' ).each( function () {
		prevu.resizableCells.correctCellData( $(this) );
	} );

	$ppb.delegate( '.pootle-live-editor .dashicons-before', 'mousedown', function () {
		$( '.pootle-live-editor-realtime.has-focus' ).blur();
	} );

	$ppb.delegate( '.ppb-edit-row .dashicons-admin-appearance', 'click', function () {
		var $t = $( this );
		window.ppbRowI = $t.closest( '.pootle-live-editor' ).data( 'index' );
		$rowPanel.ppbDialog( 'open' );
	} );

	$ppb.delegate( '.ppb-edit-row .dashicons-admin-page', 'click', function () {
		prevu.reset();
		var $t = $( this ).closest( '.pootle-live-editor' ),
			rowI = $t.data('i' ),
			row = $.extend( true, {}, ppbData.grids[ rowI ] ),
			nuI = rowI + 1,
			cells = [],
			blocks = [];

		window.ppbRowI = $t.closest( '.pootle-live-editor' ).data( 'index' );

		ppbData.grids.splice( rowI, 0, row );

		$.each( ppbData.widgets, function ( i, v ) {
			if ( v && v.info ) {
				blocks.push( $.extend( true, {}, v ) );
				var gi = parseInt( v.info.grid );
				if ( gi == rowI ) {
					var newBlock = $.extend( true, {}, v );
					newBlock.info.grid = nuI;
					blocks.push( newBlock );
				}
			}
		} );

		ppbData.widgets = $.extend( true, [], blocks.sort( function ( a, b ) {
			return a.info.grid - b.info.grid
		} ) );

		$.each( ppbData.grid_cells, function ( i,v ) {
			if ( v ) {
				cells.push( $.extend( true, {}, v ) );
				var gi = parseInt( v.grid );
				if ( gi == rowI ) {
					var newCell = $.extend( true, {}, v );
					newCell.grid = nuI;
					cells.push( newCell );
				}
			}
		} );

		ppbData.grid_cells = $.extend( true, [], cells.sort( function ( a, b ) {
			return a.grid - b.grid
		} ) );

		prevu.sync( function ( $r, qry ) {
			var $ro = $r.find( '#pg-' + qry.post + '-' + window.ppbRowI ),
				$cols = $ro.find( '.panel-grid-cell-container > .panel-grid-cell' );
			$cols.css( 'width', ( 101/$cols.length - 1 ) + '%' );
			$ro.prevuRowInit();
			$t.closest('.panel-grid' ).after( $ro );
		} );

		prevu.reset();

		logPPBData();
	} );

	$ppb.delegate( '.ppb-edit-row .dashicons-no', 'click', function () {
		var removeCells = [], removeBlocks = [],
			$t = $( this ),
			rowI = $t.closest( '.pootle-live-editor' ).data( 'index' );

		ppbData.grids.splice( rowI, 1 );

		$.each( ppbData.widgets, function ( i, v ) {
			if ( v && v.info ) {
				if ( rowI == v.info.grid ) {
					removeBlocks.push( i )
				} else if ( rowI < v.info.grid ) {
					ppbData.widgets[ i ].info.grid--;
				}
			}
		} );

		$.each( ppbData.grid_cells, function ( i,v ) {
			if ( v ) {
				var gi = parseInt( v.grid );
				if ( rowI == gi ) {
					removeCells.push( i )
				} else if ( rowI < gi ) {
					ppbData.grid_cells[ i ].old_grid = gi;
					ppbData.grid_cells[ i ].grid = --gi;
				}
			}
		} );

		//Sort in decending order
		removeBlocks.sort( function ( a, b ) { return b - a } );
		removeCells.sort( function ( a, b ) { return b - a } );

		$.each( removeBlocks, function ( i,v ) {
			ppbData.widgets.splice( v, 1 );
		} );
		$.each( removeCells, function ( i,v ) {
			ppbData.grid_cells.splice( v, 1 );
		} );

		ppbData.grids.filter( function () { return true; } );
		ppbData.widgets.filter( function () { return true; } );
		ppbData.grid_cells.filter( function () { return true; } );

		//Remove row from preview
		$t.closest( '.panel-grid' ).remove();

		prevu.sync( function () { prevu.reset(); } );
	} );

	$ppb.delegate( '.ppb-edit-block .dashicons-edit', 'click', function () {
		var $t = $( this );
		window.ppbPanelI = $t.closest( '.pootle-live-editor' ).data( 'index' );
		$contentPanel.ppbDialog( 'open' );
	} );

	$ppb.delegate( '.ppb-edit-block .dashicons-no', 'click', function () {
		prevu.reset(); // Reset the indices
		var $t = $( this ),
			i = $t.closest( '.pootle-live-editor' ).data( 'index' );

		ppbData.widgets.splice( i, 1 ); // Remove the content block data
		$t.closest( '.ppb-block' ).remove(); // Remove block html element

		prevu.reset(); // Reset the indices again
	} );

	$ppb.delegate( '.ppb-edit-block .pootle-live-editor-addons .pootle-live-editor-addon', 'click', function () {
		var $t = $( this );
		window.ppbPanelI = $t.closest( '.pootle-live-editor' ).data( 'index' );
		console.log( window.ppbPanelI );
		$contentPanel.ppbDialog( 'open' );
		$contentPanel.find( 'a[href="#pootle-' + $t.data( 'id' ) + '-tab"]' ).click();
	} );

	$ppb.delegate( '.ppb-edit-block .dashicons-format-image', 'click', function ( e ) {
		e.preventDefault();
		prevu.activeEditor = $(this).closest('.ppb-block' ).children('.pootle-live-editor-realtime');
		prevu.insertImage( prevu.activeEditor )
	} );

	$ppb.delegate( '.pootle-live-editor.add-row .dashicons-plus', 'click', function () {
		$addRowDialog.ppbDialog( 'open' );
		var $lastRow = $('.panel-grid:last-child');
		if ($lastRow.length) {
			$('html, body').animate({
				scrollTop: $lastRow.height() + $lastRow.offset().top
			}, 1000);
			return false;
		}
	} );

	$ppb.delegate( '.ppb-edit-row .dashicons-editor-code', 'dblclick', function () {
		var $t = $( this );
		window.ppbRowI = $t.closest( '.pootle-live-editor' ).data( 'index' );
		$rowPanel.ppbDialog( 'open' );
	} );


	$ppb.delegate( '.ppb-edit-block .dashicons-screenoptions', 'dblclick', function () {
		var $t = $( this );
		window.ppbPanelI = $t.closest( '.pootle-live-editor' ).data( 'index' );
		$contentPanel.ppbDialog( 'open' );
	} );


	ppbIpad.notice = $( '#ppb-ipad-updated-notice' );

	ppbIpad.AddRow = function () {
		$addRowDialog.ppbDialog( 'open' );

	};
	ppbIpad.StyleRow = function () {
		var $row = $('.panel-grid.active');
		if ( $row.length != 1 ) {
			alert( 'Please select a row by touching any of it\'s content blocks to start editing.' );
			return;
		}
		var $editBar = $row.children('.pootle-live-editor');
		console.log( $editBar.data( 'index' ) );
		window.ppbRowI = $editBar.data( 'index' );
		$rowPanel.ppbDialog( 'open' );
	};
	ppbIpad.StyleContent = function () {
		var $block = $('.ppb-block.active');
		if ( $block.length != 1 ) {
			alert( 'Please select a content block to start editing.' );
			return;
		}
		var $editBar = $block.children('.pootle-live-editor');
		console.log( $editBar.data( 'index' ) );
		window.ppbPanelI = $editBar.data( 'index' );
		$contentPanel.ppbDialog( 'open' );
	};
	ppbIpad.insertImage = function () {
		var $block = $('.ppb-block.active');
		if ( $block.length != 1 ) {
			alert( 'Please select a content block to start editing.' );
			return;
		}
		prevu.activeEditor = $block.children('.pootle-live-editor-realtime');
		prevu.insertImage( prevu.activeEditor )
	};
	ppbIpad.Update = function () {
		prevu.noRedirect = true;
		prevu.ajaxCallback = function () {
			ppbAjax.title = null;
			ppbIpad.notice.show( 0 );
			setTimeout( function () {
				ppbIpad.notice.hide();
			}, 2500 );
		};
		prevu.unSavedChanges = true;
		prevu.saveTmceBlock( $( '.mce-edit-focus' ) );
		ppbAjax.data = ppbData;
		ppbAjax.publish = 'Publish';

		if ( ppbAjax.title ) {
			var butt = [
				{
					text: 'Publish',
					icons: {
						primary: 'ipad-'
					},
					click: function () {
						ppbAjax.publish = 'Publish';
						ppbAjax.title = $( '#ppble-live-page-title' ).val();
						prevu.syncAjax();
						$setTitleDialog.ppbDialog( 'close' );
					}
				},
				{
					text: 'Save Draft',
					click: function () {
						ppbAjax.publish = 'Save Draft';
						ppbAjax.title = $( '#ppble-live-page-title' ).val();
						prevu.syncAjax();
						$setTitleDialog.ppbDialog( 'close' );
					}
				}
			];
			$setTitleDialog.parent().data( 'action', 'Publish' );
			$setTitleDialog.ppbDialog( 'open' );
			$setTitleDialog.ppbDialog( 'option', 'buttons', butt );
			return;
		}

		prevu.syncAjax();
	};

	$ppbIpadColorDialog.delegate( '.ppb-ipad-color-picker span', 'mousedown', function ( e ) {
		e.preventDefault();
		return false;
	} );

	$ppbIpadColorDialog.delegate( '.ppb-ipad-color-picker span', 'click', function ( e ) {
		e.preventDefault();
		console.log( $( this ).data( 'color' ) );
		tinymce.activeEditor.execCommand(
			'ForeColor',
			false,
			$(this).data('color')
		);
		$ppbIpadColorDialog.hide();
	} );

	ppbIpad.format = {
		H1     : function () {
			tinymce.activeEditor.execCommand("mceToggleFormat", false, "h1")
		},
		H2     : function () {
			tinymce.activeEditor.execCommand("mceToggleFormat", false, "h2")
		},
		H3     : function () {
			tinymce.activeEditor.execCommand("mceToggleFormat", false, "h3")
		},
		Quote  : function () {
			tinymce.activeEditor.execCommand('mceBlockQuote')
		},
		Color  : function () {
			$ppbIpadColorDialog.show();
		},
		Link   : function () {
			tinymce.activeEditor.execCommand('WP_Link')
		},
		Bold   : function () {
			tinymce.activeEditor.execCommand('Bold')
		},
		Italic : function () {
			tinymce.activeEditor.execCommand('Italic')
		},
		Left   : function () {
			tinymce.activeEditor.execCommand('JustifyLeft')
		},
		Center : function () {
			tinymce.activeEditor.execCommand('JustifyCenter')
		},
		Right  : function () {
			tinymce.activeEditor.execCommand('JustifyRight')
		}
	};

	$ppb.delegate( '.pootle-live-editor.add-content .dashicons-plus', 'click', function () {

		var $t = $( this ),
			id = $t.closest( '.panel-grid-cell' ).attr( 'id' ),
			data = id.split( '-' );

		$t.closest( '.panel-grid-cell' ).addClass( 'this-cell-is-waiting' );

		ppbData.widgets.push( {
			text : '<h2>Hi there,</h2><p>I am a new content block, go ahead, edit me and make me cool...</p>',
			info : {
				class	: 'Pootle_PB_Content_Block',
				grid	: data[2],
				cell	: data[3],
				style	: '{"background-color":"","background-transparency":"","text-color":"","border-width":"","border-color":"","padding":"","rounded-corners":"","inline-css":"","class":"","wc_prods-add":"","wc_prods-attribute":"","wc_prods-filter":null,"wc_prods-ids":null,"wc_prods-category":null,"wc_prods-per_page":"","wc_prods-columns":"","wc_prods-orderby":"","wc_prods-order":""}'
			}
		} );


		prevu.reset();

		ppbAjax.customData = id;

		prevu.sync( function ( $r, qry ) {
			var $col = $r.find( '#' + ppbAjax.customData );

			$col.addClass( 'pootle-live-editor-new-cell' );

			$( '.this-cell-is-waiting' ).replaceWith( $col );

			$col = $( '.pootle-live-editor-new-cell' );
			$( 'html' ).trigger( 'pootlepb_le_content_updated', [$col] );
			$col.removeClass( 'pootle-live-editor-new-cell' );

			ppbAjax.customData = undefined;
		} );

		prevu.reset();
	} );

	prevu.tmce.selector		= '.pootle-live-editor-realtime:not(.mce-content-body)';
	//prevu.tmce.selector		= '.site-info';
	prevu.tmce.inline		= true;
	prevu.tmce.theme		= 'ppbprevu';

	console.log( prevu.tmce );

	if ( ! ppbAjax.ipad ) {
		prevu.tmce.toolbar = [
			'h1',
			'h2',
			'h3',
			'blockquote',
			'forecolor',
			'link',
			'bold',
			'italic',
			'alignleft',
			'aligncenter',
			'alignright',
		];
	}

	prevu.tmce.content_css	= "http://wp/ppb/wp-includes/css/dashicons.min.css?ver=4.4.2-alpha-36412";
	prevu.tmce.setup	= function(editor) {
		editor.on('change', function(e) {
			prevu.saveTmceBlock( $( e.target.targetElm ) );
		});
		editor.on('focus', function(e) {
			var $t = $( e.target.targetElm );
			$( '.ppb-block.active, .ppb-row.active' ).removeClass( 'active' );
			$t.parents( '.ppb-block, .ppb-row' ).addClass( 'active' );
		});
	};

	tinymce.init( prevu.tmce );

	$ppb.sortable( prevu.rowsSortable );

	$ppb.find('.panel-grid').each( function () {
		$( this ).prevuRowInit();
	} );

	$( '[href="#ppb-live-update-changes"]' ).click( function(){
		prevu.sync( null, 'Save Draft' );
	} );

	$( '[href="#ppb-live-publish-changes"]' ).click( function(){
		prevu.sync( null, 'Publish' );
	} );

	window.onbeforeunload = function(e) {
		if ( prevu.unSavedChanges ) {
			return "You have unsaved changes! Click 'Update' in admin bar to save.\n\nYour changes will be lost if you dan't save.";
		}
	}
} );
