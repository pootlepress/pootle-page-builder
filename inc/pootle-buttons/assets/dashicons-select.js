/**
 * Created by shramee on 26/2/16.
 */
( function ( $ ) {
	$.fn.dashicons = ['menu','admin-site','dashboard','admin-post','admin-media','admin-links','admin-page','admin-comments','admin-appearance','admin-plugins','admin-users','admin-tools','admin-settings','admin-network','admin-home','admin-generic','admin-collapse','filter','admin-customizer','admin-multisite','welcome-write-blog','welcome-add-page','welcome-view-site','welcome-widgets-menus','welcome-comments','welcome-learn-more','format-aside','format-image','format-gallery','format-video','format-status','format-quote','format-chat','format-audio','camera','images-alt','images-alt2','video-alt','video-alt2','video-alt3','media-archive','media-audio','media-code','media-default','media-document','media-interactive','media-spreadsheet','media-text','media-video','playlist-audio','playlist-video','controls-play','controls-pause','controls-forward','controls-skipforward','controls-back','controls-skipback','controls-repeat','controls-volumeon','controls-volumeoff','image-crop','image-rotate','image-rotate-left','image-rotate-right','image-flip-vertical','image-flip-horizontal','image-filter','undo','redo','editor-bold','editor-italic','editor-ul','editor-ol','editor-quote','editor-alignleft','editor-aligncenter','editor-alignright','editor-insertmore','editor-spellcheck','editor-expand','editor-contract','editor-kitchensink','editor-underline','editor-justify','editor-textcolor','editor-paste-word','editor-paste-text','editor-removeformatting','editor-video','editor-customchar','editor-outdent','editor-indent','editor-help','editor-strikethrough','editor-unlink','editor-rtl','editor-break','editor-code','editor-paragraph','editor-table','align-left','align-right','align-center','align-none','lock','unlock','calendar','calendar-alt','visibility','hidden','post-status','edit','trash','sticky','external','arrow-up','arrow-down','arrow-right','arrow-left','arrow-up-alt','arrow-down-alt','arrow-right-alt','arrow-left-alt','arrow-up-alt2','arrow-down-alt2','arrow-right-alt2','arrow-left-alt2','sort','leftright','randomize','list-view','exerpt-view','grid-view','share','share-alt','share-alt2','twitter','rss','email','email-alt','facebook','facebook-alt','googleplus','networking','hammer','art','migrate','performance','universal-access','universal-access-alt','tickets','nametag','clipboard','heart','megaphone','schedule','wordpress','wordpress-alt','pressthis','update','screenoptions','info','cart','feedback','cloud','translation','tag','category','archive','tagcloud','text','yes','no','no-alt','plus','plus-alt','minus','dismiss','marker','star-filled','star-half','star-empty','flag','warning','location','location-alt','vault','shield','shield-alt','sos','search','slides','analytics','chart-pie','chart-bar','chart-line','chart-area','groups','businessman','id','id-alt','products','awards','forms','testimonial','portfolio','book','book-alt','download','upload','backup','clock','lightbulb','microphone','desktop','tablet','smartphone','phone','index-card','carrot','building','store','album','palmtree','tickets-alt','money','smiley','thumbs-up','thumbs-down','layout'];
	$.fn.dashiconSelector = function () {
		var $i,
			$di = $( '<div/>' ).addClass( 'dashicons-all-icons' ).css( 'display', 'none' ),
			$diwrap = $( '<div/>' ).addClass( 'dashicons-all-icons-wrap' ).css( 'display', 'inline-block' ),
			$btn = $( '<button/>' ).addClass( 'button button-primary' ).html( 'Choose Icon' ),
			$remove = $( '<button/>' ).addClass( 'button' ).html( 'No Icon' ),
			val = this.val(),
			$input = this;

		$di.append( $remove );
		$di.append( $('<br>') );
		$.each( $.fn.dashicons, function( k, icon ) {
			var ico = 'dashicons dashicons-' + icon;
			$i = $( '<i/>' ).addClass( ico ).css( {
					'font-size' : '1em',
					'height' : '1em',
					'width' : '1em',
				}
			);
			$i.click( function() {
				var $t = $( this );
				$input.val( $t.prop('outerHTML') ).change();
				$btn.show();
				$di.hide();
			} );
			$di.append( $i );
		} );

		$remove.click( function () {
			var $t = $( this );
			$input.val( '' ).change();
			$btn.show();
			$di.hide();
		} );

		$diwrap.append( $btn ); // Adding Button
		$diwrap.append( $di ); // Adding dashicons list
		$input.after( $diwrap ); // Adding wrap besides the input
		$btn.click( function() {
			$btn.hide();
			$di.show();
		} );
		$input.change( function () {
			var val = $input.val();
			if ( val ) {
				$btn.html( val + ' Icon selected' );
			} else {
				$btn.html( 'Choose Icon' );
			}
		} );
		$input.change();
	}
} )( jQuery );