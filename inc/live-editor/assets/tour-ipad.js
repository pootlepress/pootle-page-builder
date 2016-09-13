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
			var i = $$.slide;
			switch ( i ) {
				case 2: // Adding Row
					if ( ! $addRowDialog.ppbDialog( 'isOpen' ) ) {
						$addRowDialog.ppbDialog( 'open' );
					}
					break;
				case 3:
					if ( $addRowDialog.ppbDialog( 'isOpen' ) ) {
						$( '#pootlepb-add-row' ).siblings( '.ppb-dialog-buttonpane' ).find( 'button' ).click();
					}
					$row.addClass( 'tour-active' );
					break;
				case 5:
					$row.children( '.ppb-edit-row' ).addClass( 'tour-active' );
					break;
				case 6:
					$row.children( '.ppb-edit-row' ).removeClass( 'tour-active' );
					$block.children( '.ppb-edit-block' ).addClass( 'tour-active' );
					break;
				case 7:
					$block.children( '.ppb-edit-block' ).removeClass( 'tour-active' );
					$row.removeClass( 'tour-active' );
					$( '.ui-resizable-handle.ui-resizable-w' ).eq(1).parents( '.panel-grid' ).addClass( 'tour-active' );
			}
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
		} ).click();

	};

	var $row          = $( '.panel-grid' ).eq( 0 ),
	    $block        = $row.find( '.ppb-block' ).eq( 0 ),
	    $addRowDialog = $( '#pootlepb-add-row' );

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
				el      : $row.children( '.ppb-edit-row' ).find( '.dashicons-editor-code' ),
				head    : 'Row Sorting',
				content : '<ul><li>Drag and drop your <b>row</b> using this icon &ndash; cool huh?</li><li>Tap this icon to make <i>Delete Row</i> icon appear</li><li><b>Double tap</b> here to open <i>Row Styling</i> panel.</li></ul>'
			},
			{
				el      : $row.children( '.ppb-edit-row' ).find( '.dashicons-no' ),
				head    : 'Delete Row',
				content : 'Deletes your <b>row</b>. This is undoable so be sure you want to delete your <b>row</b> :)'
			},
			{
				el      : $block.children( '.ppb-edit-block' ).find( '.dashicons-move' ),
				head    : 'Drag and Drop Content Block',
				content : '<ul><li>Drag and drop your <b>content block</b> using this icon &ndash; cool huh?</li><li><b>Double tap</b> here to open <i>Edit Content</i> panel.</li></ul>'
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