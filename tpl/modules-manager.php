<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 09/08/16
 * Time: 5:54 PM
 */
?>
<form method="post" action="options.php">
	<?php
	$ppb_modules = apply_filters( 'pootlepb_modules', array() );

	$default_module_args = array(
		'label'      => 'Unlabeled Module',
		'icon_class' => 'dashicons dashicons-star-filled',
		'icon_html'  => '',
	);

	$enabled_modules = get_option( 'ppb_enabled_addons', array(
		'hero-section',
		'image',
		'pootle-slider',
		'photo-gallery',
		'unsplash',
		'pbtn',
		'blog-posts',
		'wc-products',
		'ninja_forms-form',
		'metaslider-slider',
	) );
	$enabled_modules = $enabled_modules ? $enabled_modules : array();
	$disabled_modules = get_option( 'ppb_disabled_addons', array() );
	$disabled_modules = $disabled_modules ? $disabled_modules : array();

	settings_fields( 'ppbpro_modules' );
	settings_errors(); ?>
	<div class="ppb-modules modules-disabled" style="float: right;">
		<h2 class="subhead-follows">Disabled Modules</h2>
		<h3>Drag modules here to remove them from live editor.</h3>
		<div id="pootlepb-modules-wrap">
			<div class="ppb-disabled-modules-list">
				<?php
				foreach ( $disabled_modules as $id ) {
					$md = wp_parse_args( $ppb_modules[ $id ], $default_module_args );
					unset( $ppb_modules[ $id ] );

					echo "<div id='ppb-mod-$id' class='ppb-module'>";
					echo "<i class='icon $md[icon_class]'>$md[icon_html]</i><div class='label'>$md[label]</div>";
					echo "<input type='hidden' name='ppb_disabled_addons[]' value='$id'>";
					echo "</div>";
				}
				?>
			</div>
		</div>
	</div>
	<div class="ppb-modules modules-enabled">
		<h2 class="subhead-follows">Enabled Modules</h2>
		<h3>These addons will be available in live editor.</h3>
		<div id="pootlepb-modules-wrap">
			<div class="ppb-enabled-modules-list">
				<?php
				foreach ( $enabled_modules as $id ) {
					if ( empty( $ppb_modules[ $id ] ) ) { continue; }
					$md = wp_parse_args( $ppb_modules[ $id ], $default_module_args );
					unset( $ppb_modules[ $id ] );

					echo "<div id='ppb-mod-$id' class='ppb-module'>";
					echo "<i class='icon $md[icon_class]'>$md[icon_html]</i><div class='label'>$md[label]</div>";
					echo "<input type='hidden' name='ppb_enabled_addons[]' value='$id'>";
					echo "</div>";
				}
				foreach ( $ppb_modules as $id => $md ) {
					$md = wp_parse_args( $md, $default_module_args );
					unset( $ppb_modules[ $id ] );

					echo "<div id='ppb-mod-$id' class='ppb-module'>";
					echo "<i class='icon $md[icon_class]'>$md[icon_html]</i><div class='label'>$md[label]</div>";
					echo "<input type='hidden' name='ppb_enabled_addons[]' value='$id'>";
					echo "</div>";
				}
				?>
			</div>
		</div>
	</div>
	<div class="clear"></div>
	<?php submit_button(); ?>
</form>
