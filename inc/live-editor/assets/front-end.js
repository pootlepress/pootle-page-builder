/**
 * Plugin front end scripts
 *
 * @package Pootle_Page_Builder_Live_Editor
 * @version 1.0.0
 * @developer shramee <shramee@wpdevelopment.me>
 */
/**
 * Moves the elements in array
 * @param oldI
 * @param newI
 * @returns Array
 */
Array.prototype.ppbPrevuMove = function (oldI, newI) {
	this.splice(newI, 0, this.splice(oldI, 1)[0]);
	return this;
};
ppbPrevuDebug = 1;
ppbIpad = {};
logPPBData = function ( a, b, c ) {

	//Comment the code below to log console
	if ( 'undefined' == typeof ppbPrevuDebug || ! ppbPrevuDebug ) return;

	var log = {
			message : a,
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

	$.fn.prevuBlockInit = function () {
		$( this ).each( function () {
			var $t = $( this );
			$t.draggable( prevu.contentDraggable );
			$t.resizable( prevu.contentResizable );
			$t.droppable( prevu.moduleDroppable );
		} );
	};

	$.fn.prevuRowInit = function () {
		var $t = $( this );
		$t.find('.ppb-block').prevuBlockInit();
		$t.find('.panel-grid-cell-container > .panel-grid-cell').resizable( prevu.resizableCells );
		tinymce.init( prevu.tmce );
		$ppb.sortable( "refresh" );
	};

	var $contentPanel = $( '#pootlepb-content-editor-panel' ),
		$rowPanel = $( '#pootlepb-row-editor-panel' ),
		$deleteDialog = $( '#pootlepb-confirm-delete' ),
		$deletingWhat = $( '#pootlepb-deleting-item' ),
		$addRowDialog = $( '#pootlepb-add-row' ),
		$setTitleDialog = $( '#pootlepb-set-title' ),
		$postSettingsDialog = $( '#pootlepb-post-settings' ),
		$ppbIpadColorDialog = $('#ppb-ipad-color-picker'),
		$iconPicker = $( '#ppb-iconpicker' ),
		$ppb = $( '#pootle-page-builder' ),
		$mods = $('#pootlepb-modules-wrap'),
		$body = $('body'),
		$loader = $('#ppb-loading-overlay'),
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

		justClickedEditRow: false,
		justClickedEditBlock: false,
		syncAjax : function () {

			return jQuery.post( ppbAjax.url, ppbAjax, function ( response ) {
				var $response = $( $.parseHTML( response, document, true ) );
				if ( 'function' == typeof prevu.ajaxCallback ) {
					prevu.ajaxCallback( $response, ppbAjax, response );
					ppbCorrectOnResize();
					prevu.ajaxCallback = null;
				}
				$( 'style#pootle-live-editor-styles' ).html( $response.find( 'style#pootle-live-editor-styles' ).html() );
				if ( ppbAjax.publish ) {
					prevu.unSavedChanges = false;
					if( ! prevu.noRedirect ) {
						if ( window.ppbAjaxDebug ) {
							$body.append( '<div class="ajax-debug">' + response + '</div>' );
						} else {
							window.location = response;
						}
					}
				}
				ppbAjax.publish = 0;
			} );
		},

		sync : function ( callback, publish ) {
			logPPBData( 'Before sync' );

			tinyMCE.triggerSave();

			prevu.ajaxCallback = callback;
			prevu.unSavedChanges = true;
			prevu.saveTmceBlock( $( '.mce-edit-focus' ) );
			delete ppbAjax.data;
			ppbAjax.data = ppbData;

			if ( publish ) {
				ppbAjax.publish = publish;
				$body.trigger( 'savingPPB' );
				if ( ppbAjax.title ) {
					var butt = [				{
						text  : publish,
						icons : {
							primary : publish == 'Publish' ? 'ipad-publish' : ''
						},
						click : function () {
							$setTitleDialog.ppbDialog( 'close' );
							prevu.syncAjax();
						}
					}
					];

					butt[ publish ] = function () {

					};
					$setTitleDialog.parent().attr( 'data-action', publish );
					$setTitleDialog.ppbDialog( 'open' );
					$setTitleDialog.ppbDialog( 'option', 'buttons', butt );
					return;
				}
			} else {
				delete ppbAjax.publish;
			}
			logPPBData( 'After sync' );
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
					$rStyle = $p.children( '.panel-row-style' ).children( 'style' ),
					oldIdRegex = new RegExp( $p.attr('id') , "g"),
					id = 'pg-' + ppbAjax.post + '-' + i;
				$t.data( 'index', i ).attr( 'data-index', i );
				$rStyle.html( $rStyle.html().replace( oldIdRegex, id ) );
				$p.attr( 'id', id );
				allIDs[id] = 1;
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

			console.log( window.ppbPanelI );

			// Add event handlers
			panels.addInputFieldEventHandlers( $contentPanel );

			var dt = ppbData.widgets[window.ppbPanelI],
				st = JSON.parse( dt.info.style );

			panels.setStylesToFields( $contentPanel, st );

			tinyMCE.get( 'ppbeditor' ).setContent( dt.text );

			$( 'html' ).trigger( 'pootlepb_admin_editor_panel_done', [$contentPanel, st] );
		},

		savePanel : function () {
			ppbData.widgets[window.ppbPanelI].text = tinyMCE.get( 'ppbeditor' ).getContent();

			var st = JSON.parse( ppbData.widgets[window.ppbPanelI].info.style );

			st = panels.getStylesFromFields( $contentPanel, st );

			ppbData.widgets[window.ppbPanelI].info.style = JSON.stringify( st );

			var $t = $( '.ppb-block.active' );

			prevu.sync( function ( $r, qry ) {
				var id = $t.attr( 'id' ),
					$blk = $r.find( '#' + id ),
					style = $blk.closest( '.panel-grid-cell' ).children( 'style' ).html(),
					$cell = $t.closest( '.panel-grid-cell' );

				console.log( $t, id );

				$blk.addClass( 'pootle-live-editor-new-content-block' );

				$t.replaceWith( $blk );

				$blk = $( '.pootle-live-editor-new-content-block' );
				$( 'html' ).trigger( 'pootlepb_le_content_updated', [$blk] );
				$blk
					.removeClass( 'pootle-live-editor-new-content-block' )
					.addClass( 'active' )
					.prevuBlockInit();

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

				$ro.addClass( 'pootle-live-editor-new-row' );

				$( id ).replaceWith( $ro );

				$ro = $( '.pootle-live-editor-new-row' );
				$( 'html' ).trigger( 'pootlepb_le_content_updated', [$ro] );
				$ro.removeClass( 'pootle-live-editor-new-cell' );

				$( id ).prevuRowInit();
			} );
			$rowPanel.ppbDialog( 'close' );
		},

		addRow : function ( callback, blockText ) {
			//console.log( "adding row" );
			window.ppbRowI = ppbData.grids.length;
			var num_cells;

			logPPBData( 'Adding row' );

			num_cells = parseInt( $('#ppb-row-add-cols' ).val() );
			var row = {
				id: window.ppbRowI,
				cells: num_cells,
				style: { background: "", background_image: "", background_image_repeat: "", background_image_size: "cover", background_parallax: "", background_toggle: "", bg_color_wrap: "", bg_image_wrap: "", match_col_hi: "", bg_mobile_image: "", bg_overlay_color: "", bg_overlay_opacity: "0.5", bg_video: "", bg_video_wrap: "", bg_wrap_close: "", class: "", col_class: "", col_gutter: "1", full_width: "", hide_row: "", margin_bottom: "0", margin_top: "0", row_height: "0", style: ""}
			}, cells, block;

			ppbData.grids.push( row );

			cells = {
				grid: window.ppbRowI,
				weight: ( 1 / row.cells )
			};

			block = {
				text : typeof blockText == "string" ? blockText : '<h2>Hi there,</h2><p>I am a new content block, go ahead, edit me and make me cool...</p>',
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
				var $ro   = $r.find( '#pg-' + qry.post + '-' + window.ppbRowI ),
					$cols = $ro.find( '.panel-grid-cell-container > .panel-grid-cell' );
				$cols.css( 'width', ( 100 - num_cells + 1 ) / num_cells + '%' );
				$( '.ppb-block.active, .ppb-row.active' ).removeClass( 'active' );
				$ro.find( '.pootle-live-editor-realtime:eq(0)' ).parents( '.ppb-block, .ppb-row' ).addClass( 'active' );
				$( '.pootle-live-editor.add-row' ).before( $ro );
				$ro = $( '#pg-' + qry.post + '-' + window.ppbRowI );
				$ro.prevuRowInit();

				if ( 'function' == typeof callback ) {
					callback( $ro );
				}
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
					diff = -1,
					$focussedContent = $( '.mce-edit-focus' );

				// Save content block
				prevu.saveTmceBlock( $focussedContent );
				$focussedContent.removeClass('mce-edit-focus')

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
				prevu.sync( function() { prevu.reset( 'noSort' ); } );

				logPPBData( 'Moved row ' + olI + ' => ' + newI );
			}
		},

		resizableCells : {
			handles: 'w',
			stop: function (event, ui) {
				$( this ).parent().removeClass( 'ppb-cols-resizing' );
			},
			resize: function (event, ui) {
				var $t = $( this ),
					$p = $t.parent(),
					$prev = $t.prev(),
					widthTaken = 0,
					widthNow = ui.size.width,
					originalWidth = ui.originalSize.width;

				$p.addClass( 'ppb-cols-resizing' );

				$t.css( 'width', ( 100 * $t.innerWidth() / $p.innerWidth() ) + '%' );

				$prev.siblings('.panel-grid-cell' ).each( function() {
					var $t = $( this );
					widthTaken += $t.outerWidth();
				} );

				widthTaken += parseInt( $prev.css('padding-left') ) + parseInt( $prev.css('padding-right') );

				$prev.css( 'width', 100 - ( 100 * widthTaken / $p.width() ) + '%' );

				prevu.resizableCells.correctCellData( $t  );
				prevu.resizableCells.correctCellData( $prev  );

				prevu.unSavedChanges = true;

				if ( originalWidth < widthNow ) {
					//Increasing width
					if ( $p.width() * 0.93 < widthTaken ) {
						$t.resizable( 'widget' ).trigger( 'mouseup' );
					}
				} else {
					//Decreasing width
					if ( $p.width() * 0.07 > $t.width() ) {
						$t.resizable( 'widget' ).trigger( 'mouseup' );
					}
				}
			},
			correctCellData: function ( $t ) {
				var width = $t.outerWidth(),
					pWidth = $t.parent().width() + 1,
					i = $('.panel-grid-cell-container > .panel-grid-cell' ).not('.ppb-block *').index( $t ),
					weight = Math.floor( 10000 * width / pWidth ) / 10000;

				$t.find('.pootle-live-editor.resize-cells' ).html('<div class="weight">' + (Math.round( 1000 * weight ) / 10) + '%</div>');

				ppbData.grid_cells[i].weight = weight;
				return weight;
			}
		},

		contentDraggable : {
			handle: '.ppb-edit-block .dashicons-move',
			grid: [5,5],
			start: function (e, ui) {
				var $t = $( this ),
					$ro = $t.closest( '.panel-row-style' ),
					roMinHi = $ro.css( 'min-height' );

				if ( roMinHi ) {
					$ro.find( '.panel-grid-cell-container, .ppb-col' ).not( '.ppb-block *' )
					   .css( 'min-height', roMinHi );
				}
				$t.find( '.ppb-edit-block .dashicons-before:first' ).click();
				ui.position.left = parseInt( $t.css( 'margin-left' ) );
				ui.position.top = parseInt( $t.css( 'margin-top' ) );
			},
			drag: function( e, ui ) {
				var $t = $( this ),
					$p = $t.parent(),
					$ro = $t.closest( '.panel-row-style' ),
					mg = {
						t: parseInt( $t.css( 'margin-top' ) ),
						l: parseInt( $t.css( 'margin-left' ) )
					},
					top = ui.position.top + mg.t,
					left = ui.position.left + mg.l,
					hiMrgn = ( parseInt( $ro.css( 'min-height' ) ) - $t.outerHeight() ) / 2,
					wiMrgn = ( $p.width() - parseInt( $t.outerWidth() ) ) / 2;

				if( top < -25 || left < -25 ) {
					$t.draggable( 'widget' ).trigger( 'mouseup' );
				}

				$ro.removeClass( 'pootle-guides-x pootle-guides-y' );

				if ( hiMrgn > 25 && Math.abs( hiMrgn - top ) < 25 ) {
					$ro.addClass( 'pootle-guides-x' );
					ui.position.top = hiMrgn - mg.t;
				}

				if ( wiMrgn > 25 && Math.abs( wiMrgn - left ) < 25 ) {
					$ro.addClass( 'pootle-guides-y' );
					ui.position.left = wiMrgn - mg.l;
				}
			},
			stop: function (e, ui) {
				var st = JSON.parse( ppbData.widgets[window.ppbPanelI].info.style ),
					margin = {},
					$t = $( this );
				$t.closest('.panel-row-style').removeClass( 'pootle-guides-x pootle-guides-y' );

				st['margin-top'] = Math.max( 1, ui.position.top + parseInt( $t.css( 'margin-top' ) ) );
				st['margin-left'] = Math.max( 1, ui.position.left + parseInt( $t.css( 'margin-left' ) ) );

				$t.css( {
					marginTop	: st['margin-top'],
					top			: '',
					marginLeft	: st['margin-left'],
					left		: '',
					width		: '',
					height		: ''
				} );

				ppbData.widgets[window.ppbPanelI].info.style = JSON.stringify( st );
			}
		},

		contentResizable : {
			handles: 'e, w',
			start: function (e, ui) {
				var $t = $( this );
				$t.find( '.ppb-edit-block .dashicons-before:first' ).click();
				$t.css( {
					maxWidth: 9999,
				} );
			},
			stop: function (event, ui) {
				var st = JSON.parse( ppbData.widgets[window.ppbPanelI].info.style ),
					$t = $( this ),
					$p = $t.parent();

				st['width'] = Math.round( parseInt( $t.width() ) );
				st['margin-left'] = Math.max( 1, ui.position.left + parseInt( $t.css( 'margin-left' ) ) );

				$t.css( {
					maxWidth	: st['width'],
					marginLeft	: st['margin-left'],
					left		: '',
					width		: ''
				} );

				ppbData.widgets[window.ppbPanelI].info.style = JSON.stringify( st );
			},
			resize: function (event, ui) {
				var st = JSON.parse( ppbData.widgets[window.ppbPanelI].info.style ),
					$t = $( this ),
					$p = $t.parent();
				if ( ( $t.outerWidth() - 7 ) > $p.width() ) {
					$t.css( 'width', '' );
					$t.resizable( 'widget' ).trigger( 'mouseup' );
				}
			},
		},

		moduleDraggable : {
			helper : "clone",
			start : function(){
				$mods.removeClass('toggle')
			}
		},

		insertModule : function( $contentblock, $module ) {
			var tab = $module.data( 'tab' );
			$contentblock.find('.dashicons-move').click();

			var $ed = $contentblock.find( '.mce-content-body' ),
				ed  = tinymce.get( $ed.attr( 'id' ) );
			ed.selection.select(tinyMCE.activeEditor.getBody(), true);
			ed.selection.collapse(false);

			if ( $module.data( 'callback' ) ) {
				if ( typeof window.ppbModules[ $module.data( 'callback' ) ] == 'function' )
					window.ppbModules[ $module.data( 'callback' ) ]( $contentblock, ed, $ );
			}

			if ( tab ) {
				if ( 0 < tab.indexOf('-row-tab') ) {
					$('.panel-grid.active').find('.ppb-edit-row .dashicons-admin-appearance').click();
				} else {
					$contentblock.find('.ppb-edit-block .dashicons-edit' ).click();
				}

				$( 'a.ppb-tabs-anchors[href="' + tab + '"]' ).click();
			}
			$loader.fadeOut(500);
		},

		moduleDroppable : {
			accept: '.ppb-module',
			activeClass: "ppb-drop-module",
			hoverClass: "ppb-hover-module",
			drop: function( e, ui ) {
				var $m = ui.draggable,
					$t = $( this );
				$loader.fadeIn(500);
				if ( $t.hasClass('add-row') ) {
					$( '#ppb-row-add-cols' ).val( '1' );
					prevu.addRow( function ( $t ) {
						prevu.insertModule( $t.find( '.ppb-block' ).last(), $m )
					}, '<p>&nbsp;</p>' );
				} else {
					prevu.insertModule( $t, $m )
				}
			}
		},

		insertImage : function() {
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
				var img = prevu.insertImageFrame.state().get('selection').first().toJSON();

				console.log( img );

				// Do something with img.id and/or img.url here
				var $img =
					'<figure id="attachment_' + img.id + '" class="' + ( img.caption ? 'wp-caption' : '' ) + '">' +
					'<img class="size-medium wp-image-' + img.id + '" src="' + img.url + '" alt="' + img.alt + '">' +
					( img.caption ? '<figcaption class="wp-caption-text">' + img.caption + '</figcaption>' : '' ) +
					'</figure>';

				var ed = tinymce.get( prevu.activeEditor.attr( 'id' ) );
				ed.selection.select(tinyMCE.activeEditor.getBody(), true);
				ed.selection.collapse(false);
				ed.execCommand( 'mceInsertContent', false, $img );
			});

			// Finally, open the modal
			prevu.insertImageFrame.open();
		},

		saveTmceBlock : function ( $ed ) {
			if ( ! $ed || ! $ed.length ) return;
			var blockI = $ed.siblings( '.pootle-live-editor' ).data( 'index' );
			if ( ! ppbData.widgets[blockI] ) return;
			ppbData.widgets[blockI].text = $ed.html();
			prevu.unSavedChanges = true;
		},

		postSettings : function () {
			$postSettingsDialog.ppbDialog( 'open' );
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
	$rowPanel.ppbTabs().ppbDialog( dialogAttr );
	panels.addInputFieldEventHandlers( $rowPanel );

	dialogAttr.title = 'Add row';
	dialogAttr.dialogClass = dialogAttr.open = null;
	dialogAttr.buttons.Done = prevu.addRow;
	dialogAttr.height = ppbAjax.ipad ? 268 : 232;
	dialogAttr.width = 340;
	$addRowDialog.ppbDialog( dialogAttr );


	dialogAttr.title = 'Are you sure';
	dialogAttr.buttons = {
		'Yes' : function () {
			if ( 'function' == typeof prevu.deleteCallback ) {
				prevu.deleteCallback();
			}
			delete prevu.deleteCallback;
			$deleteDialog.ppbDialog( 'close' );
		},
		'Cancel' : function () {
			$deleteDialog.ppbDialog( 'close' );
		}
	};
	dialogAttr.height = ppbAjax.ipad ? 241 : 200;
	dialogAttr.width =  430;
	$deleteDialog.ppbDialog( dialogAttr );
	dialogAttr.buttons = {
		Done : function () {
			$setTitleDialog.ppbDialog( 'close' );
			prevu.syncAjax();
		}
	};

	dialogAttr.height = ppbAjax.ipad ? 232 : 227;
	dialogAttr.width =  430;
	dialogAttr.title = $setTitleDialog.data( 'title' );
	dialogAttr.close = function() {
		ppbAjax.title = $( '#ppble-live-page-title' ).val();
	};
	$setTitleDialog.ppbDialog( dialogAttr );

	dialogAttr.height = 610;
	dialogAttr.width =  520;
	dialogAttr.title = 'Insert icon';
	dialogAttr.buttons = [
		{
			text: 'Remove icon',
			class: 'ui-button-link',
			click: function () {
				if ( 'function' == typeof pickFaIcon.callback ) {
					pickFaIcon.callback( {
						html: '',
						attr: '',
						style: '',
						class: '',
						size: '',
						color: ''
					} );
				}
				$iconPicker.ppbDialog( 'close' );
			}
		},
		{
			text: 'Insert',
			click: function () {
				$iconPicker.ppbDialog( 'close' );
				var iclas = $iconPicker.clas.val(),
					icolr = $iconPicker.colr.val(),
					isize = $iconPicker.size.val(),
					ilink = $iconPicker.link.val(),
					style = 'font-size:' + isize + 'px;color:' + icolr,
					attr = 'style="' + style + '" class="fa ' + iclas + '"',
					icon = '<i ' + attr + '><span style="display:none">' + iclas + '</span></i>';
				if ( 'function' == typeof pickFaIcon.callback ) {
					pickFaIcon.callback( {
						html: icon,
						attr: attr,
						style: style,
						class: iclas,
						size: isize,
						color: icolr,
						link: ilink
					} );
				}
			},
		},
	];
	dialogAttr.close = function() {};
	$iconPicker.ppbDialog( dialogAttr );
	$iconPicker.find( '#ppb-icon-choose' ).iconpicker({
		placement: 'inline'
	});

	$iconPicker.clas = $( '#ppb-icon-choose' );
	$iconPicker.colr = $( '#ppb-icon-color' );
	$iconPicker.size = $( '#ppb-icon-size' );
	$iconPicker.link = $( '#ppb-icon-link' );
	$iconPicker.prvu = $( '#ppb-icon-preview' );

	prevu.iconPrevu = function ( e ) {
		var iclas = $iconPicker.clas.val(),
			icolr = $iconPicker.colr.val(),
			isize = $iconPicker.size.val(),
			style = 'font-size:' + isize + 'px;color:' + icolr,
			attr = 'style="' + style + '" class="fa ' + iclas + '"';
		console.log( iclas, icolr, isize );
			$iconPicker.prvu.html( '<i ' + attr + '><span style="display:none">' + iclas + '</span></i>' );
	};

	$iconPicker.clas.on( 'iconpickerUpdated', prevu.iconPrevu );
	$iconPicker.colr.wpColorPicker( { change: prevu.iconPrevu } );
	$iconPicker.size.change( prevu.iconPrevu );

	var pickFaIcon = function ( callback, properties ) {
		$iconPicker.ppbDialog( 'open' );
		pickFaIcon.callback = callback;

		$iconPicker.clas.add( $iconPicker.find( '.iconpicker-search' ) ).val( '' );
		$iconPicker.prvu.html( '' );
		if ( ! properties ) return;

		if ( properties.class ) {
			$iconPicker.clas.val( properties.class ).change();
		}
		if ( properties.color ) {
			$iconPicker.colr.val( properties.color ).change();
		}
		if ( properties.size ) {
			$iconPicker.size.val( parseInt( properties.size ) ).change();
		}
		if ( properties.link ) {
			$iconPicker.link.val( properties.link ).change();
		}

	};

	if ( $postSettingsDialog.length ) {
		dialogAttr.height = 700;
		dialogAttr.height = ppbAjax.ipad ? 529 : 502;

		dialogAttr.width = 610;
		dialogAttr.title = 'Post settings';
		dialogAttr.close = function () {
			ppbAjax.category = $postSettingsDialog.find( '.post-category' ).val();
			ppbAjax.tags = $postSettingsDialog.find( '.post-tags' ).val();
		};
		dialogAttr.buttons.Done = function () {
			$postSettingsDialog.ppbDialog( 'close' );
			prevu.syncAjax();
		};
		$postSettingsDialog.ppbDialog( dialogAttr );

		//$setTitleDialog = $postSettingsDialog;
	}

	$('.panel-grid-cell-container > .panel-grid-cell' ).not('.ppb-block *').each( function () {
		prevu.resizableCells.correctCellData( $(this) );
	} );

	$ppb.delegate( '.pootle-live-editor .dashicons-before', 'mousedown', function () {
		$( '.pootle-live-editor-realtime.has-focus' ).blur();
	} );

	$ppb.delegate( '.ppb-edit-row .dashicons-before', 'click', function () {
		window.ppbRowI = $( this ).closest( '.pootle-live-editor' ).data( 'index' );;
	} );
	$ppb.delegate( '.ppb-edit-row .dashicons-admin-appearance', 'click', function () {
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
		var removeCells  = [],
			removeBlocks = [],
			$t           = $( this ),
			rowI         = $t.closest( '.pootle-live-editor' ).data( 'index' );
		prevu.deleteCallback = function () {
			ppbData.grids.splice( rowI, 1 );

			$.each( ppbData.widgets, function ( i, v ) {
				if ( v && v.info ) {
					if ( rowI == v.info.grid ) {
						removeBlocks.push( i )
					} else if ( rowI < v.info.grid ) {
						ppbData.widgets[i].info.grid --;
					}
				}
			} );

			$.each( ppbData.grid_cells, function ( i, v ) {
				if ( v ) {
					var gi = parseInt( v.grid );
					if ( rowI == gi ) {
						removeCells.push( i )
					} else if ( rowI < gi ) {
						ppbData.grid_cells[i].old_grid = gi;
						ppbData.grid_cells[i].grid = -- gi;
					}
				}
			} );

			//Sort in decending order
			removeBlocks.sort( function ( a, b ) {
				return b - a
			} );
			removeCells.sort( function ( a, b ) {
				return b - a
			} );

			$.each( removeBlocks, function ( i, v ) {
				ppbData.widgets.splice( v, 1 );
			} );
			$.each( removeCells, function ( i, v ) {
				ppbData.grid_cells.splice( v, 1 );
			} );

			ppbData.grids.filter( function () {
				return true;
			} );
			ppbData.widgets.filter( function () {
				return true;
			} );
			ppbData.grid_cells.filter( function () {
				return true;
			} );

			//Remove row from preview
			$t.closest( '.panel-grid' ).remove();

			prevu.sync( function () {
				prevu.reset();
			} );
		};
		$deletingWhat.html( 'row' );
		$deleteDialog.ppbDialog( 'open' );
	} );

	$ppb.delegate( '.ppb-edit-block .dashicons-before', 'click touchstart', function () {
		var $t = $( this );
		$( '.ppb-block.active, .ppb-row.active' ).removeClass( 'active' );
		$t.parents( '.ppb-block, .ppb-row' ).addClass( 'active' );
		window.ppbPanelI = $t.closest( '.pootle-live-editor' ).data( 'index' );
		prevu.activeEditor = $(this).closest('.ppb-block' ).children('.pootle-live-editor-realtime');
	} );

	$ppb.delegate( '.ppb-edit-block .dashicons-edit', 'click', function () {
		$contentPanel.ppbDialog( 'open' );
	} );

	$ppb.delegate( '.ppb-edit-block .dashicons-no', 'click', function () {
		prevu.reset(); // Reset the indices
		var $t = $( this ),
			i = $t.closest( '.pootle-live-editor' ).data( 'index' );

		prevu.deleteCallback = function () {

			ppbData.widgets.splice( i, 1 ); // Remove the content block data
			$t.closest( '.ppb-block' ).remove(); // Remove block html element

			prevu.reset(); // Reset the indices again
		};
		$deletingWhat.html( 'content block' );
		$deleteDialog.ppbDialog( 'open' );
	} );

	$ppb.delegate( '.ppb-edit-block .pootle-live-editor-addons .pootle-live-editor-addon', 'click', function () {
		var $t = $( this );
		$contentPanel.ppbDialog( 'open' );
		$contentPanel.find( 'a[href="#pootle-' + $t.data( 'id' ) + '-tab"]' ).click();
	} );

	$ppb.delegate( '.ppb-edit-block .dashicons-format-image', 'click', function ( e ) {
		e.preventDefault();
		prevu.insertImage()
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

	$body.on("click touchstart", function ( e ) {
		var $t = $( e.target );
		if ( ! $t.closest('.ppb-block').length || $t.closest('.ppb-edit-row .dashicons-before, .ppb-edit-block .dashicons-before').length ) {
			try {
				webkit.messageHandlers.heySwift.postMessage( "hideTextFormatting" );
				webkit.messageHandlers.heySwift.postMessage("hideKeyboard");
			} catch ( err ) {}
		} else {
			try {
				webkit.messageHandlers.heySwift.postMessage( "showTextFormatting" );
			} catch ( err ) {}
		}
	} );

	$ppb.delegate( '.ppb-edit-row .dashicons-editor-code', 'click', function () {
		if ( prevu.justClickedEditRow ) {
			try {
				webkit.messageHandlers.heySwift.postMessage("hideKeyboard");
			} catch(err) {}
			var $t = $( this );
			window.ppbRowI = $t.closest( '.pootle-live-editor' ).data( 'index' );
			$rowPanel.ppbDialog( 'open' );
		} else {
			prevu.justClickedEditRow = true;
			setTimeout( function(){
				prevu.justClickedEditRow = false;
			}, 520 );
		}
	} );

	$ppb.delegate( '.ppb-edit-block .dashicons-move', 'click', function () {
		if ( prevu.justClickedEditBlock ) {
			var $t = $( this );
			window.ppbPanelI = $t.closest( '.pootle-live-editor' ).data( 'index' );
			$contentPanel.ppbDialog( 'open' );
		} else {
			prevu.justClickedEditBlock = true;
			setTimeout( function () {
				prevu.justClickedEditBlock = false;
			}, 520 );
		}
	} );

	ppbIpad.updatedNotice = $( '#ppb-ipad-updated-notice' );
	ppbIpad.notice = $( '#ppb-ipad-notice' );
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
		//console.log( $editBar.data( 'index' ) );
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
		tinymce.execCommand( 'mceFocus', false, prevu.activeEditor.attr( 'id' ) );
		prevu.insertImage()
	};
	ppbIpad.preview = function () {
		prevu.sync( null, 'Publish' );

	};
	ppbIpad.postSettings = function () {
		prevu.postSettings();
	};
	ppbIpad.AddRow = function () {
		$addRowDialog.ppbDialog( 'open' );
	};

	ppbIpad.Update = function () {
		prevu.ajaxCallback = function ( no1, no2, url ) {
			window.location = url + '?ppb-ipad=preview';
		};

		prevu.unSavedChanges = true;
		prevu.saveTmceBlock( $( '.mce-edit-focus' ) );
		ppbAjax.data = ppbData;
		ppbAjax.publish = 'Publish';
		prevu.noRedirect = 1;

		if ( ppbAjax.title ) {
			var butt = [
				{
					text  : 'Save Draft',
					click : function () {
						$setTitleDialog.ppbDialog( 'close' );
						try {
							webkit.messageHandlers.heySwift.postMessage( "updatedLoadingPreview" );
						} catch ( err ) {
							console.log( 'The native context does not exist yet' );
						}
						ppbIpad.notice.show( 0 );
						ppbAjax.publish = 'Save Draft';
						prevu.syncAjax();
					}
				},
				{
					text  : 'Publish',
					icons : {
						primary : 'ipad-publish'
					},
					click : function () {
						$setTitleDialog.ppbDialog( 'close' );
						try {
							webkit.messageHandlers.heySwift.postMessage( "updatedLoadingPreview" );
						} catch ( err ) {
							console.log( 'The native context does not exist yet' );
						}
						ppbIpad.notice.show( 0 );
						ppbAjax.publish = 'Publish';
						prevu.syncAjax();
					}
				}
			];
			$setTitleDialog.parent().data( 'action', 'Publish' );
			$setTitleDialog.ppbDialog( 'option', 'buttons', butt );
			$setTitleDialog.ppbDialog( 'open' );
			return;
		} else {
			try {
				webkit.messageHandlers.heySwift.postMessage( "updatedLoadingPreview" );
			} catch ( err ) {
				console.log( 'The native context does not exist yet' );
			}
			ppbIpad.notice.show( 0 );
		}
		prevu.syncAjax();
	};

	$ppbIpadColorDialog.delegate( '.ppb-ipad-color-picker span', 'mousedown', function ( e ) {
		e.preventDefault();
		return false;
	} );

	$ppbIpadColorDialog.delegate( '.ppb-ipad-color-picker span', 'click', function ( e ) {
		e.preventDefault();

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
		H4     : function () {
			tinymce.activeEditor.execCommand("mceToggleFormat", false, "h4")
		},
		Quote  : function () {
			tinymce.activeEditor.execCommand('mceBlockQuote')
		},
		Color  : function () {
			//console.log( $( window ).scrollTop() );
			//console.log( $( '.ppb-block.active' ).offset().top );
			var posTop = Math.max( $( window ).scrollTop(), $( '.ppb-block.active' ).offset().top );
			//console.log( posTop );
			$ppbIpadColorDialog.show().css('top', posTop );
		},
		Link   : function () {
			tinymce.activeEditor.execCommand('PPB_Link')
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

	prevu.tmce.verify_html = false;
	prevu.tmce.inline	= true;
	prevu.tmce.theme	= 'ppbprevu';
	prevu.tmce.fontsize_formats	= '20px 25px 30px 35px 40px 50px 70px 100px';

	//console.log( prevu.tmce );

	if ( ! ppbAjax.ipad ) {
		prevu.tmce.toolbar = [
			'h1',
			'h2',
			'h3',
			'h4',
			'shrameeFonts',
			'fontsizeselect',
			//'ppbFontStyles',
			'blockquote',
			'forecolor',
			'ppblink',
			'bold',
			'italic',
			'alignleft',
			'aligncenter',
			'alignright',
			'ppbInsertImage'
		];
		$postSettingsDialog.find('select').chosen();
	} else {
		prevu.tmce.plugins	= prevu.tmce.plugins.replace('wpeditimage,', '').replace('wplink,', 'ppblink,');
		$( 'a' ).click( function( e ) {
			e.preventDefault();
		} )
	}

	prevu.tmce.content_css	= "http://wp/ppb/wp-includes/css/dashicons.min.css?ver=4.4.2-alpha-36412";
	prevu.tmce.setup	= function(editor) {

		editor.onDblClick.add(function(ed, e) {
			var $i = $( e.target );
			if ( $i.hasClass( "fa" ) ) {
				var $a = $i.parent('a');
				pickFaIcon(
					function ( icon ) {
						if ( icon.class ) {
							$i.attr( {
								class: 'fa ' + icon.class,
								style: icon.style
							} );
							if ( icon.link ) {
								if ( ! $a.length ) {
									$i.wrap('<a></a>');
									$a = $i.parent('a');
								}
								$a.attr( 'href', icon.link );
							} else {
								if ( $i.parent('a').length )
									$i.unwrap();
							}
						} else {
							$i.closest('div[style*="text-align: center"]').remove();
						}
						prevu.saveTmceBlock( $( $i.closest('.mce-content-body') ) );
					},
					{
						class: $i.attr( 'class' ).replace( 'fa ', '' ),
						color: $i.css( 'color' ),
						size: $i.css( 'font-size' ),
						link: $a.attr( 'href' )
					}
				);
			}
		});

		editor.on('change', function(e) {
			prevu.saveTmceBlock( $( e.target.targetElm ) );
		});
		editor.on('focus', function(e) {
			var $t = $( e.target.targetElm );
			$( '.ppb-block.active, .ppb-row.active' ).removeClass( 'active' );
			$t.parents( '.ppb-block, .ppb-row' ).addClass( 'active' );
		});
		editor.addButton('ppbInsertImage', {
			text: '',
			icon: 'dashicons dashicons-format-image',
			onclick: function () {
				ppbIpad.insertImage();
			}
		});

		editor.addButton( 'ppbAlign', function () {
			var items = [
				{icon: 'alignleft', tooltip: 'Align left', value: 'alignleft'},
				{icon: 'aligncenter', tooltip: 'Align center', value: 'aligncenter'},
				{icon: 'alignright', tooltip: 'Align right', value: 'alignright'}
			];

			return {
				type: 'listbox',
				text: '',
				icon: 'alignleft',
				minWidth: 70,
				onclick: function () {
				},
				onselect: function ( e ) {
					var ed = tinymce.activeEditor,
						val = this.value().replace('align', '');
					ed.execCommand( 'Justify' + val[0].toUpperCase() + val.substring( 1 ) );
				},
				values: items,
				onPostRender: function() {
					var ed = tinymce.activeEditor,
						self = this;

					ed.on( 'nodeChange', function ( e ) {
						var formatter = ed.formatter;
						var value = null;

						$.each( e.parents, function ( ni, node ) {
							$.each( items, function ( ii, item ) {
								if ( formatter.matchNode( node, item.value ) ) {
									self.value( item.value );
									self.settings.icon = item.icon;
									return false;
								}
							} );
						} );

					} );
				}
			}
		} );
		editor.addButton( 'shrameeFonts', function() {
			var items = [
				{text: 'Default', value: 'inherit'},
				// System Fonts
				{text: 'Georgia', value: 'Georgia, serif'}, {text: 'Arial Black', value: '"Arial Black", Gadget, sans-serif'}, {text: 'Comic Sans MS', value: '"Comic Sans MS", cursive, sans-serif'}, {text: 'Impact', value: 'Impact, Charcoal, sans-serif'}, {text: 'Courier New', value: '"Courier New", Courier, monospace'},
				// Google Fonts
				{text: 'Abril Fatface', value: 'Abril Fatface'}, {text: 'Amatic SC', value: 'Amatic SC'}, {text: 'Dancing Script', value: 'Dancing Script'}, {text: 'Droid Serif', value: 'Droid Serif'}, {text: 'Great Vibes', value: 'Great Vibes'}, {text: 'Inconsolata', value: 'Inconsolata'}, {text: 'Indie Flower', value: 'Indie Flower'}, {text: 'Lato', value: 'Lato'}, {text: 'Lobster', value: 'Lobster'}, {text: 'Lora', value: 'Lora'}, {text: 'Oswald', value: 'Oswald'}, {text: 'Pacifico', value: 'Pacifico'}, {text: 'Passion One', value: 'Passion One'}, {text: 'Patua One', value: 'Patua One'}, {text: 'Playfair Display', value: 'Playfair Display'}, {text: 'Poiret One', value: 'Poiret One'}, {text: 'Raleway', value: 'Raleway'}, {text: 'Roboto', value: 'Roboto'}, {text: 'Roboto Condensed', value: 'Roboto Condensed'}, {text: 'Roboto Mono', value: 'Roboto Mono'}, {text: 'Roboto Slab', value: 'Roboto Slab'}, {text: 'Shadows Into Light', value: 'Shadows Into Light'}, {text: 'Sigmar One', value: 'Sigmar One'}, {text: 'Source Sans Pro', value: 'Source Sans Pro'}, {text: 'Ubuntu Mono', value: 'Ubuntu Mono'},
			]
			return {
				type: 'listbox',
				text: 'Font',
				icon: false,
				minWidth: 70,
				classes: 'shramee-fonts-control',
				onclick: function() {
				},
				onselect: function ( e ) {
					var ed = tinymce.activeEditor,
						val = this.value();

					if ( ! val ) ed.formatter.remove( 'shrameeFontFormat' );

					if ( -1 == val.indexOf(',') ) {
						ed.formatter.apply( 'shrameeFontFormat', {font: val, gfont: val.replace( ' ', '+' ) } );
						$body.append( '<link href="https://fonts.googleapis.com/css?family=' + val.replace( ' ', '+' ) + '"  rel="stylesheet">' );
					} else {
						ed.formatter.apply( 'shrameeFontFormat', {font: val} );
					}
				},
				values: items,
				onPostRender: function () {
					var ed = tinymce.activeEditor,
						self = this;

					ed.on( 'nodeChange', function ( e ) {
						var value = null;

						$( e.parents ).each( function () {
							var font = $( this ).css( 'font-family' );
							$.each( items, function ( ii, item ) {
								if ( -1 < font.indexOf( item.text ) ) {
									value = item.value;
									$( '.mce-shramee-fonts-control' ).find( '.mce-txt' ).html( item.text );
									return false;
								}
							} );
							if ( value ) {
								return false;
							}
						} );

						if ( ! value ) {
							$( '.mce-shramee-fonts-control' ).find( '.mce-txt' ).html( 'Font' );
							value = 'inherit';
						}
						self.state.set( 'value', value );
					} );
				}
			};
		} );
		editor.addButton( 'ppbFontStyles', function() {
			var items = [
				{text: 'Elegant shadow', value: 'ppbfost-elegant-shadow'},
				{text: 'Deep shadow', value: 'ppbfost-deep-shadow'},
				{text: 'Inset shadow', value: 'ppbfost-inset-shadow'},
				{text: 'Retro shadow', value: 'ppbfost-retro-shadow'},
			],
				lastVal = '';
			return {
				type: 'listbox',
				text: 'Font Style',
				icon: false,
				minWidth: 70,
				classes: 'ppbFoStField',
				onselect: function ( e ) {
					var ed = tinymce.activeEditor,
						val = this.value();
					$.each( items, function ( ii, item ) {
						ed.formatter.remove( 'ppbFoStFormat', {value: item.value} );
					} );
					ed.formatter.apply( 'ppbFoStFormat', {value: val} );
				},
				values: items,
				onPostRender: function () {
					var ed = tinymce.activeEditor,
						self = this;

					ed.on( 'nodeChange', function ( e ) {
						var value = null;

						$( e.parents ).each( function () {
							var $t = $( this );
							$.each( items, function ( ii, item ) {
								if ( $t.hasClass( item.value ) ) {
									value = item.value;
									$( '.mce-ppbFoStField' ).find( '.mce-txt' ).html( item.text );
									return false;
								}
							} );
							if ( value ) {
								return false;
							}
						} );

						if ( ! value ) {
							$( '.mce-ppbFoStField' ).find( '.mce-txt' ).html( 'Font Style' );
							value = 'inherit';
						}

						lastVal = value;
						self.state.set( 'value', value );
					} );
				}
			};
		} );

	};

	prevu.tmce.formats = {
		shrameeFontFormat: {
			inline: 'span',
			classes: 'ppb-google-font',
			attributes: {'data-font': '%gfont'},
			styles: {fontFamily: '%font'}
		},
		ppbFoStFormat: {
			block: 'h2',
			classes: '%value'
		}
	};

	tinymce.init( prevu.tmce );

	$ppb.sortable( prevu.rowsSortable );

	$ppb.find('.panel-grid').each( function () {
		$( this ).prevuRowInit();
	} );

	$( '[href="#ppb-live-update-changes"]' ).click( function(){
		prevu.sync( null, 'Save Draft' );
	} );

	$( '[href="#ppb-live-post-settings"]' ).click( function(){
		$postSettingsDialog
			.ppbDialog( 'option', 'buttons', {
				Done : function () {
					$postSettingsDialog.ppbDialog( 'close' );
					prevu.sync('Publish');
				}
			} )
			.ppbDialog( 'open' );
	} );

	$( '[href="#ppb-live-publish-changes"]' ).click( function(){
		prevu.sync( null, 'Publish' );
	} );

	$( '.ppb-edit-block' ).click( function () {
		var editorid = $( this ).siblings( '.mce-content-body' ).attr( 'id' );
		tinymce.get( editorid ).focus();
	} );

	$( '#ppble-feat-img-prevu' ).click( function () {
		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( typeof ppbFeaturedImageFrame !== 'undefined' ) {
			ppbFeaturedImageFrame.open();
			return;
		}

		// Create the media frame.
		ppbFeaturedImageFrame = wp.media.frames.ppbFeaturedImageFrame = wp.media( {
			title: 'Featured Image',
			button: {
				text: 'Set Featured Image'
			},
			multiple: false  // Set to true to allow multiple files to be selected
		} );

		// When an image is selected, run a callback.
		ppbFeaturedImageFrame.on( 'select', function () {
			// We set multiple to false so only get one image from the uploader
			attachment = ppbFeaturedImageFrame.state().get( 'selection' ).first().toJSON();

			// Do something with attachment.id and/or attachment.url here
			ppbAjax.thumbnail = attachment.id;
			$('#ppble-feat-img-prevu').css( 'background-image', 'url(' + attachment.sizes.thumbnail.url + ')' );
		} );

		// Finally, open the modal
		ppbFeaturedImageFrame.open();
	} );

	window.onbeforeunload = function ( e ) {
		if ( prevu.unSavedChanges ) {
			return "You have unsaved changes! Click 'Update' in admin bar to save.\n\nYour changes will be lost if you dan't save.";
		}
	};
	prevu.resort();
	prevu.reset( 'noSort' );

	// Modules
	$mods.find( '.ppb-module' ).draggable( prevu.moduleDraggable );
	$ppb.find( '.ppb-block, .ppb-live-add-object.add-row' ).droppable( prevu.moduleDroppable );

	window.ppbModules.image = function ( $t, ed ) {
		prevu.insertImage();
	};

	window.ppbModules.chooseIconDialog = function ( $t, ed ) {
		pickFaIcon( function ( icon ) {
			ed.selection.setCursorLocation( ed.getBody().firstChild, 0 );
			ed.selection.collapse( false );

			if ( icon.link ) {
				icon.html = '<a href="' + icon.link + '">' + icon.html + '</a>';
			}

			ed.execCommand( 'mceInsertContent', false, '<div style="text-align: center">' + icon.html + '</div>' );
			prevu.saveTmceBlock( $( $i.closest('.mce-content-body') ) );
		} );
	};

	window.ppbModules.unsplash = function ( $t, ed ) {
		ShrameeUnsplashImage( function ( url ) {
			var $img = '<img src="' + url + '">';
			ed.selection.select(tinyMCE.activeEditor.getBody(), true);
			ed.selection.collapse(false);
			ed.execCommand( 'mceInsertContent', false, $img );
		} );
	};

	window.ppbModules.button = function ( $t, ed ) {
		ed.execCommand( 'pbtn_add_btn_cmd' );
	};

	window.ppbModules.heroSection = function ( $t ) {
		var $tlbr = $t.closest( '.panel-grid' ).find( '.ppb-edit-row' );
		$tlbr.find( '.ui-sortable-handle' ).click();
		ppbData.grids[ppbRowI].style.full_width = true;
		ppbData.grids[ppbRowI].style.background_toggle = '.bg_image';
		ppbData.grids[ppbRowI].style.row_height = '500';
	};

	window.ppbModules.onePager = function ( $t ) {
		$t.find( '.ppb-edit-block .dashicons-edit' ).click();
		$( 'a.ppb-tabs-anchors[href="#pootle-ppb-1-pager-tab"]' ).click();
		ppbModules.heroSection( $t );
	};

	$body.on( 'savingPPB', function(){
		ppbAjax.data.google_fonts = [];
		$body.find( '[data-font]' ).not( '[data-font="%gfont"]' ).each( function(){
			ppbAjax.data.google_fonts.push( $( this ).attr( 'data-font' ) );
		} );
	} );
	$( 'html' ).on( 'pootlepb_le_content_updated', function ( e, $t ) {
		console.log( $t );
		ppbSkrollr.refresh( $t.find('.ppb-col') );
	} );
} );