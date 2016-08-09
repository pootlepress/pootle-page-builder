/**
 * Buttons tmce plugin file
 * @package pootle buttons
 * @since 0.7
 * @developer http://wpdevelopment.me <shramee@wpdevelopment.me>
 */

(function($) {
	/* Register the buttons */
	tinymce.create('tinymce.plugins.MyButtons', {
		init : function(ed, url) {
			var ass_url = pbtn.ass_url = url + '/assets/';
			/**
			 * Adds HTML tag to selected content
			 */
			ed.addButton( 'pbtn_add_btn', {
				title : 'Insert Button',
				image : ass_url + 'icon.png',
				cmd: 'pbtn_add_btn_cmd'
			});
			ed.addCommand( 'pbtn_add_btn_cmd', function() {
				var selected_text = ed.selection.getContent();
				ed.windowManager.open( {
					title: '',
					url : pbtn.dialogUrl + '&assets_url=' + ass_url + '&text=' + selected_text,
					width : 700,
					height : 610
				}, { plugin_url : pbtn.ass_url, editor : ed } );
			});
			ed.on( "dblClick", function ( e ) {
				var btn = $( e.target );
				if ( btn.hasClass( "pbtn" ) ) {
					var href = btn.attr( 'href' ) ? btn.attr( 'href' ) : '',
						icon = btn.find('i').length ? encodeURIComponent( btn.find('i').prop( 'outerHTML' ) ) : '';
					ed.selection.select( btn[0] );
					console.log( 'Double clicked!' );
					ed.windowManager.open( {
						title: '',
						url : pbtn.dialogUrl + '&edit_button=1&assets_url=' + ass_url + '&text=' + btn.text() +
						      '&icon=' + icon + '&url=' + href,
						width : 700,
						height : 610
					}, { plugin_url : pbtn.ass_url, editor : ed, button : btn } );
				}
			} );
		},
		createControl : function(n, cm) {
			return null;
		}
	});
	/* Start the buttons */
	tinymce.PluginManager.add( 'pbtn_script', tinymce.plugins.MyButtons );

})(jQuery);