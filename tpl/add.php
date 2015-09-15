<?php
/**
 * New pootle page builder page template
 * User is redirected to new post page
 * @author pootlepress
 * @since 0.1.1
 */
?>
<div class="wrap">
	<h2 class="page_builder_add">
		If you are not automatically redirected.
		<a href="<?php echo esc_url( admin_url( '/post-new.php?post_type=page&page_builder=pootle' ) ); ?>">
			Click Here to Create New page with Pootle Page Builder.
		</a>
	<h2>
</div>
