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
		<?php _e( 'If you are not automatically redirected.', 'pootle-page-builder' ); ?>
		<a href="<?php echo esc_url( admin_url( '/post-new.php?post_type=page&page_builder=pootle' ) ); ?>">
			<?php _e( 'Click Here to Create New page with Pootle Page Builder.', 'pootle-page-builder' ); ?>
		</a>
	<h2>
</div>
