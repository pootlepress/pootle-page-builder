/**
 * Created by shramee on 05/07/16.
 */
jQuery( function ( $ ) {
	$.prototype.tour = function ( slides, options ) {
		var
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
					console.log( el );
					console.log( el.outerHeight() );
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
					console.log( pos );
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
		options.skipText = options.skipText ? options.skipText : "Skip Tour";
		options.moreText = options.moreText ? options.moreText : "More";
		options.finishText = options.moreText ? options.moreText : "Thanks!";

		this.html(
			'<div class="tour-header">' + options.beforeHeading + '<span class="tour-heading">Content Block</span>' + options.afterHeading + '</div>' +
			'<div class="tour-content"></div>' +
			'<div class="tour-footer">' +
			'<a href="javascript:0" class="tour-skip" onclick="jQuery(this).parents(\'#ppb-tour-dialog\').hide()">' + options.skipText + '</a>' +
			'<a href="javascript:0" class="tour-next-slide">' + options.moreText + '</a>' +
			'</div>' );

		$d.find( '.tour-next-slide' ).click( function () {
			$d.hide( 0 );
			if ( $$.slide == slides.length ) {
				return;
			}
			var i = $$.slide;
			console.log(i)
			switch ( i ) {
				case 1: // Adding Row
					if ( ! $addRowDialog.ppbDialog( 'isOpen' ) ) {
						$addRowDialog.ppbDialog( 'open' );
					}
					break;
				case 2:
					if ( $addRowDialog.ppbDialog( 'isOpen' ) ) {
						$( '#pootlepb-add-row' ).siblings( '.ppb-dialog-buttonpane' ).find( 'button' ).click();
					}
					$row.addClass( 'tour-active' );
					break;
				case 3:
					$row.children( '.ppb-edit-row' ).addClass( 'tour-active' );
					break;
				case 5:
					$row.children( '.ppb-edit-row' ).removeClass( 'tour-active' );
					$block.children( '.ppb-edit-block' ).addClass( 'tour-active' );
					break;
				case 9:
					$block.children( '.ppb-edit-block' ).removeClass( 'tour-active' );
					break;
				case 10:
					$( '.ui-resizable-handle.ui-resizable-w' ).eq(1).parents( '.panel-grid' ).addClass( 'tour-active' );
			}
			var el  = slides[i].el,
			    $el = el instanceof jQuery ? el : $( el );
			$$.position( $el );
			$$.heading( slides[i].head );
			$$.content( slides[i].content );
			$$.slide ++;
			if ( $$.slide == slides.length ) {
				$( this ).html( options.finishText );
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
				head    : 'Add row',
				content : 'Click the icon to add a new row and set number of columns.'
			},
			{
				el      : $( '#ppb-row-add-cols' ),
				head    : 'Number of columns',
				content : 'Choose 2 columns for this tour and click \'Done\.<br>We will click it for you when you move to next slide since you can\'t reach it at the moment.'
			},
			{
				el      : $row.children( '.ppb-edit-row' ).find( '.dashicons-editor-code' ),
				head    : 'Row Sorting',
				content : '<ul><li>Drag and drop your row using this icon ­ cool huh?</li><li>Hover over this to make Row Styling and Delete Row icons appear</li><li>You can also doubleclick here to shortcut to  Row Styling panel.</li></ul>'
			},
			{
				el      : $row.children( '.ppb-edit-row' ).find( '.dashicons-admin-appearance' ),
				head    : 'Row Styling',
				content : 'Edit your row here.<ul><li>Set row background: colour, image or video</li><li>Set row layout: full­width, height, margin and gutter</li><li>Set advanced CSS styles.</li></ul>'
			},
			{
				el      : $row.children( '.ppb-edit-row' ).find( '.dashicons-no' ),
				head    : 'Delete Row',
				content : 'Deletes your row . This is undoable so be sure you want to delete your row :)'
			},
			{
				el      : $block.children( '.ppb-edit-block' ).find( '.dashicons-screenoptions' ),
				head    : 'Drag and Drop Content Block',
				content : '<ul><li>Drag and drop your Content Block using this icon ­ cool huh?</li><li>Hover over this to make Edit Content, Add Image and Delete Content Block icons appear</li><li>You can also doubleclick here to shortcut to Edit Content panel.</li></ul>'
			},
			{
				el      : $block.children( '.ppb-edit-block' ).find( '.dashicons-edit' ),
				head    : 'Edit Content',
				content : 'Add content here:<ul><li>Add and edit copy</li><li>Add and edit media</li><li>Style Content Block background: image, colour, transparency</li><li>Style text, border, padding and corners.</li></ul>'
			},
			{
				el      : $block.children( '.ppb-edit-block' ).find( '.dashicons-format-image' ),
				head    : 'Insert Image',
				content : 'Adds image to Content Block.'
			},
			{
				el      : $block.children( '.ppb-edit-block' ).find( '.dashicons-no' ),
				head    : 'Delete Content Block',
				content : 'Deletes your Content Block. This is undoable so be sure you want to delete your Content Block :)'
			},
			{
				el      : $block,
				head    : 'In Content Block',
				content : 'Doubleclick to directly edit your content block ­ snazzy!'
			},
			{
				el      : '.ppb-col + .ppb-col .ui-resizable-handle.ui-resizable-w',
				head    : 'Column drag',
				content : 'Change the width of your columns simply by dragging ­ so easy!'
			},
			{
				el      : '.ppb-live-add-object.add-row',
				head    : 'Finished',
				content : 'Let\'s get cracking :) Click here to start building!'
			}
		],
		{
			beforeHeading : '<h3><span class="dashicons dashicons-lightbulb"></span>',
			afterHeading  : "</h3>",
			skipText      : '<span class="dashicons dashicons-dismiss"></span> I know...',
			moreText      : '<span class="dashicons dashicons-controls-play"></span> Tell me more!',
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