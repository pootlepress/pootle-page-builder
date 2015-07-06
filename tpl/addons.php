<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 24/6/15
 * Time: 11:16 PM
 * @since 0.1.0
 */
?>
<div class="wrap">
	<h2>Pootle Page Builder Add-ons</h2>
	<?php settings_errors(); ?>
<form action='options.php' method="POST">
	<h4>There are no add-ons yet.</h4>
	<?php
	settings_fields( 'pootlepage-add-ons' );
	submit_button();
	?>
</form>
</div>