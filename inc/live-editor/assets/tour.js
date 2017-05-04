/**
 * Created by shramee on 05/07/16.
 */
jQuery( function ( $ ) {
	$.prototype.tour = function ( slides, options ) {
		var
			$w = $(window),
			docW = $(document).width(),
			docH = $(document).height(),
			$d = this.$d = this.addClass( 'tour-dialog' ),
			$$ = this.$$ = {
				slide    : 0,
				heading  : function ( head ) {
					$d.find( '.tour-heading' ).html( head );
				},
				content  : function ( content ) {
					$d.find( '.tour-content' ).html( content );
				},
				position : function ( el ) {
					if ( ! el.length ) {
						return;
					}
					el = el.eq( 0 );
					var
						pos  = el.offset(),
						top  = pos.top + el.outerHeight(),
						left = pos.left + el.outerWidth() / 2;

					$d.removeClass( 'tour-arrow-right tour-arrow-bottom' );

					if ( ( left + $d.outerWidth() ) > docW ) {
						$d.addClass( 'tour-arrow-right' );
						left -= $d.outerWidth();
					}
					$d.css( {
						top  : top,
						left : left
					} );
				}
			};

		if ( ! options ) {
			options = {}
		}

		options.beforeHeading = options.beforeHeading ? options.beforeHeading : "<h3>";
		options.afterHeading = options.afterHeading ? options.afterHeading : "</h3>";
		options.endTourText = options.endTourText ? options.endTourText : "End Tour";
		options.moreText = options.moreText ? options.moreText : "Next";
		options.completeText = options.completeText ? options.completeText : "Thanks!";
		options.beforeSlide = options.beforeSlide ? options.beforeSlide : function(){};
		options.afterSlide = options.afterSlide ? options.afterSlide : function(){};

		this.html(
			'<div class="tour-header"><span class="tour-arrow"></span>' + options.beforeHeading + '<span class="tour-heading">Content Block</span>' + options.afterHeading + '</div>' +
			'<div class="tour-content"></div>' +
			'<div class="tour-footer">' +
			'<a href="javascript:0" class="tour-skip" onclick="jQuery(this).parents(\'#ppb-tour-dialog\').hide()">' + options.endTourText + '</a>' +
			'<a href="javascript:0" class="tour-next-slide">' + options.moreText + '</a>' +
			'</div>' );

		$d.find( '.tour-next-slide' ).click( function () {
			$d.hide( 0 );
			if ( $$.slide == slides.length ) {
				return;
			}
			var
				i = $$.slide,
				delay = 25;

			options.beforeSlide( i );

			if ( typeof slides[i].callback === 'function' ) {
				slides[i].callback( i );
			}
			if ( typeof slides[i].delay === 'number' ) {
				delay = slides[i].delay;
			}

			setTimeout( function () {
				var el  = slides[i].el,
					$el = el instanceof jQuery ? el : $( el );
				$$.position( $el );
				$$.heading( slides[i].head );
				$$.content( slides[i].content );

				if ( 0 == i ) {
					$d.addClass( 'tour-no-arrow' ).css( {
						top : $w.scrollTop() + ( window.innerHeight - $d.innerHeight() ) / 2,
						left: ( window.innerWidth - $d.innerWidth() ) / 2
					} );
				} else if ( 1 == i ) {
					$d.removeClass( 'tour-no-arrow' )
				}

				$$.slide ++;
				if ( $$.slide == slides.length ) {
					$( this ).html( options.completeText );
					$d.find( '.tour-skip' ).hide();
					$( '.tour-active' ).removeClass( 'tour-active' );
				}
				$d.show( 0 );
				options.afterSlide( i );
			}, delay );

		} ).click();

	};
	var $row = $( '.panel-grid' ).eq( 0 ),
		$block = $row.find( '.ppb-block' ).eq( 0 ),
		$addRowDialog = $( '#pootlepb-add-row' ),
		$modules = $( '#pootlepb-modules-wrap' );

	$( '#ppb-tour-dialog' ).tour(
		[
			{
				el      : '.ppb-live-add-object.add-row',
				head    : 'Pootle Pagebuilder Tour',
				content : 'Welcome to our 1 minute tour. Click ‘Next’ to be guided around your page and learn what you can do with Pootle Pagebuilder.'
			},
			{
				el      : '.ppb-live-add-object.add-row',
				head    : 'Add row',
				content : 'Click the ‘+’ icon to add a new row and set number of columns.'
			},
			{
				el      : $( '#ppb-row-add-cols' ),
				head    : 'Number of columns',
				content : 'Select number of required columns.'
			},
			{
				el      : $block,
				head    : 'In Content Block',
				content : 'Click here to directly add copy to your content block &ndash; snazzy!'
			},
			{
				el      : $row.children( '.ppb-edit-row' ).find( '.settings-dialog' ),
				head    : 'Row Styling',
				content : 'Edit your <b>row</b> here.<ul><li>Set <b>row</b> background: colour, image or video</li><li>Set <b>row</b> layout: full&ndash;width, height, margin and gutter</li><li>Set advanced CSS styles.</li></ul>'
			},
			{
				el      : $row.children( '.ppb-edit-row' ).find( '.drag-handle' ),
				head    : 'Row Sorting',
				content : '<ul><li>Drag and drop your <b>row</b> using this icon &ndash; cool huh?</li><li>Hover over this to make <i>Row Styling</i> and <i>Delete Row</i> icons appear</li><li>You can also doubleclick here to shortcut to <i>Row Styling</i> panel.</li></ul>'
			},
			{
				el      : $row.children( '.ppb-edit-row' ).find( '.dashicons-no' ),
				head    : 'Delete Row',
				content : 'Deletes your <b>row</b>. This is undoable so be sure you want to <b>delete your row</b> :)'
			},
			{
				el      : $block.children( '.ppb-edit-block' ).find( '.dashicons-move' ),
				head    : 'Drag and Drop Content Block',
				content : '<ul><li>Drag and drop your <b>content block</b> using this icon &ndash; cool huh?</li><li>Hover over this to make <i>Edit Content</i>, <i>Add Image</i> and <i>Delete Content Block</i> icons appear</li><li>You can also doubleclick here to shortcut to <i>Edit Content</i> panel.</li></ul>'
			},
			{
				el      : $block.children( '.ppb-edit-block' ).find( '.dashicons-edit' ),
				head    : 'Edit Content',
				content : 'Add content here:<ul><li>Add and edit copy</li><li>Add and edit media</li><li>Style <b>content block</b> background: image, colour, transparency</li><li>Style text, border, padding and corners.</li></ul>'
			},
			{
				el      : $block.children( '.ppb-edit-block' ).find( '.dashicons-no' ),
				head    : 'Delete Content',
				content : 'Deletes your <b>content block</b>. This is undoable so be sure you want to delete your <b>content block</b> :)'
			},
			{
				el      : $modules.children( '.dashicons-screenoptions' ),
				head    : 'Drag and drop modules',
				content : 'Click here to show available modules :)'
			},
			{
				el      : $modules.find( '.ppb-module' ).eq(2),
				head    : 'Drag and drop modules',
				content : '<ul><li>Drag and drop module of your choice into any content block</li><li>You can drag module into new row icon to add your module into a new row &ndash; cool huh?</li></ul>',
				delay: 500
			},
			{
				el      : '.ppb-col + .ppb-col .ui-resizable-handle.ui-resizable-w',
				head    : 'Column drag',
				content : 'Change the width of your columns simply by dragging &ndash; so easy!'
			},
			{
				el      : '.ppb-live-add-object.add-row',
				head    : 'Finished',
				content : 'Let\'s get cracking :)'
			}
		],
		{
			beforeHeading : '<h3><span class="dashicons dashicons-lightbulb"></span>',
			afterHeading  : "</h3>",
			endTourText      : '<span class="dashicons dashicons-dismiss"></span> End Tour',
			moreText      : '<span class="dashicons dashicons-controls-play"></span> Next',
			completeText      : '<span class="dashicons dashicons-controls-play"></span> Finish Tour',
			beforeSlide: function( i ) {
				switch ( i ) {
					case 2: // Adding Row
						if ( ! $addRowDialog.ppbDialog( 'isOpen' ) ) {
							$addRowDialog.ppbDialog( 'open' );
							$( '#ppb-row-add-cols' ).val( 2 );
						}
						break;
					case 3:
						if ( $addRowDialog.ppbDialog( 'isOpen' ) ) {
							$( '#ppb-row-add-cols' ).val( 2 );
							$( '#pootlepb-add-row' ).siblings( '.ppb-dialog-buttonpane' ).find( 'button' ).click();
						}
						$row.addClass( 'tour-active' );
						break;
					case 5:
						$row.children( '.ppb-edit-row' ).addClass( 'tour-active' );
						break;
					case 7:
						$row.children( '.ppb-edit-row' ).removeClass( 'tour-active' );
						$block.children( '.ppb-edit-block' ).addClass( 'tour-active' );
						break;
					case 11:
						$modules.children( '.dashicons-screenoptions' ).click();
						break;
					case 12:
						$modules.children( '.dashicons-screenoptions' ).click();
						$block.children( '.ppb-edit-block' ).removeClass( 'tour-active' );
						$row.removeClass( 'tour-active' );
						$( '.ui-resizable-handle.ui-resizable-w' ).eq(1).parents( '.panel-grid' ).addClass( 'tour-active' );
				}
			},
		}
	);
} );
/*
jQuery(document).ready( function ( $ ) {
	var $d = $( '#ppb-tour-dialog' ), // Dialog
	    $row = $( '.panel-grid' ).eq(0),
	    $block = $row.find( '.ppb-block' ).eq(0),
	    tour = {
		    slide     : 0,
		    heading   : function ( head ) {
			    $d.find( '.tour-heading' ).html( head );
		    },
		    content   : function ( content ) {
			    $d.find( '.tour-content' ).html( content );
		    },
		    position  : function ( el ) {
			    if ( ! el.length ) {
				    return;
			    }
			    el = el.eq( 0 );
			    var
				    pos  = el.offset(),
				    top  = pos.top + el.outerHeight(),
				    left = pos.left + (
					    el.outerWidth() / 2
					    );
			    $d.css( {
				    top  : top,
				    left : left
			    } );
		    },
	    };
} );
	*/