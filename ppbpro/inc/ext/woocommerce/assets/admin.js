/**
 * Plugin admin end scripts
 *
 * @package pootle_page_builder_for_WooCommerce
 * @version 0.1.0
 */
jQuery( function ( $ ) {
	var $html = $( 'html' );
	$html.on( 'pootlepb_admin_content_block_title', function ( e, $t, data ) {

		if ( typeof data != 'undefined' && typeof data.info != 'undefined' ) {
			var style = data.info.style
			if ( data.info.style['wc_prods-add'] ) {
				switch ( style['wc_prods-add'] ) {
					case 'product_category':
						var cats = style['wc_prods-category'].join( ', ' );
						$t.find( 'h4' ).html( 'Products: ' + cats );
						break;
					case 'sale_products':
						$t.find( 'h4' ).html( 'Products: On sale' );
						break;
					case 'best_selling_products':
						$t.find( 'h4' ).html( 'Products: Best selling' );
						break;
					case 'featured_products':
						$t.find( 'h4' ).html( 'Products: Featured' );
						break;
					case 'product_categories':
						$t.find( 'h4' ).html( 'Product categories' );
						break;
					case 'top_rated_products':
						$t.find( 'h4' ).html( 'Products: Top rated' );
						break;
					case 'product_attribute':
						$t.find( 'h4' ).html( 'Products: ' + style['wc_prods-attribute'].replace( 'pa_', '' ) );
						break;
					case 'products':
						$t.find( 'h4' ).html( 'Products: By id' );
						break;
				}
			}
		}
	} );

	//Switch to pofo tab
	$html.on( 'pootlepb_admin_editor_panel_done', function ( e, $this ) {
		if ( $this.find( '.content-block-wc_prods-add' ).val() ) {
			$this.find( '.ppb-tabs-anchors[href="#pootle-wc_prods-tab"]' ).click();
		}
	} );

	$html.on( 'pootlepb_admin_input_field_event_handlers', function ( e, $this ) {

		var wcFields = $this.find( 'div[class*="wc_prods"]' );
		$this.find( '.field-wc_prods-add select' ).change( function () {
			var $t = $( this );
			wcFields.not( '.field-wc_prods-add' )
			        .show();
			var cats = [];

			switch ( $t.val() ) {
				case 'product_categories':
					ppbWooHideField( 'per_page' );
					ppbWooHideField( 'hide_add_to_cart' );
					ppbWooHideField( 'star_rating' );
					ppbWooHideField( 'hide_price' );
					ppbWooHideField( 'hide_title' );
				case 'featured_products':
				case 'best_selling_products':
					ppbWooHideField( 'order' );
					ppbWooHideField( 'orderby' );
				case 'products':
				case 'sale_products':
				case 'top_rated_products':
					ppbWooHideField( 'category' );
				case 'product_category':
					ppbWooHideField( 'attribute' );
					ppbWooHideField( 'filter' );
				case 'product_attribute':
					break;
				default:
					wcFields.not( '.field-wc_prods-add' ).hide();
					break;
			}
			if ( 'products' != $t.val() ) {
				ppbWooHideField( 'ids' );
			} else {
				ppbWooHideField( 'per_page' );
			}

			if ( 'product_attribute' == $t.val() ) {
				$( '.field-wc_prods-filter' ).show();
				ppbWooHideField( 'category' );
			}
			if ( 'product_categories' == $t.val() ) {
				ppbWooHideField( 'per_page' );
			} else {
				ppbWooHideField( 'catids' );
			}
		} );

		$( '.field-wc_prods-attribute select' ).change( function () {
			var $t = $( this ),
				$$ = $( '.field-wc_prods-filter select' );
			$( '.field-wc_prods-filter' ).hide();
			$$.html( '' );

			if ( 'object' == typeof ppb_wc_filters[$t.val()] ) {
				$.each( ppb_wc_filters[$t.val()], function ( key, value ) {
					$( '.field-wc_prods-filter' ).show();
					$$.append( "<option value='" + key + "'>" + value + "</option>" );
					$$.val( null ).trigger( "chosen:updated" );
				} );
			}
		} )
	} );

	ppbWooHideField = function ( key ) {
		var $p = $( '.field-wc_prods-' + key ).hide();

		$p.find( 'input, select' ).not('[type="checkbox"]').val( '' );
		$p.find( 'input[type="checkbox"]' ).prop( 'checked', false );
	}
} );
