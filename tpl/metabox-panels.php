<?php
/**
 * Main pootle page builder user interface template
 * @author pootlepress
 * @since 0.1.0
 */

$layouts = apply_filters( 'pootlepb_prebuilt_layouts', array() );

$buttons = array( 'grid-add' => 'Add Row' );
if ( ! empty( $layouts ) ) {
	$buttons['prebuilt-set'] = 'Use Existing Page Layout';
}

$buttons = apply_filters( 'pootlepb_add_to_panel_buttons', $buttons );
?>

<div id="panels" data-animations="true">

	<?php do_action( 'pootlepb_before_interface' ) ?>

	<div id="panels-container">
	</div>

	<div id="add-to-pb-panel">

	<?php
		foreach ( $buttons as $id => $name ) { ?>
			<button class="<?php echo $id ?> add-button ed_button button button-small">
				<?php _e( $name, 'ppb-panels' ) ?>
			</button>
		<?php }
	?>

		<div class="clear"></div>
	</div>

	<?php // The add row dialog ?>
	<div id="grid-add-dialog" data-title="<?php esc_attr_e( 'Add Row', 'ppb-panels' ) ?>"
	     class="panels-admin-dialog" style="text-align: center">
		<p>
			<label>
				<strong>
					<?php _e( 'How many columns do you want your row to have? ', 'ppb-panels' ) ?><br>
					(<?php _e( 'You can adjust the widths later', 'ppb-panels' ) ?>)
				</strong>
			</label>
		</p>
		<p><input type="number" id="grid-add-dialog-input" name="column_count" class="small-text" value="3"/></p>
	</div>

		<?php

		//Get Current User
		$current_user = wp_get_current_user();

		//Get first name if set
		$username = '';
		if ( ! empty( $current_user->user_firstname ) ) {
			$username = " {$current_user->user_firstname}";
		}

		//Get user's visit count
		$visit_count = get_user_meta( $current_user->ID, 'pootlepb_visit_count', true );

		//Set welcome message
		if ( empty( $visit_count ) || empty( $layouts ) ) {
			$visit_count = 0;
			$message = "Welcome to Page Builder{$username}! Click the 'Add Row' button above to start building your page.";
		} elseif ( 1 == $visit_count ) {
			$message = "Welcome back to Page Builder{$username}! You can now also use existing pages as a template to start your page and save you time!";
		} else {
			$message = "Welcome to Page Builder{$username}! You know what to do.";
		}

		//Print the message
		echo apply_filters( 'pootlepb_welcome_message',
			"<div id='ppb-hello-user' class='visit-count-" . esc_attr( $visit_count ) . "'> " .
			esc_html( $message ) .
			"</div>" ,
			$current_user, $visit_count );

		//Update user visit count
		$visit_count++;
		global $pagenow;
		if ( 'post-new.php' == $pagenow ){
			update_user_meta( $current_user->ID, 'pootlepb_visit_count', $visit_count );
		}

		?>

	<div id="remove-row-dialog" data-title="<?php esc_attr_e( "Remove Row", 'ppb-panels' ) ?>"
	     class="panels-admin-dialog">
		<p>Are you sure?</p>
	</div>

	<div id="remove-widget-dialog" data-title="<?php esc_attr_e( "Delete Content", 'ppb-panels' ) ?>"
	     class="panels-admin-dialog">
		<p>Are you sure?</p>
	</div>

	<?php // The layouts dialog ?>

	<?php if ( ! empty( $layouts ) ) : ?>
		<div id="grid-prebuilt-dialog"
		     data-title="<?php esc_attr_e( 'Use Existing Page Layout', 'ppb-panels' ) ?>"
		     class="panels-admin-dialog">
			<p><label><strong><?php _e( 'Page Layout', 'ppb-panels' ) ?></strong></label></p>

			<p>
				<select type="text" id="grid-prebuilt-input" name="prebuilt_layout" style="width:580px;"
				        placeholder="<?php esc_attr_e( 'Select Layout', 'ppb-panels' ) ?>">
					<option class="empty" <?php selected( true ) ?> value=""></option>
					<?php foreach ( $layouts as $id => $data ) : ?>
						<option id="panel-prebuilt-<?php echo esc_attr( $id ) ?>"
						        data-layout-id="<?php echo esc_attr( $id ) ?>" class="prebuilt-layout">
							<?php echo isset( $data['name'] ) ? $data['name'] : __( 'Untitled Layout', 'ppb-panels' ) ?>
						</option>
					<?php endforeach; ?>
				</select>
			</p>
		</div>
	<?php endif; ?>

	<?php // The styles dialog ?>
	<div id="grid-styles-dialog" data-title="<?php esc_attr_e( 'Row Visual Style', 'ppb-panels' ) ?>"
	     class="panels-admin-dialog">
			<?php require POOTLEPB_DIR . 'tpl/row-settings-panel.php'; ?>
	</div>

	<div id="content-loss-dialog" data-title="<?php esc_attr_e( 'Changing to Page Builder', 'ppb-panels' ) ?>"
	     data-button-i-know="<?php esc_attr_e( "I know what I'm doing", 'ppb-panels' ) ?>"
	     data-button-stop="<?php esc_attr_e( "Yep, I'll stop and create a new page", 'ppb-panels' ) ?>"
	     class="panels-admin-dialog">
		<p>
			<?php _e( 'Slow down tiger! Do you realise that changing to Page Builder for this page will make all your page content disappear forever?', 'ppb-panels' ) ?>
			<br><br>
			<?php _e( 'Why not create a new page instead?', 'ppb-panels' ) ?>
		</p>
	</div>

	<div id="layout-loss-dialog"
	     data-title="<?php esc_attr_e( 'Changing to the default editor', 'ppb-panels' ) ?>"
	     data-button-i-know="<?php esc_attr_e( "I know what I'm doing", 'ppb-panels' ) ?>"
	     data-button-stop="<?php esc_attr_e( "I love Page Builder, keep me here", 'ppb-panels' ) ?>"
	     class="panels-admin-dialog">
		<p>
			<?php _e( "Ummm... if you go back to the default editor you'll loose all your content. Are you sure you want to loose all that hard work you've done?", 'ppb-panels' ) ?>
		</p>
	</div>
	<div id="no-empty-col-dialog"
	     data-title="<?php esc_attr_e( 'No empty column found', 'ppb-panels' ) ?>"
	     class="panels-admin-dialog">
		<p>
			<?php _e( "You can only remove an empty column, please move or delete content from the column you wish to remove.", 'ppb-panels' ) ?>
		</p>
	</div>

	<?php
	global $post;
	$panels_data = get_post_meta( $post->ID, 'panels_data', true );
	?>

	<?php wp_nonce_field( 'pootlepb_save', 'pootlepb_nonce' ) ?>
	<?php do_action( 'pootlepb_metabox_end' ); ?>
</div>

<?php
if ( 'pootle' == filter_input( INPUT_GET, 'page_builder' ) || ! empty( $panels_data['grids'] ) ) {
?>
	<style>.wrap{opacity:0;}</style>
	<script>
		jQuery(document).ready(function($){
			$('#content-panels').click();
			<?php
			if ( empty( $panels_data['grids'] ) ) {
				echo "$('.wrap').css('opacity', '1');";
			} else {
				?>
				pootlePBShowWrap = function () {
					$('.wrap').css('opacity', '1');
				};
				<?php
			}
			?>
		});
	</script>
<?php
}
?>