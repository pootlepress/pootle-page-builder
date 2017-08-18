/*! jQuery UI - v1.11.4 - 2015-07-16
 * http://jqueryui.com
 * Includes: core.js, widget.js, mouse.js, position.js, draggable.js, resizable.js, button.js, dialog.js
 * Copyright 2015 jQuery Foundation and other contributors; Licensed MIT */

jQuery( function ( $ ) {
	/*!
	 * jQuery UI Dialog 1.11.4
	 * http://jqueryui.com
	 *
	 * Copyright jQuery Foundation and other contributors
	 * Released under the MIT license.
	 * http://jquery.org/license
	 *
	 * http://api.jqueryui.com/dialog/
	 */
	$.widget( "ppb.ppbDialog", {
		version: "1.11.4",
		options: {
			appendTo: "body",
			autoOpen: true,
			buttons: [],
			closeOnEscape: true,
			closeText: "Close",
			dialogClass: "",
			draggable: true,
			hide: null,
			height: "auto",
			maxHeight: null,
			maxWidth: null,
			minHeight: 150,
			minWidth: 150,
			modal: true,
			position: {
				my: "center",
				at: "center",
				of: window,
				collision: "fit",
				// Ensure the titlebar is always visible
				using: function ( pos ) {
					var topOffset = $( this ).css( pos ).offset().top;
					if ( topOffset < 0 ) {
						$( this ).css( "top", pos.top - topOffset );
					}
				}
			},
			resizable: true,
			show: null,
			title: null,
			width: 300,

			// callbacks
			beforeClose: null,
			close: null,
			drag: null,
			dragStart: null,
			dragStop: null,
			focus: null,
			open: null,
			resize: null,
			resizeStart: null,
			resizeStop: null
		},

		sizeRelatedOptions: {
			buttons: true,
			height: true,
			maxHeight: true,
			maxWidth: true,
			minHeight: true,
			minWidth: true,
			width: true
		},

		resizableRelatedOptions: {
			maxHeight: true,
			maxWidth: true,
			minHeight: true,
			minWidth: true
		},

		_create: function () {
			this.originalCss = {
				display: this.element[0].style.display,
				width: this.element[0].style.width,
				minHeight: this.element[0].style.minHeight,
				maxHeight: this.element[0].style.maxHeight,
				height: this.element[0].style.height
			};
			this.originalPosition = {
				parent: this.element.parent(),
				index: this.element.parent().children().index( this.element )
			};
			this.originalTitle = this.element.attr( "title" );
			this.options.title = this.options.title || this.originalTitle;

			this._createWrapper();

			this.element
					.show()
					.removeAttr( "title" )
					.addClass( "ppb-dialog-content ppb-widget-content" )
					.appendTo( this.uiDialog );

			$( this.uiDialog ).css( 'position', 'fixed' );

			this._createTitlebar();
			this._createButtonPane();

			if ( this.options.draggable && $.fn.draggable ) {
				this._makeDraggable();
			}
			if ( this.options.resizable && $.fn.resizable ) {
				this._makeResizable();
			}

			this._isOpen = false;

			this._trackFocus();
		},

		_init: function () {
			if ( this.options.autoOpen ) {
				this.open();
			}
		},

		_appendTo: function () {
			var element = this.options.appendTo;
			if ( element && (
				element.jquery || element.nodeType
				) ) {
				return $( element );
			}
			return this.document.find( element || "body" ).eq( 0 );
		},

		_destroy: function () {
			var next,
				originalPosition = this.originalPosition;

			this._untrackInstance();
			this._destroyOverlay();

			this.element
					.removeUniqueId()
					.removeClass( "ppb-dialog-content ppb-widget-content" )
					.css( this.originalCss )
					// Without detaching first, the following becomes really slow
					.detach();

			this.uiDialog.stop( true, true ).remove();

			if ( this.originalTitle ) {
				this.element.attr( "title", this.originalTitle );
			}

			next = originalPosition.parent.children().eq( originalPosition.index );
			// Don't try to place the dialog next to itself (#8613)
			if ( next.length && next[0] !== this.element[0] ) {
				next.before( this.element );
			} else {
				originalPosition.parent.append( this.element );
			}
		},

		widget: function () {
			return this.uiDialog;
		},

		disable: $.noop,
		enable: $.noop,

		close: function ( event ) {
			var activeElement,
				that = this;

			if ( ! this._isOpen || this._trigger( "beforeClose", event ) === false ) {
				return;
			}

			this._isOpen = false;
			this._focusedElement = null;
			this._destroyOverlay();
			this._untrackInstance();

			if ( ! this.opener.filter( ":focusable" ).focus().length ) {

				// support: IE9
				// IE9 throws an "Unspecified error" accessing document.activeElement from an <iframe>
				try {
					activeElement = this.document[0].activeElement;

					// Support: IE9, IE10
					// If the <body> is blurred, IE will switch windows, see #4520
					if ( activeElement && activeElement.nodeName.toLowerCase() !== "body" ) {

						// Hiding a focused element doesn't trigger blur in WebKit
						// so in case we have nothing to focus on, explicitly blur the active element
						// https://bugs.webkit.org/show_bug.cgi?id=47182
						$( activeElement ).blur();
					}
				} catch ( error ) {
				}
			}

			this._hide( this.uiDialog, this.options.hide, function () {
				that._trigger( "close", event );
			} );
		},

		isOpen: function () {
			return this._isOpen;
		},

		moveToTop: function () {
			this._moveToTop();
		},

		_moveToTop: function ( event, silent ) {
			var moved = false,
				zIndices = this.uiDialog.siblings( ".ppb-front:visible" ).map( function () {
					return + $( this ).css( "z-index" );
				} ).get(),
				zIndexMax = Math.max.apply( null, zIndices );

			if ( zIndexMax >= + this.uiDialog.css( "z-index" ) ) {
				this.uiDialog.css( "z-index", zIndexMax + 1 );
				moved = true;
			}

			if ( moved && ! silent ) {
				this._trigger( "focus", event );
			}
			return moved;
		},

		open: function () {
			var that = this;
			if ( this._isOpen ) {
				if ( this._moveToTop() ) {
					this._focusTabbable();
				}
				return;
			}

			this._isOpen = true;
			this.opener = $( this.document[0].activeElement );

			this._size();
			this._position();
			this._createOverlay();
			this._moveToTop( null, true );

			// Ensure the overlay is moved to the top with the dialog, but only when
			// opening. The overlay shouldn't move after the dialog is open so that
			// modeless dialogs opened after the modal dialog stack properly.
			this.overlay.css( "z-index", this.uiDialog.css( "z-index" ) - 1 );

			this._show( this.uiDialog, this.options.show, function () {
				that._focusTabbable();
				that._trigger( "focus" );
			} );

			// Track the dialog immediately upon openening in case a focus event
			// somehow occurs outside of the dialog before an element inside the
			// dialog is focused (#10152)
			this._makeFocusTarget();

			this._trigger( "open" );
		},

		_focusTabbable: function () {
			// Set focus to the first match:
			// 1. An element that was focused previously
			// 2. First element inside the dialog matching [autofocus]
			// 3. Tabbable element inside the content element
			// 4. Tabbable element inside the buttonpane
			// 5. The close button
			// 6. The dialog itself
			var hasFocus = this._focusedElement;
			if ( ! hasFocus ) {
				hasFocus = this.element.find( "[autofocus]" );
			}
			if ( ! hasFocus.length ) {
				hasFocus = this.element.find( ":tabbable" );
			}
			if ( ! hasFocus.length ) {
				hasFocus = this.uiDialogButtonPane.find( ":tabbable" );
			}
			if ( ! hasFocus.length ) {
				hasFocus = this.uiDialogTitlebarClose.filter( ":tabbable" );
			}
			if ( ! hasFocus.length ) {
				hasFocus = this.uiDialog;
			}
			hasFocus.eq( 0 ).focus();
		},

		_keepFocus: function ( event ) {
			function checkFocus() {
				var activeElement = this.document[0].activeElement,
					isActive = this.uiDialog[0] === activeElement ||
										 $.contains( this.uiDialog[0], activeElement );
				if ( ! isActive ) {
					this._focusTabbable();
				}
			}

			event.preventDefault();
			checkFocus.call( this );
			// support: IE
			// IE <= 8 doesn't prevent moving focus even with event.preventDefault()
			// so we check again later
			this._delay( checkFocus );
		},

		_createWrapper: function () {
			this.uiDialog = $( "<div>" )
				.addClass( "ppb-dialog ppb-widget ppb-widget-content ppb-corner-all ppb-front " +
									 this.options.dialogClass )
				.hide()
				.attr( {
					// Setting tabIndex makes the div focusable
					tabIndex: - 1,
					role: "dialog"
				} )
				.appendTo( this._appendTo() );

			this._on( this.uiDialog, {
				keydown: function ( event ) {
					if ( this.options.closeOnEscape && ! event.isDefaultPrevented() && event.keyCode &&
							 event.keyCode === $.ui.keyCode.ESCAPE ) {
						event.preventDefault();
						this.close( event );
						return;
					}

					// prevent tabbing out of dialogs
					if ( event.keyCode !== $.ui.keyCode.TAB || event.isDefaultPrevented() ) {
						return;
					}
					var tabbables = this.uiDialog.find( ":tabbable" ),
						first = tabbables.filter( ":first" ),
						last = tabbables.filter( ":last" );

					if ( (
							 event.target === last[0] || event.target === this.uiDialog[0]
							 ) && ! event.shiftKey ) {
						this._delay( function () {
							first.focus();
						} );
						event.preventDefault();
					} else if ( (
											event.target === first[0] || event.target === this.uiDialog[0]
											) && event.shiftKey ) {
						this._delay( function () {
							last.focus();
						} );
						event.preventDefault();
					}
				},
				mousedown: function ( event ) {
					if ( this._moveToTop( event ) ) {
						this._focusTabbable();
					}
				}
			} );

			// We assume that any existing aria-describedby attribute means
			// that the dialog content is marked up properly
			// otherwise we brute force the content as the description
			if ( ! this.element.find( "[aria-describedby]" ).length ) {
				this.uiDialog.attr( {
					"aria-describedby": this.element.uniqueId().attr( "id" )
				} );
			}
		},

		_createTitlebar: function () {
			var uiDialogTitle;

			this.uiDialogTitlebar = $( "<div>" )
				.addClass( "ppb-dialog-titlebar ppb-widget-header ppb-corner-all ppb-helper-clearfix" )
				.prependTo( this.uiDialog );
			this._on( this.uiDialogTitlebar, {
				mousedown: function ( event ) {
					// Don't prevent click on close button (#8838)
					// Focusing a dialog that is partially scrolled out of view
					// causes the browser to scroll it into view, preventing the click event
					if ( ! $( event.target ).closest( ".ppb-dialog-titlebar-close" ) ) {
						// Dialog isn't getting focus when dragging (#8063)
						this.uiDialog.focus();
					}
				}
			} );

			// support: IE
			// Use type="button" to prevent enter keypresses in textboxes from closing the
			// dialog in IE (#9312)
			this.uiDialogTitlebarClose = $( "<button type='button'></button>" )
				.button( {
					label: this.options.closeText,
					icons: {
						primary: "ppb-icon-closethick"
					},
					text: false
				} )
				.addClass( "ppb-dialog-titlebar-close" )
				.appendTo( this.uiDialogTitlebar );
			this._on( this.uiDialogTitlebarClose, {
				click: function ( event ) {
					event.preventDefault();
					this.close( event );
				}
			} );

			uiDialogTitle = $( "<span>" )
				.uniqueId()
				.addClass( "ppb-dialog-title" )
				.prependTo( this.uiDialogTitlebar );
			this._title( uiDialogTitle );

			this.uiDialog.attr( {
				"aria-labelledby": uiDialogTitle.attr( "id" )
			} );
		},

		_title: function ( title ) {
			if ( ! this.options.title ) {
				title.html( "&#160;" );
			}
			title.text( this.options.title );
		},

		_createButtonPane: function () {
			this.uiDialogButtonPane = $( "<div>" )
				.addClass( "ppb-dialog-buttonpane ppb-widget-content ppb-helper-clearfix" );

			this.uiButtonSet = $( "<div>" )
				.addClass( "ppb-dialog-buttonset" )
				.appendTo( this.uiDialogButtonPane );

			this._createButtons();
		},

		_createButtons: function () {
			var that = this,
				buttons = this.options.buttons;

			// if we already have a button pane, remove it
			this.uiDialogButtonPane.remove();
			this.uiButtonSet.empty();

			if ( $.isEmptyObject( buttons ) || (
				$.isArray( buttons ) && ! buttons.length
				) ) {
				this.uiDialog.removeClass( "ppb-dialog-buttons" );
				return;
			}

			$.each( buttons, function ( name, props ) {
				var click, buttonOptions;
				props = $.isFunction( props ) ?
					{click: props, text: name} :
					props;
				// Default to a non-submitting button
				props = $.extend( {type: "button"}, props );
				// Change the context for the click callback to be the main element
				click = props.click;
				props.click = function () {
					click.apply( that.element[0], arguments );
				};
				buttonOptions = {
					icons: props.icons,
					text: props.showText
				};
				delete props.icons;
				delete props.showText;
				$( "<button></button>", props )
					.button( buttonOptions )
					.appendTo( that.uiButtonSet );
			} );
			this.uiDialog.addClass( "ppb-dialog-buttons" );
			this.uiDialogButtonPane.appendTo( this.uiDialog );
		},

		_makeDraggable: function () {
			var that = this,
				options = this.options;

			function filteredUi( ui ) {
				return {
					position: ui.position,
					offset: ui.offset
				};
			}

			this.uiDialog.draggable( {
				cancel: ".ppb-dialog-content, .ppb-dialog-titlebar-close",
				handle: ".ppb-dialog-titlebar",
				containment: "document",
				start: function ( event, ui ) {
					$( this ).addClass( "ppb-dialog-dragging" );
					that._blockFrames();
					that._trigger( "dragStart", event, filteredUi( ui ) );
				},
				drag: function ( event, ui ) {
					that._trigger( "drag", event, filteredUi( ui ) );
				},
				stop: function ( event, ui ) {
					var left = ui.offset.left - that.document.scrollLeft(),
						top = ui.offset.top - that.document.scrollTop();

					options.position = {
						my: "left top",
						at: "left" + (
							left >= 0 ? "+" : ""
						) + left + " " +
								"top" + (
									top >= 0 ? "+" : ""
								) + top,
						of: that.window
					};
					$( this ).removeClass( "ppb-dialog-dragging" );
					that._unblockFrames();
					that._trigger( "dragStop", event, filteredUi( ui ) );
				}
			} );
		},

		_makeResizable: function () {
			var that = this,
				options = this.options,
				handles = options.resizable,
				// .ppb-resizable has position: relative defined in the stylesheet
				// but dialogs have to use absolute or fixed positioning
				position = this.uiDialog.css( "position" ),
				resizeHandles = typeof handles === "string" ?
					handles :
					"n,e,s,w,se,sw,ne,nw";

			function filteredUi( ui ) {
				return {
					originalPosition: ui.originalPosition,
					originalSize: ui.originalSize,
					position: ui.position,
					size: ui.size
				};
			}

			this.uiDialog.resizable( {
				cancel: ".ppb-dialog-content",
				containment: "document",
				alsoResize: this.element,
				maxWidth: options.maxWidth,
				maxHeight: options.maxHeight,
				minWidth: options.minWidth,
				minHeight: this._minHeight(),
				handles: resizeHandles,
				start: function ( event, ui ) {
					$( this ).addClass( "ppb-dialog-resizing" );
					that._blockFrames();
					that._trigger( "resizeStart", event, filteredUi( ui ) );
				},
				resize: function ( event, ui ) {
					that._trigger( "resize", event, filteredUi( ui ) );
				},
				stop: function ( event, ui ) {
					var offset = that.uiDialog.offset(),
						left = offset.left - that.document.scrollLeft(),
						top = offset.top - that.document.scrollTop();

					options.height = that.uiDialog.height();
					options.width = that.uiDialog.width();
					options.position = {
						my: "left top",
						at: "left" + (
							left >= 0 ? "+" : ""
						) + left + " " +
								"top" + (
									top >= 0 ? "+" : ""
								) + top,
						of: that.window
					};
					$( this ).removeClass( "ppb-dialog-resizing" );
					that._unblockFrames();
					that._trigger( "resizeStop", event, filteredUi( ui ) );
				}
			} )
					.css( "position", position );
		},

		_trackFocus: function () {
			this._on( this.widget(), {
				focusin: function ( event ) {
					this._makeFocusTarget();
					this._focusedElement = $( event.target );
				}
			} );
		},

		_makeFocusTarget: function () {
			this._untrackInstance();
			this._trackingInstances().unshift( this );
		},

		_untrackInstance: function () {
			var instances = this._trackingInstances(),
				exists = $.inArray( this, instances );
			if ( exists !== - 1 ) {
				instances.splice( exists, 1 );
			}
		},

		_trackingInstances: function () {
			var instances = this.document.data( "ppb-dialog-instances" );
			if ( ! instances ) {
				instances = [];
				this.document.data( "ppb-dialog-instances", instances );
			}
			return instances;
		},

		_minHeight: function () {
			var options = this.options;

			return options.height === "auto" ?
				options.minHeight :
				Math.min( options.minHeight, options.height );
		},

		_position: function () {
			// Need to show the dialog to get the actual offset in the position plugin
			var isVisible = this.uiDialog.is( ":visible" );
			if ( ! isVisible ) {
				this.uiDialog.show();
			}
			this.uiDialog.position( this.options.position );
			if ( ! isVisible ) {
				this.uiDialog.hide();
			}
		},

		_setOptions: function ( options ) {
			var that = this,
				resize = false,
				resizableOptions = {};

			$.each( options, function ( key, value ) {
				that._setOption( key, value );

				if ( key in that.sizeRelatedOptions ) {
					resize = true;
				}
				if ( key in that.resizableRelatedOptions ) {
					resizableOptions[key] = value;
				}
			} );

			if ( resize ) {
				this._size();
				this._position();
			}
			if ( this.uiDialog.is( ":data(ppb-resizable)" ) ) {
				this.uiDialog.resizable( "option", resizableOptions );
			}
		},

		_setOption: function ( key, value ) {
			var isDraggable, isResizable,
				uiDialog = this.uiDialog;

			if ( key === "dialogClass" ) {
				uiDialog
					.removeClass( this.options.dialogClass )
					.addClass( value );
			}

			if ( key === "disabled" ) {
				return;
			}

			this._super( key, value );

			if ( key === "appendTo" ) {
				this.uiDialog.appendTo( this._appendTo() );
			}

			if ( key === "buttons" ) {
				this._createButtons();
			}

			if ( key === "closeText" ) {
				this.uiDialogTitlebarClose.button( {
					// Ensure that we always pass a string
					label: "" + value
				} );
			}

			if ( key === "draggable" ) {
				isDraggable = uiDialog.is( ":data(ppb-draggable)" );
				if ( isDraggable && ! value ) {
					uiDialog.draggable( "destroy" );
				}

				if ( ! isDraggable && value ) {
					this._makeDraggable();
				}
			}

			if ( key === "position" ) {
				this._position();
			}

			if ( key === "resizable" ) {
				// currently resizable, becoming non-resizable
				isResizable = uiDialog.is( ":data(ppb-resizable)" );
				if ( isResizable && ! value ) {
					uiDialog.resizable( "destroy" );
				}

				// currently resizable, changing handles
				if ( isResizable && typeof value === "string" ) {
					uiDialog.resizable( "option", "handles", value );
				}

				// currently non-resizable, becoming resizable
				if ( ! isResizable && value !== false ) {
					this._makeResizable();
				}
			}

			if ( key === "title" ) {
				this._title( this.uiDialogTitlebar.find( ".ppb-dialog-title" ) );
			}
		},

		_size: function () {
			// If the user has resized the dialog, the .ppb-dialog and .ppb-dialog-content
			// divs will both have width and height set, so we need to reset them
			var nonContentHeight, minContentHeight, maxContentHeight,
				options = this.options;

			// Reset content sizing
			this.element.show().css( {
				width: "auto",
				minHeight: 0,
				maxHeight: "none",
				height: 0
			} );

			if ( options.minWidth > options.width ) {
				options.width = options.minWidth;
			}

			// reset wrapper sizing
			// determine the height of all the non-content elements
			nonContentHeight = this.uiDialog.css( {
				height: "auto",
				width: options.width
			} )
														 .outerHeight();
			minContentHeight = Math.max( 0, options.minHeight - nonContentHeight );
			maxContentHeight = typeof options.maxHeight === "number" ?
				Math.max( 0, options.maxHeight - nonContentHeight ) :
				"none";

			if ( options.height === "auto" ) {
				this.element.css( {
					minHeight: minContentHeight,
					maxHeight: maxContentHeight,
					height: "auto"
				} );
			} else {
				this.element.height( Math.max( 0, options.height - nonContentHeight ) );
			}

			if ( this.uiDialog.is( ":data(ppb-resizable)" ) ) {
				this.uiDialog.resizable( "option", "minHeight", this._minHeight() );
			}
		},

		_blockFrames: function () {
			this.iframeBlocks = this.document.find( "iframe" ).map( function () {
				var iframe = $( this );

				return $( "<div>" )
					.css( {
						position: "absolute",
						width: iframe.outerWidth(),
						height: iframe.outerHeight()
					} )
					.appendTo( iframe.parent() )
					.offset( iframe.offset() )[0];
			} );
		},

		_unblockFrames: function () {
			if ( this.iframeBlocks ) {
				this.iframeBlocks.remove();
				delete this.iframeBlocks;
			}
		},

		_allowInteraction: function ( event ) {
			if ( $( event.target ).closest( ".ppb-dialog" ).length ) {
				return true;
			}

			// TODO: Remove hack when datepicker implements
			// the .ppb-front logic (#8989)
			return ! ! $( event.target ).closest( ".ppb-datepicker" ).length;
		},

		_createOverlay: function () {
			// We use a delay in case the overlay is created from an
			// event that we're going to be cancelling (#2804)
			var isOpening = true;
			this._delay( function () {
				isOpening = false;
			} );

			if ( ! this.document.data( "ppb-dialog-overlays" ) ) {

				// Prevent use of anchors and inputs
				// Using _on() for an event handler shared across many instances is
				// safe because the dialogs stack and must be closed in reverse order
				this._on( this.document, {
					focusin: function ( event ) {
						if ( isOpening ) {
							return;
						}

						if ( ! this._allowInteraction( event ) ) {
							event.preventDefault();
							this._trackingInstances()[0]._focusTabbable();
						}
					}
				} );
			}

			this.overlay = $( "<div>" )
				.addClass( "ppb-widget-overlay ppb-front" )
				.appendTo( this._appendTo() );
			this._on( this.overlay, {
				mousedown: "_keepFocus"
			} );
			this.document.data( "ppb-dialog-overlays",
				(
				this.document.data( "ppb-dialog-overlays" ) || 0
				) + 1 );
		},

		_destroyOverlay: function () {
			if ( this.overlay ) {
				var overlays = this.document.data( "ppb-dialog-overlays" ) - 1;

				if ( ! overlays ) {
					this.document
							.unbind( "focusin" )
							.removeData( "ppb-dialog-overlays" );
				} else {
					this.document.data( "ppb-dialog-overlays", overlays );
				}

				this.overlay.remove();
				this.overlay = null;
			}
		}
	} );

	/*!
	 * jQuery UI Tabs 1.11.4
	 * http://jqueryui.com
	 *
	 * Copyright jQuery Foundation and other contributors
	 * Released under the MIT license.
	 * http://jquery.org/license
	 *
	 * http://api.jqueryui.com/tabs/
	 */
	var tabs = $.widget( "ppb.ppbTabs", {
		version: "1.11.4",
		delay: 300,
		options: {
			active: null,
			collapsible: false,
			event: "click",
			heightStyle: "content",
			hide: null,
			show: null,

			// callbacks
			activate: null,
			beforeActivate: null,
			beforeLoad: null,
			load: null
		},

		_isLocal: (
			function () {
				var rhash = /#.*$/;

				return function ( anchor ) {
					var anchorUrl, locationUrl;

					// support: IE7
					// IE7 doesn't normalize the href property when set via script (#9317)
					anchor = anchor.cloneNode( false );

					anchorUrl = anchor.href.replace( rhash, "" );
					locationUrl = location.href.replace( rhash, "" );

					// decoding may throw an error if the URL isn't UTF-8 (#9518)
					try {
						anchorUrl = decodeURIComponent( anchorUrl );
					} catch ( error ) {
					}
					try {
						locationUrl = decodeURIComponent( locationUrl );
					} catch ( error ) {
					}

					return anchor.hash.length > 1 && anchorUrl === locationUrl;
				};
			}
		)(),

		_create: function () {
			var that = this,
				options = this.options;

			this.running = false;

			this.element
					.addClass( "ppb-tabs ppb-widget ppb-widget-content ppb-corner-all" )
					.toggleClass( "ppb-tabs-collapsible", options.collapsible );

			this._processTabs();
			options.active = this._initialActive();

			// Take disabling tabs via class attribute from HTML
			// into account and update option properly.
			if ( $.isArray( options.disabled ) ) {
				options.disabled = $.unique( options.disabled.concat(
					$.map( this.tabs.filter( ".ppb-state-disabled" ), function ( li ) {
						return that.tabs.index( li );
					} )
				) ).sort();
			}

			// check for length avoids error when initializing empty list
			if ( this.options.active !== false && this.anchors.length ) {
				this.active = this._findActive( options.active );
			} else {
				this.active = $();
			}

			this._refresh();

			if ( this.active.length ) {
				this.load( options.active );
			}
		},

		_initialActive: function () {
			var active = this.options.active,
				collapsible = this.options.collapsible,
				locationHash = location.hash.substring( 1 );

			if ( active === null ) {
				// check the fragment identifier in the URL
				if ( locationHash ) {
					this.tabs.each( function ( i, tab ) {
						if ( $( tab ).attr( "aria-controls" ) === locationHash ) {
							active = i;
							return false;
						}
					} );
				}

				// check for a tab marked active via a class
				if ( active === null ) {
					active = this.tabs.index( this.tabs.filter( ".ppb-tabs-active" ) );
				}

				// no active tab, set to false
				if ( active === null || active === - 1 ) {
					active = this.tabs.length ? 0 : false;
				}
			}

			// handle numbers: negative, out of range
			if ( active !== false ) {
				active = this.tabs.index( this.tabs.eq( active ) );
				if ( active === - 1 ) {
					active = collapsible ? false : 0;
				}
			}

			// don't allow collapsible: false and active: false
			if ( ! collapsible && active === false && this.anchors.length ) {
				active = 0;
			}

			return active;
		},

		_getCreateEventData: function () {
			return {
				tab: this.active,
				panel: ! this.active.length ? $() : this._getPanelForTab( this.active )
			};
		},

		_tabKeydown: function ( event ) {
			var focusedTab = $( this.document[0].activeElement ).closest( "li" ),
				selectedIndex = this.tabs.index( focusedTab ),
				goingForward = true;

			if ( this._handlePageNav( event ) ) {
				return;
			}

			switch ( event.keyCode ) {
				case $.ui.keyCode.RIGHT:
				case $.ui.keyCode.DOWN:
					selectedIndex ++;
					break;
				case $.ui.keyCode.UP:
				case $.ui.keyCode.LEFT:
					goingForward = false;
					selectedIndex --;
					break;
				case $.ui.keyCode.END:
					selectedIndex = this.anchors.length - 1;
					break;
				case $.ui.keyCode.HOME:
					selectedIndex = 0;
					break;
				case $.ui.keyCode.SPACE:
					// Activate only, no collapsing
					event.preventDefault();
					clearTimeout( this.activating );
					this._activate( selectedIndex );
					return;
				case $.ui.keyCode.ENTER:
					// Toggle (cancel delayed activation, allow collapsing)
					event.preventDefault();
					clearTimeout( this.activating );
					// Determine if we should collapse or activate
					this._activate( selectedIndex === this.options.active ? false : selectedIndex );
					return;
				default:
					return;
			}

			// Focus the appropriate tab, based on which key was pressed
			event.preventDefault();
			clearTimeout( this.activating );
			selectedIndex = this._focusNextTab( selectedIndex, goingForward );

			// Navigating with control/command key will prevent automatic activation
			if ( ! event.ctrlKey && ! event.metaKey ) {

				// Update aria-selected immediately so that AT think the tab is already selected.
				// Otherwise AT may confuse the user by stating that they need to activate the tab,
				// but the tab will already be activated by the time the announcement finishes.
				focusedTab.attr( "aria-selected", "false" );
				this.tabs.eq( selectedIndex ).attr( "aria-selected", "true" );

				this.activating = this._delay( function () {
					this.option( "active", selectedIndex );
				}, this.delay );
			}
		},

		_panelKeydown: function ( event ) {
			if ( this._handlePageNav( event ) ) {
				return;
			}

			// Ctrl+up moves focus to the current tab
			if ( event.ctrlKey && event.keyCode === $.ui.keyCode.UP ) {
				event.preventDefault();
				this.active.focus();
			}
		},

		// Alt+page up/down moves focus to the previous/next tab (and activates)
		_handlePageNav: function ( event ) {
			if ( event.altKey && event.keyCode === $.ui.keyCode.PAGE_UP ) {
				this._activate( this._focusNextTab( this.options.active - 1, false ) );
				return true;
			}
			if ( event.altKey && event.keyCode === $.ui.keyCode.PAGE_DOWN ) {
				this._activate( this._focusNextTab( this.options.active + 1, true ) );
				return true;
			}
		},

		_findNextTab: function ( index, goingForward ) {
			var lastTabIndex = this.tabs.length - 1;

			function constrain() {
				if ( index > lastTabIndex ) {
					index = 0;
				}
				if ( index < 0 ) {
					index = lastTabIndex;
				}
				return index;
			}

			while ( $.inArray( constrain(), this.options.disabled ) !== - 1 ) {
				index = goingForward ? index + 1 : index - 1;
			}

			return index;
		},

		_focusNextTab: function ( index, goingForward ) {
			index = this._findNextTab( index, goingForward );
			this.tabs.eq( index ).focus();
			return index;
		},

		_setOption: function ( key, value ) {
			if ( key === "active" ) {
				// _activate() will handle invalid values and update this.options
				this._activate( value );
				return;
			}

			if ( key === "disabled" ) {
				// don't use the widget factory's disabled handling
				this._setupDisabled( value );
				return;
			}

			this._super( key, value );

			if ( key === "collapsible" ) {
				this.element.toggleClass( "ppb-tabs-collapsible", value );
				// Setting collapsible: false while collapsed; open first panel
				if ( ! value && this.options.active === false ) {
					this._activate( 0 );
				}
			}

			if ( key === "event" ) {
				this._setupEvents( value );
			}

			if ( key === "heightStyle" ) {
				this._setupHeightStyle( value );
			}
		},

		_sanitizeSelector: function ( hash ) {
			return hash ? hash.replace( /[!"$%&'()*+,.\/:;<=>?@\[\]\^`{|}~]/g, "\\$&" ) : "";
		},

		refresh: function () {
			var options = this.options,
				lis = this.tablist.children( ":has(a[href])" );

			// get disabled tabs from class attribute from HTML
			// this will get converted to a boolean if needed in _refresh()
			options.disabled = $.map( lis.filter( ".ppb-state-disabled" ), function ( tab ) {
				return lis.index( tab );
			} );

			this._processTabs();

			// was collapsed or no tabs
			if ( options.active === false || ! this.anchors.length ) {
				options.active = false;
				this.active = $();
				// was active, but active tab is gone
			} else if ( this.active.length && ! $.contains( this.tablist[0], this.active[0] ) ) {
				// all remaining tabs are disabled
				if ( this.tabs.length === options.disabled.length ) {
					options.active = false;
					this.active = $();
					// activate previous tab
				} else {
					this._activate( this._findNextTab( Math.max( 0, options.active - 1 ), false ) );
				}
				// was active, active tab still exists
			} else {
				// make sure active index is correct
				options.active = this.tabs.index( this.active );
			}

			this._refresh();
		},

		_refresh: function () {
			this._setupDisabled( this.options.disabled );
			this._setupEvents( this.options.event );
			this._setupHeightStyle( this.options.heightStyle );

			this.tabs.not( this.active ).attr( {
				"aria-selected": "false",
				"aria-expanded": "false",
				tabIndex: - 1
			} );
			this.panels.not( this._getPanelForTab( this.active ) )
					.hide()
					.attr( {
						"aria-hidden": "true"
					} );

			// Make sure one tab is in the tab order
			if ( ! this.active.length ) {
				this.tabs.eq( 0 ).attr( "tabIndex", 0 );
			} else {
				this.active
						.addClass( "ppb-tabs-active ppb-state-active" )
						.attr( {
							"aria-selected": "true",
							"aria-expanded": "true",
							tabIndex: 0
						} );
				this._getPanelForTab( this.active )
						.show()
						.attr( {
							"aria-hidden": "false"
						} );
			}
		},

		_processTabs: function () {
			var that = this,
				prevTabs = this.tabs,
				prevAnchors = this.anchors,
				prevPanels = this.panels;

			this.tablist = this._getList()
												 .addClass( "ppb-tabs-nav ppb-helper-reset ppb-helper-clearfix ppb-widget-header ppb-corner-all" )
												 .attr( "role", "tablist" )

												 // Prevent users from focusing disabled tabs via click
												 .delegate( "> li", "mousedown" + this.eventNamespace, function ( event ) {
													 if ( $( this ).is( ".ppb-state-disabled" ) ) {
														 event.preventDefault();
													 }
												 } )

												 // support: IE <9
												 // Preventing the default action in mousedown doesn't prevent IE
												 // from focusing the element, so if the anchor gets focused, blur.
												 // We don't have to worry about focusing the previously focused
												 // element since clicking on a non-focusable element should focus
												 // the body anyway.
												 .delegate( ".ppb-tabs-anchor", "focus" + this.eventNamespace, function () {
													 if ( $( this ).closest( "li" ).is( ".ppb-state-disabled" ) ) {
														 this.blur();
													 }
												 } );

			this.tabs = this.tablist.find( "> li:has(a[href])" )
											.addClass( "ppb-state-default ppb-corner-top" )
											.attr( {
												role: "tab",
												tabIndex: - 1
											} );

			this.anchors = this.tabs.map( function () {
				return $( "a", this )[0];
			} )
												 .addClass( "ppb-tabs-anchor" )
												 .attr( {
													 role: "presentation",
													 tabIndex: - 1
												 } );

			this.panels = $();

			this.anchors.each( function ( i, anchor ) {
				var selector, panel, panelId,
					anchorId = $( anchor ).uniqueId().attr( "id" ),
					tab = $( anchor ).closest( "li" ),
					originalAriaControls = tab.attr( "aria-controls" );

				// inline tab
				if ( that._isLocal( anchor ) ) {
					selector = anchor.hash;
					panelId = selector.substring( 1 );
					panel = that.element.find( that._sanitizeSelector( selector ) );
					// remote tab
				} else {
					// If the tab doesn't already have aria-controls,
					// generate an id by using a throw-away element
					panelId = tab.attr( "aria-controls" ) || $( {} ).uniqueId()[0].id;
					selector = "#" + panelId;
					panel = that.element.find( selector );
					if ( ! panel.length ) {
						panel = that._createPanel( panelId );
						panel.insertAfter( that.panels[i - 1] || that.tablist );
					}
					panel.attr( "aria-live", "polite" );
				}

				if ( panel.length ) {
					that.panels = that.panels.add( panel );
				}
				if ( originalAriaControls ) {
					tab.data( "ppb-tabs-aria-controls", originalAriaControls );
				}
				tab.attr( {
					"aria-controls": panelId,
					"aria-labelledby": anchorId
				} );
				panel.attr( "aria-labelledby", anchorId );
			} );

			this.panels
					.addClass( "ppb-tabs-panel ppb-widget-content ppb-corner-bottom" )
					.attr( "role", "tabpanel" );

			// Avoid memory leaks (#10056)
			if ( prevTabs ) {
				this._off( prevTabs.not( this.tabs ) );
				this._off( prevAnchors.not( this.anchors ) );
				this._off( prevPanels.not( this.panels ) );
			}
		},

		// allow overriding how to find the list for rare usage scenarios (#7715)
		_getList: function () {
			return this.tablist || this.element.find( "ol,ul" ).eq( 0 );
		},

		_createPanel: function ( id ) {
			return $( "<div>" )
				.attr( "id", id )
				.addClass( "ppb-tabs-panel ppb-widget-content ppb-corner-bottom" )
				.data( "ppb-tabs-destroy", true );
		},

		_setupDisabled: function ( disabled ) {
			if ( $.isArray( disabled ) ) {
				if ( ! disabled.length ) {
					disabled = false;
				} else if ( disabled.length === this.anchors.length ) {
					disabled = true;
				}
			}

			// disable tabs
			for (
				var i = 0, li; (
				li = this.tabs[i]
			); i ++
			) {
				if ( disabled === true || $.inArray( i, disabled ) !== - 1 ) {
					$( li )
						.addClass( "ppb-state-disabled" )
						.attr( "aria-disabled", "true" );
				} else {
					$( li )
						.removeClass( "ppb-state-disabled" )
						.removeAttr( "aria-disabled" );
				}
			}

			this.options.disabled = disabled;
		},

		_setupEvents: function ( event ) {
			var events = {};
			if ( event ) {
				$.each( event.split( " " ), function ( index, eventName ) {
					events[eventName] = "_eventHandler";
				} );
			}

			this._off( this.anchors.add( this.tabs ).add( this.panels ) );
			// Always prevent the default action, even when disabled
			this._on( true, this.anchors, {
				click: function ( event ) {
					event.preventDefault();
				}
			} );
			this._on( this.anchors, events );
			this._on( this.tabs, {keydown: "_tabKeydown"} );
			this._on( this.panels, {keydown: "_panelKeydown"} );

			this._focusable( this.tabs );
			this._hoverable( this.tabs );
		},

		_setupHeightStyle: function ( heightStyle ) {
			var maxHeight,
				parent = this.element.parent();

			if ( heightStyle === "fill" ) {
				maxHeight = parent.height();
				maxHeight -= this.element.outerHeight() - this.element.height();

				this.element.siblings( ":visible" ).each( function () {
					var elem = $( this ),
						position = elem.css( "position" );

					if ( position === "absolute" || position === "fixed" ) {
						return;
					}
					maxHeight -= elem.outerHeight( true );
				} );

				this.element.children().not( this.panels ).each( function () {
					maxHeight -= $( this ).outerHeight( true );
				} );

				this.panels.each( function () {
					$( this ).height( Math.max( 0, maxHeight -
																				 $( this ).innerHeight() + $( this ).height() ) );
				} )
						.css( "overflow", "auto" );
			} else if ( heightStyle === "auto" ) {
				maxHeight = 0;
				this.panels.each( function () {
					maxHeight = Math.max( maxHeight, $( this ).height( "" ).height() );
				} ).height( maxHeight );
			}
		},

		_eventHandler: function ( event ) {
			var options = this.options,
				active = this.active,
				anchor = $( event.currentTarget ),
				tab = anchor.closest( "li" ),
				clickedIsActive = tab[0] === active[0],
				collapsing = clickedIsActive && options.collapsible,
				toShow = collapsing ? $() : this._getPanelForTab( tab ),
				toHide = ! active.length ? $() : this._getPanelForTab( active ),
				eventData = {
					oldTab: active,
					oldPanel: toHide,
					newTab: collapsing ? $() : tab,
					newPanel: toShow
				};

			event.preventDefault();

			if ( tab.hasClass( "ppb-state-disabled" ) ||
					 // tab is already loading
					 tab.hasClass( "ppb-tabs-loading" ) ||
					 // can't switch durning an animation
					 this.running ||
					 // click on active header, but not collapsible
					 (
					 clickedIsActive && ! options.collapsible
					 ) ||
					 // allow canceling activation
					 (
					 this._trigger( "beforeActivate", event, eventData ) === false
					 ) ) {
				return;
			}

			options.active = collapsing ? false : this.tabs.index( tab );

			this.active = clickedIsActive ? $() : tab;
			if ( this.xhr ) {
				this.xhr.abort();
			}

			if ( ! toHide.length && ! toShow.length ) {
				$.error( "jQuery UI Tabs: Mismatching fragment identifier." );
			}

			if ( toShow.length ) {
				this.load( this.tabs.index( tab ), event );
			}
			this._toggle( event, eventData );
		},

		// handles show/hide for selecting tabs
		_toggle: function ( event, eventData ) {
			var that = this,
				toShow = eventData.newPanel,
				toHide = eventData.oldPanel;

			this.running = true;

			function complete() {
				that.running = false;
				that._trigger( "activate", event, eventData );
			}

			function show() {
				eventData.newTab.closest( "li" ).addClass( "ppb-tabs-active ppb-state-active" );

				if ( toShow.length && that.options.show ) {
					that._show( toShow, that.options.show, complete );
				} else {
					toShow.show();
					complete();
				}
			}

			// start out by hiding, then showing, then completing
			if ( toHide.length && this.options.hide ) {
				this._hide( toHide, this.options.hide, function () {
					eventData.oldTab.closest( "li" ).removeClass( "ppb-tabs-active ppb-state-active" );
					show();
				} );
			} else {
				eventData.oldTab.closest( "li" ).removeClass( "ppb-tabs-active ppb-state-active" );
				toHide.hide();
				show();
			}

			toHide.attr( "aria-hidden", "true" );
			eventData.oldTab.attr( {
				"aria-selected": "false",
				"aria-expanded": "false"
			} );
			// If we're switching tabs, remove the old tab from the tab order.
			// If we're opening from collapsed state, remove the previous tab from the tab order.
			// If we're collapsing, then keep the collapsing tab in the tab order.
			if ( toShow.length && toHide.length ) {
				eventData.oldTab.attr( "tabIndex", - 1 );
			} else if ( toShow.length ) {
				this.tabs.filter( function () {
					return $( this ).attr( "tabIndex" ) === 0;
				} )
						.attr( "tabIndex", - 1 );
			}

			toShow.attr( "aria-hidden", "false" );
			eventData.newTab.attr( {
				"aria-selected": "true",
				"aria-expanded": "true",
				tabIndex: 0
			} );
		},

		_activate: function ( index ) {
			var anchor,
				active = this._findActive( index );

			// trying to activate the already active panel
			if ( active[0] === this.active[0] ) {
				return;
			}

			// trying to collapse, simulate a click on the current active header
			if ( ! active.length ) {
				active = this.active;
			}

			anchor = active.find( ".ppb-tabs-anchor" )[0];
			this._eventHandler( {
				target: anchor,
				currentTarget: anchor,
				preventDefault: $.noop
			} );
		},

		_findActive: function ( index ) {
			return index === false ? $() : this.tabs.eq( index );
		},

		_getIndex: function ( index ) {
			// meta-function to give users option to provide a href string instead of a numerical index.
			if ( typeof index === "string" ) {
				index = this.anchors.index( this.anchors.filter( "[href$='" + index + "']" ) );
			}

			return index;
		},

		_destroy: function () {
			if ( this.xhr ) {
				this.xhr.abort();
			}

			this.element.removeClass( "ppb-tabs ppb-widget ppb-widget-content ppb-corner-all ppb-tabs-collapsible" );

			this.tablist
					.removeClass( "ppb-tabs-nav ppb-helper-reset ppb-helper-clearfix ppb-widget-header ppb-corner-all" )
					.removeAttr( "role" );

			this.anchors
					.removeClass( "ppb-tabs-anchor" )
					.removeAttr( "role" )
					.removeAttr( "tabIndex" )
					.removeUniqueId();

			this.tablist.unbind( this.eventNamespace );

			this.tabs.add( this.panels ).each( function () {
				if ( $.data( this, "ppb-tabs-destroy" ) ) {
					$( this ).remove();
				} else {
					$( this )
						.removeClass( "ppb-state-default ppb-state-active ppb-state-disabled " +
													"ppb-corner-top ppb-corner-bottom ppb-widget-content ppb-tabs-active ppb-tabs-panel" )
						.removeAttr( "tabIndex" )
						.removeAttr( "aria-live" )
						.removeAttr( "aria-busy" )
						.removeAttr( "aria-selected" )
						.removeAttr( "aria-labelledby" )
						.removeAttr( "aria-hidden" )
						.removeAttr( "aria-expanded" )
						.removeAttr( "role" );
				}
			} );

			this.tabs.each( function () {
				var li = $( this ),
					prev = li.data( "ppb-tabs-aria-controls" );
				if ( prev ) {
					li
						.attr( "aria-controls", prev )
						.removeData( "ppb-tabs-aria-controls" );
				} else {
					li.removeAttr( "aria-controls" );
				}
			} );

			this.panels.show();

			if ( this.options.heightStyle !== "content" ) {
				this.panels.css( "height", "" );
			}
		},

		enable: function ( index ) {
			var disabled = this.options.disabled;
			if ( disabled === false ) {
				return;
			}

			if ( index === undefined ) {
				disabled = false;
			} else {
				index = this._getIndex( index );
				if ( $.isArray( disabled ) ) {
					disabled = $.map( disabled, function ( num ) {
						return num !== index ? num : null;
					} );
				} else {
					disabled = $.map( this.tabs, function ( li, num ) {
						return num !== index ? num : null;
					} );
				}
			}
			this._setupDisabled( disabled );
		},

		disable: function ( index ) {
			var disabled = this.options.disabled;
			if ( disabled === true ) {
				return;
			}

			if ( index === undefined ) {
				disabled = true;
			} else {
				index = this._getIndex( index );
				if ( $.inArray( index, disabled ) !== - 1 ) {
					return;
				}
				if ( $.isArray( disabled ) ) {
					disabled = $.merge( [index], disabled ).sort();
				} else {
					disabled = [index];
				}
			}
			this._setupDisabled( disabled );
		},

		load: function ( index, event ) {
			index = this._getIndex( index );
			var that = this,
				tab = this.tabs.eq( index ),
				anchor = tab.find( ".ppb-tabs-anchor" ),
				panel = this._getPanelForTab( tab ),
				eventData = {
					tab: tab,
					panel: panel
				},
				complete = function ( jqXHR, status ) {
					if ( status === "abort" ) {
						that.panels.stop( false, true );
					}

					tab.removeClass( "ppb-tabs-loading" );
					panel.removeAttr( "aria-busy" );

					if ( jqXHR === that.xhr ) {
						delete that.xhr;
					}
				};

			// not remote
			if ( this._isLocal( anchor[0] ) ) {
				return;
			}

			this.xhr = $.ajax( this._ajaxSettings( anchor, event, eventData ) );

			// support: jQuery <1.8
			// jQuery <1.8 returns false if the request is canceled in beforeSend,
			// but as of 1.8, $.ajax() always returns a jqXHR object.
			if ( this.xhr && this.xhr.statusText !== "canceled" ) {
				tab.addClass( "ppb-tabs-loading" );
				panel.attr( "aria-busy", "true" );

				this.xhr
						.done( function ( response, status, jqXHR ) {
							// support: jQuery <1.8
							// http://bugs.jquery.com/ticket/11778
							setTimeout( function () {
								panel.html( response );
								that._trigger( "load", event, eventData );

								complete( jqXHR, status );
							}, 1 );
						} )
						.fail( function ( jqXHR, status ) {
							// support: jQuery <1.8
							// http://bugs.jquery.com/ticket/11778
							setTimeout( function () {
								complete( jqXHR, status );
							}, 1 );
						} );
			}
		},

		_ajaxSettings: function ( anchor, event, eventData ) {
			var that = this;
			return {
				url: anchor.attr( "href" ),
				beforeSend: function ( jqXHR, settings ) {
					return that._trigger( "beforeLoad", event,
						$.extend( {jqXHR: jqXHR, ajaxSettings: settings}, eventData ) );
				}
			};
		},

		_getPanelForTab: function ( tab ) {
			var id = $( tab ).attr( "aria-controls" );
			return this.element.find( this._sanitizeSelector( "#" + id ) );
		}
	} );

} );