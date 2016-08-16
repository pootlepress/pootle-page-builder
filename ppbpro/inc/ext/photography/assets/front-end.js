/**
 * Plugin front end scripts
 *
 * @package page_builder_photo_addon
 * @version 1.0.0
 */
jQuery( function ( $ ) {
	var correctDimensions = function () {
			if ( window.ppbTimeout ) {
				clearTimeout( window.ppbTimeout );
			}
			window.ppbTimeout = setTimeout( function () {
				$( '[data-full_width="1"]' ).each( function () {
					var $t = $( this );
					ppbPhotoFullWidth( $t );
				} );
			}, 250 );
		},
		ppbPhotoFullWidth = function ( $t ) {
			var $p = $t.parent(),
				leftSpace = $p.offset().left,
				rightSpace = $( window ).outerWidth() - leftSpace - $p.outerWidth();
			$t.css( {
				'margin-left': - leftSpace,
				'margin-right': - rightSpace
			} );

			if ( $t.data( 'flexslider' ) ) {
				$t.data( 'flexslider' ).resize();
			}

			var msnry = $t.find( '.masonry' );
			if ( msnry.length ) {
				msnry.masonry( 'reload' );
			}

			$t.parentsUntil( '#pootle-page-builder' ).css( 'overflow', 'visible' );
		},
		initGallery = function (){
			var $t = $( this );
			$t.imagesLoaded( function () {
				if ( $t.data( 'type' ) ) {
					$t.find( '.ppb-photo-gallery-items' ).masonry( {
						itemSelector: '.ppb-photo-gallery-item-wrap'
					} );
				}
			} );
		},
		initSlider = function () {
			var $t = $( this ),
				fs = {},
				def = {},
				animation = $t.data( 'animation' ) ? $t.data( 'animation' ) : 'fade';

			if ( 'ribbon' == animation ) {
				fs.animation = 'slide';
				fs.itemWidth = $t.outerWidth() * 0.79;
				fs.itemMargin = 10;
			}

			fs.animation = 'kb' == animation ? 'fade' : animation;
			fs.directionNav = $t.data( 'arrows' ) ? true : false;
			fs.pausePlay = fs.directionNav;
			fs.controlNav = $t.data( 'pagination' ) ? true : false;
			fs.slideshowSpeed = $t.data( 'autoplay' ) ? $t.data( 'autoplay' ) : 5000;
			fs.animationSpeed = $t.data( 'animation_speed' ) ? $t.data( 'animation_speed' ) : 500;
			fs.after = function ( s ) {
				var li = s.find( 'li' ),
					sn = s.currentSlide;
				li.removeClass( 'ppb-active-slide' );
				li.eq( s.currentSlide ).addClass( 'ppb-active-slide' );
			};
			fs.start = fs.after;
			$t.imagesLoaded( function () {
				$t.flexslider( fs );
				if ( $t.data( 'full_width' ) ) {
					ppbPhotoFullWidth( $t );
					setTimeout( function () {
						$t.css( 'opacity', 1 );
					}, 500 );
				}
			} );
		},
		initPhotoAddon = function ( $t ) {
			console.log( $t.find( '.ppb-photo-slider' ) );
			$t.find( '.ppb-photo-slider' ).each( initSlider );

			$t.find( '.ppb-photo-gallery' ).each( initGallery );

			$t.find( '.ppb-lightbox' ).each( function () { $( this ).lightGallery( {selector: 'this'} ); } );

			$t.find( '[data-type="photo-listing"] .control' ).click( function () {
				var $t = $( this );
				if ( ! $t.hasClass( 'active' ) ) {
					var $p = $t.closest( '[data-type="photo-listing"]' );
					$p.toggleClass( 'full-items' );

					$t.siblings( '.active' ).removeClass( 'active' );
					$t.addClass( 'active' );
				}
			} );
		};

	$( window ).resize( correctDimensions );
	correctDimensions();

	// Init addon on body
	initPhotoAddon( $( 'body' ) );
	$( 'html' ).on( 'pootlepb_le_content_updated', function ( e, $this ) {
		initPhotoAddon( $this );
	} );

} );