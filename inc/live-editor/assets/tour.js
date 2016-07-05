/**
 * Created by shramee on 05/07/16.
 */
ppbTourNextSlide = function(){};
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
	    },
	    slides = [
		    {
			    el: $block,
			    head: 'Content block',
			    content: 'This is the content block, click here to edit this, hovering over a content block shows content block and row options.'
		    },
		    {
			    cb: function(){
				    $block.parents('.panel-grid').addClass('tour-active')
			    },
			    el: $block.children('.ppb-edit-block').find('.dashicons-screenoptions'),
			    head: 'Content block settings',
			    content: 'Lorem Ipsum dolor Sit Amet'
		    },
		    {
			    cb: function(){

			    },
			    el: $block.parents('.panel-grid').children('.ppb-edit-row').find('.dashicons-screenoptions'),
			    head: 'Row Settings',
			    content: 'Ipsum_dolor_Sit_Amet'
		    },
		    {
			    cb: function(){

			    },
			    el: $block,
			    head: 'Lorem',
			    content: 'Ipsum_dolor_Sit_Amet'
		    },
		    {
			    cb: function(){

			    },
			    el: $block,
			    head: 'Lorem',
			    content: 'Ipsum_dolor_Sit_Amet'
		    },
		    {
			    cb: function(){

			    },
			    el: $block,
			    head: 'Lorem',
			    content: 'Ipsum_dolor_Sit_Amet'
		    }
	    ];
	$( '.tour-next-slide' ).click( function () {
		$d.hide(0);
		var i = tour.slide;
		if ( typeof slides[i].cb == 'function' ) {
			slides[i].cb();
		}
		var el = slides[i].el,
			$el = el instanceof jQuery ? el : $( el )
		tour.position( $el );
		tour.heading( slides[i].head );
		tour.content( slides[i].content );
		tour.slide ++;
		$d.show(0);
	} ).click();
} );