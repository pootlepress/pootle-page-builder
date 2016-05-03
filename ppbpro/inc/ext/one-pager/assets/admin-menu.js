/**
 * Created by shramee on 14/8/15.
 */
( function( $ ) {
    $( document).ready( function(){
        $( '#submit-ppb-1pager' ).click( function(){
            var data = {};

            $('.ppb-1pager-url').each(function(){
                var $t = $(this);
                if ( $t.prop('checked') ) {
                    data[$t.data('index')] = {
                        'menu-item-type': 'custom',
                        'menu-item-url': $t.val(),
                        'menu-item-title': $t.siblings('.ppb-1pager-title').val()
                    };
                }
            });

            if ( ! jQuery.isEmptyObject( data ) ) {
                $('.ppb-1pager-div .spinner').show();

                wpNavMenu.addItemToMenu(
                    data,
                    wpNavMenu.addMenuItemToBottom,
                    function () {
                        // Remove the ajax spinner
                        $('.ppb-1pager-div .spinner').hide();
                        // Set custom link form back to defaults
                        $('#custom-menu-item-name').val('').blur();
                        $('#custom-menu-item-url').val('http://');
                    }
                );
            }

        } );

    } );


    ppb_1pager = {
        addItemToMenu : function (menuItem, processMethod, callback) {
            var menu = $('#menu').val(),
                nonce = $('#menu-settings-column-nonce').val(),
                params;

            processMethod = processMethod || function(){};
            callback = callback || function(){};

            params = {
                'action': 'sfxtp_add_menu_item',
                'menu': menu,
                'menu-settings-column-nonce': nonce,
                'menu-item': menuItem
            };

            $.post( ajaxurl, params, function(menuMarkup) {
                var ins = $('#menu-instructions');

                menuMarkup = $.trim( menuMarkup ); // Trim leading whitespaces
                processMethod(menuMarkup, params);

                // Make it stand out a bit more visually, by adding a fadeIn
                $( 'li.pending' ).hide().fadeIn('slow');
                $( '.drag-instructions' ).show();
                if( ! ins.hasClass( 'menu-instructions-inactive' ) && ins.siblings().length )
                    ins.addClass( 'menu-instructions-inactive' );

                callback();
            });
        },

        ppb_1pager_item_save : function ( data ) {
            var url = $('#sfxtp-phone-item-url').val(),
                label = $('#sfxtp-phone-item-title').val();

            // Show the ajax spinner
            $('.customlinkdiv .spinner').show();


            ppb_1pager.addItemToMenu( { '-1': data }, wpNavMenu.addMenuItemToBottom, function() {
                // Remove the ajax spinner
                $('.customlinkdiv .spinner').hide();
                // Set custom link form back to defaults
                $('#custom-menu-item-name').val('').blur();
                $('#custom-menu-item-url').val('http://');
            } );

        }

    }
} )( jQuery );