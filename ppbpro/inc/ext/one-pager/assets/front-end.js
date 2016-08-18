/**
 * Plugin front end scripts
 *
 * @package pootle_page_builder_one_pager
 * @version 1.0.0
 */

jQuery(function ($) {
    var loc = window.location;

    $( '.one-pager-section-marker' ).each( function() {
        var $t = $(this);
        $t.waypoint({
            handler: function(direction) {
                $('a.one-pager-menu-item' ).removeClass('active');
                $('a[href="#' + $(this ).attr('id') + '"]' ).addClass('active');
            },
            offset: 5 + parseInt( $t.data( 'offset' ) )
        });
    } );

    //Smooth scroll
    $(function() {
        $('#one-pager-nav a, .menu-item a[href*="#"]').click(function( e ) {
            e.pre
            if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
                var $target = $(this.hash + '.one-pager-section-marker');
                if ( '' == this.hash ) {
                    $target = $('body');
                }

                if ($target.length) {
                    var offset = 0;
                    if ( $target.data( 'offset' ) ) {
                        offset = parseInt( $target.data( 'offset' ) );
                    }
                    $('html,body').animate({
                        scrollTop: $target.offset().top - offset
                    }, 1000);
                }
            }
        });
    });

});