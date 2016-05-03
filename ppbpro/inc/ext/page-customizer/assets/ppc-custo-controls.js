/**
 * Created by shramee on 6/10/15.
 */
jQuery(function($, undef){
	var api = wp.customize;

	api.lib_alpha_color_control = api.Control.extend({
		ready: function() {
			var control = this,
				picker = control.container.find('.color-picker-hex');

			picker.val( control.setting() ).libColorPicker({
				change: function() {
					control.setting.set( picker.libColorPicker('color') );
				},
				clear: function() {
					control.setting.set( false );
				}
			});

			control.setting.bind( function( value ) {
				picker.val( value );
				picker.libColorPicker( 'color', value );
			});

			/**
			 * Adding following event whenever footer_menu_text_color is changed, due to its relationship with footer_menu_active_link_color.
			 */
			if ( 'et_divi[footer_menu_text_color]' === this.id ) {

				// Whenever user change footer_menu_text_color, do the following
				this.setting.bind( 'change', function( value ){

					// Set footer_menu_active_link_color equal to the newly changed footer_menu_text_color
					api( 'et_divi[footer_menu_active_link_color]' ).set( value );

					// Update default color of footer_menu_active_link_color equal to the newly changed footer_menu_text_color.
					// If afterward user change the color and not happy with it, they can click reset and back to footer_menu_text_color color
					api.control( 'et_divi[footer_menu_active_link_color]' ).container.find( '.color-picker-hex' )
						.data( 'data-default-color', value )
						.libColorPicker({ 'defaultColor' : value, 'color' : value });
				});
			}
		}
	});

	api.controlConstructor['lib_color'] = api.lib_alpha_color_control;
});