<?php
/**
 * Add-ons page template
 * Shows add-ons cards from http://pootlepress.com/ feed
 * @author pootlepress
 * @since 0.1.0
 */
$tabs = array(
	'plugins' => 'Modules',
	'manager' => 'Manager',
);
$current = filter_input( INPUT_GET, 'tab' );
$current = array_key_exists( $current, $tabs ) ? $current : 'plugins';
?>
<div class="wrap">
	<h1>Pootle Page Builder Modules</h1>
	<h2 class="nav-tab-wrapper">
		<?php
		foreach ( $tabs as $tab => $name ) {
			$class = ( $tab == $current ) ? 'nav-tab nav-tab-active' : 'nav-tab';
			echo "<a class='$class' href='?page=page_builder_modules&tab=$tab'>$name</a>";
		}
		?>
	</h2>
	<div class="ppb-modules-wrap">
		<?php
		include_once "modules-$current.php";
		?>
	</div>
</div>