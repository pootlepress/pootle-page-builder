/**
 * Admin end scripts
 *
 * @package pootle_page_builder_blog_customizer
 * @version 1.0.0
 */
jQuery(function ($) {
    var leftRightDefaults = {
            'ppb-blog-customizer-image-posts-only' : '1',
            'ppb-blog-customizer-title-size' : '1',
            'ppb-blog-customizer-feat-img' : 'circle',
            'ppb-blog-customizer-show-excerpt' : '1',
            'ppb-blog-customizer-post-border-width': '1',
            'ppb-blog-customizer-post-border-color': '#e8e8e8',
            'ppb-blog-customizer-show-date' : '',
            'ppb-blog-customizer-show-author' : '',
            'ppb-blog-customizer-show-cats' : '',
            'ppb-blog-customizer-show-comments' : ''
        },
        topImageDefaults = {
            'ppb-blog-customizer-image-posts-only' : '1',
            'ppb-blog-customizer-title-size': '1',
            'ppb-blog-customizer-feat-img': 'image-56',
            'ppb-blog-customizer-show-excerpt': '1',
            'ppb-blog-customizer-post-border-width': '1',
            'ppb-blog-customizer-post-border-color': '#e8e8e8',
            'ppb-blog-customizer-show-date': '',
            'ppb-blog-customizer-show-author': '',
            'ppb-blog-customizer-show-cats': '',
            'ppb-blog-customizer-show-comments': ''
        },
        fullImageDefaults = {
            'ppb-blog-customizer-image-posts-only' : '1',
            'ppb-blog-customizer-title-size': '1',
            'ppb-blog-customizer-text-color': '#ffffff',
            'ppb-blog-customizer-text-position': '',
            'ppb-blog-customizer-feat-img': '',
            'ppb-blog-customizer-show-gutters': '',
            'ppb-blog-customizer-post-rounded-corners': '',
            'ppb-blog-customizer-show-date': '',
            'ppb-blog-customizer-show-author': '',
            'ppb-blog-customizer-show-cats': '',
            'ppb-blog-customizer-show-comments': ''
        };
    ppbPostLayoutsSettings = {
        'left-image' : leftRightDefaults,
        'right-image' : leftRightDefaults,
        'top-image' : topImageDefaults,
        'full-image' : fullImageDefaults
    };

    var $html = $('html');
    $html.on('pootlepb_admin_content_block_title', function (e, $t, data) {

        if (typeof data != 'undefined' && typeof data.info != 'undefined') {
            if ( data.info.style['ppb-blog-customizer-across'] && data.info.style['ppb-blog-customizer-down'] ) {
                $t.find('h4')
                    .html('Posts ')
            }
        }
    });

    //Switch to pofo tab
    $html.on('pootlepb_admin_editor_panel_done', function (e, $this, styles) {
        if (
            $this.find('.content-block-ppb-blog-customizer-across').val() &&
            $this.find('.content-block-ppb-blog-customizer-down').val()
        ) {
            $this.find('.ppb-tabs-anchors[href="#pootle-ppb-blog-customizer-tab"]').click();
        } else {
            $this.find('.field-ppb-blog-customizer-layout img[src*="layout-.png"]').click();
        }

        if ( 'undefined' == typeof styles['ppb-blog-customizer-layout'] || '' == styles['ppb-blog-customizer-layout'] ) {
            $('input[name="ppb-content-panel-radio-ppb-blog-customizer-layout"]').prop('checked', false).change();
            $this.find('.ppb-custo-layout-options').hide();
        }
    });

    $html.on('pootlepb_admin_input_field_event_handlers', function ( e, $this ) {
        var $layout = $this.find('.field-ppb-blog-customizer-layout input');
        $layout.change(function() {
            var $$ = $(this);
            if ( ! $$.prop('checked') ) {
                return;
            }
            $('.field[class*="field-ppb-blog-customizer"]').attr('data-available', 1).show();
            switch ( $$.val() ) {
                case 'top-image':
                    $this.find('.ppb-custo-layout-options').show();
                    $hide('text-color');
                    $hide('text-position');
                    $hide('show-gutters');
                    break;
                case 'left-image':
                case 'right-image':
                    $this.find('.ppb-custo-layout-options').show();
                    $hide('text-color');
                    $hide('text-position');
                    $hide('show-gutters');
                    break;
                case 'full-image':
                    $this.find('.ppb-custo-layout-options').show();
                    $hide('show-excerpt');
                    $hide('post-border');
            }
        });

        //Full Image
        $this.find('.post-custo-layout-thumb').click( function(){
            var $t = $(this),
                $field = $t.closest('.field'),
                key = $t.siblings('input').val(),
                oldLayout = $field.find("input[type='radio']:checked").val(),
                $layoutOptions = $('.ppb-custo-layout-options, .field-ppb-blog-customizer-image-posts-only');
            $t.siblings('input').prop('checked', true).change();
            ppbPostLayoutsSettings[oldLayout] = panels.getStylesFromFields($layoutOptions);
            panels.setStylesToFields( $layoutOptions, ppbPostLayoutsSettings[key] );
        });
    });

    $hide = function( key, checked ) {
        var $t = $('.field-ppb-blog-customizer-' + key);
        $t.removeAttr('data-available');
        $t.find('input[type="text"],input[type="hidden"]').val('');
        $t.find(':checkbox').prop('checked', false);
        $t.change().hide();
    }
});