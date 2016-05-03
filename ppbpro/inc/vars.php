<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 18/12/15
 * Time: 4:06 PM
 */

global $ppbpro_addons_data, $ppbpro_tpl;
$ppbpro_addons_data = array(
	array(
		'img'  => 'http://www.pootlepress.com/wp-content/uploads/2015/08/icon-post-customizer-add-on-400x400.jpg',
		'path' => 'blog-customizer',
	),
	array(
		'img'  => 'http://www.pootlepress.com/wp-content/uploads/2015/10/page-customizer-icon-400x400.png',
		'path' => 'page-customizer',
	),
	array(
		'img'  => 'http://www.pootlepress.com/wp-content/uploads/2015/12/photography-add-on-icon-400x400.jpg',
		'path' => 'photography',
	),
	array(
		'img'  => 'http://www.pootlepress.com/wp-content/uploads/2015/10/one-pager-400x400.png',
		'path' => 'one-pager',
	),
	array(
		'img'  => 'http://www.pootlepress.com/wp-content/uploads/2015/08/ppb-woocommmerce-add-on-400x400.jpg',
		'path' => 'woocommerce',
	),
	//array(
	//    'img'   => 'http://www.pootlepress.com/wp-content/uploads/2016/01/post-builder-icon-400x400.png',
	//    'path'  => 'post-builder',
	//),
);

function ppbpro_get_template( $tpl = '' ) {
	$tpls = array(
		'first' => '{"widgets":[{"text":"&lt;p&gt;&lt;a href=\"http://wp/ppb/wp-content/uploads/2016/05/blog-970722_1280.jpg\"&gt;&lt;img class=\"aligncenter wp-image-188 size-full\" src=\"http://wp/ppb/wp-content/uploads/2016/05/blog-970722_1280.jpg\" alt=\"blog-970722_1280\" width=\"1280\" height=\"960\" /&gt;&lt;/a&gt;&lt;/p&gt;","info":{"grid":"0","cell":"0","id":"0","class":"Pootle_PB_Content_Block","style":"{\"background-image\":\"\",\"background-color\":\"\",\"background-transparency\":\"\",\"text-color\":\"\",\"border-width\":\"\",\"border-color\":\"\",\"padding\":\"\",\"rounded-corners\":\"\",\"inline-css\":\"\",\"class\":\"\"}"}},{"text":"&lt;p&gt;&nbsp;&lt;/p&gt;&lt;p&gt;&nbsp;&lt;/p&gt;&lt;p&gt;&nbsp;&lt;/p&gt;&lt;p&gt;&nbsp;&lt;/p&gt;","info":{"grid":"1","cell":"0","id":"1","class":"Pootle_PB_Content_Block","style":"{\"background-image\":\"http://wp/ppb/wp-content/uploads/2016/04/file000325161223.jpg\",\"background-color\":\"\",\"background-transparency\":\"0\",\"text-color\":\"\",\"border-width\":\"\",\"border-color\":\"\",\"padding\":\"\",\"rounded-corners\":\"\",\"inline-css\":\"\",\"class\":\"\"}"}},{"text":"&lt;h3 style=\"text-align: center;\"&gt;The Sunset&lt;/h3&gt;&lt;p&gt;Donec eget varius urna. Donec facilisis justo sed ipsum maximus, ac tempus ipsum maximus. Sed ipsum est, condimentum sit amet scelerisque pretium, accumsan at nisl.&lt;/p&gt;","info":{"grid":"1","cell":"0","id":"2","class":"Pootle_PB_Content_Block","style":"{\"background-image\":\"\",\"background-color\":\"\",\"background-transparency\":\"0\",\"text-color\":\"\",\"border-width\":\"\",\"border-color\":\"\",\"padding\":\"\",\"rounded-corners\":\"\",\"inline-css\":\"\",\"class\":\"\"}"}},{"text":"&lt;p&gt;&nbsp;&lt;/p&gt;&lt;p&gt;&nbsp;&lt;/p&gt;&lt;p&gt;&nbsp;&lt;/p&gt;&lt;p&gt;&nbsp;&lt;/p&gt;","info":{"grid":"1","cell":"1","id":"3","class":"Pootle_PB_Content_Block","style":"{\"background-image\":\"http://wp/ppb/wp-content/uploads/2016/04/file000477760838.jpg\",\"background-color\":\"\",\"background-transparency\":\"0\",\"text-color\":\"\",\"border-width\":\"\",\"border-color\":\"\",\"padding\":\"\",\"rounded-corners\":\"\",\"inline-css\":\"\",\"class\":\"\"}"}},{"text":"&lt;h3 style=\"text-align: center;\"&gt;The fields&lt;/h3&gt;&lt;p&gt;Donec eget varius urna. Donec facilisis justo sed ipsum maximus, ac tempus ipsum maximus. Sed ipsum est, condimentum sit amet scelerisque pretium, accumsan at nisl.&lt;/p&gt;","info":{"grid":"1","cell":"1","id":"4","class":"Pootle_PB_Content_Block","style":"{\"background-image\":\"\",\"background-color\":\"\",\"background-transparency\":\"0\",\"text-color\":\"\",\"border-width\":\"\",\"border-color\":\"\",\"padding\":\"\",\"rounded-corners\":\"\",\"inline-css\":\"\",\"class\":\"\"}"}},{"text":"&lt;p&gt;&nbsp;&lt;/p&gt;&lt;p&gt;&nbsp;&lt;/p&gt;&lt;p&gt;&nbsp;&lt;/p&gt;&lt;p&gt;&nbsp;&lt;/p&gt;","info":{"grid":"1","cell":"2","id":"5","class":"Pootle_PB_Content_Block","style":"{\"background-image\":\"http://wp/ppb/wp-content/uploads/2016/04/file000541344089.jpg\",\"background-color\":\"\",\"background-transparency\":\"0\",\"text-color\":\"\",\"border-width\":\"\",\"border-color\":\"\",\"padding\":\"\",\"rounded-corners\":\"\",\"inline-css\":\"\",\"class\":\"\"}"}},{"text":"&lt;h3 style=\"text-align: center;\"&gt;Clockwork&lt;/h3&gt;&lt;p&gt;Donec eget varius urna. Donec facilisis justo sed ipsum maximus, ac tempus ipsum maximus. Sed ipsum est, condimentum sit amet scelerisque pretium, accumsan at nisl.&lt;/p&gt;","info":{"grid":"1","cell":"2","id":"6","class":"Pootle_PB_Content_Block","style":"{\"background-image\":\"\",\"background-color\":\"\",\"background-transparency\":\"0\",\"text-color\":\"\",\"border-width\":\"\",\"border-color\":\"\",\"padding\":\"\",\"rounded-corners\":\"\",\"inline-css\":\"\",\"class\":\"\"}"}},{"text":""}],"grids":[{"cells":"1","style":{"full_width":"1","row_height":"0","hide_row":"","margin_top":"0","margin_bottom":"0","col_gutter":"0","background_toggle":"","bg_color_wrap":"","background":"","bg_image_wrap":"","background_image":"http://wp/ppb/wp-content/uploads/2016/05/blog-970722_1280.jpg","background_image_repeat":"","background_parallax":"","background_image_size":"cover","bg_overlay_color":"","bg_overlay_opacity":"0.5","bg_video_wrap":"","bg_video":"","bg_mobile_image":"","bg_wrap_close":"","style":"","class":"","col_class":""},"id":0},{"cells":"3","style":{"full_width":"","row_height":"0","hide_row":"","margin_top":"0","margin_bottom":"0","col_gutter":"2","background_toggle":"","bg_color_wrap":"","background":"","bg_image_wrap":"","background_image":"","background_image_repeat":"","background_parallax":"","background_image_size":"cover","bg_overlay_color":"","bg_overlay_opacity":"0.5","bg_video_wrap":"","bg_video":"","bg_mobile_image":"","bg_wrap_close":"","style":"","class":"","col_class":""},"id":1}],"grid_cells":[{"weight":1,"grid":"0","id":0},{"weight":0.3261,"grid":"1","id":1},{"weight":0.3456,"grid":"1","id":2},{"weight":0.3271,"grid":"1","id":3}]}'
	);
	if ( ! $tpl ) {
		return array_keys( $tpls );
	} elseif ( ! empty( $tpls[ $tpl ] ) ) {
		$tpl = json_decode( $tpls[ $tpl ], 'associative_array' );

		foreach ( $tpl['widgets'] as $i => $wid ) {
			if ( ! empty( $wid['info']['style'] ) ) {
				$tpl['widgets'][ $i ]['info']['style'] = stripslashes( $wid['info']['style'] );
				$tpl['widgets'][ $i ]['text'] = html_entity_decode( stripslashes( $wid['text'] ) );
			}
		}
		return $tpl;
	} else {
		return false;
	}
}