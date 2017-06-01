<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 09/08/16
 * Time: 5:53 PM
 */

global $pootlepb_modules, $ppbpro_addons_data;

$pootlepb_modules = apply_filters( 'pootlepb_modules_page', $pootlepb_modules );

$active_addons = get_option( 'ppbpro_active_addons', array( 'blog-customizer', 'page-customizer', 'photography', ) );

?>
<div class="ppb-modules modules-free">
	<h2>Free Module Plugins</h2>
	<?php
	foreach ( $pootlepb_modules as $slug => $plugin ) {
		$card_classes = class_exists( $plugin['active_class'] ) ? 'ppb-addon-card active' : 'ppb-addon-card';
		?>
		<div id="<?php echo $slug ?>" class="ppb-addon-card-wrap">
			<div class="<?php echo $card_classes; ?>">
				<div class="ppb-addon-img">
					<a class="thickbox"
					   style="background-image: url(<?php echo $plugin['Image']; ?>);"></a>
				</div>

				<div class="ppb-addon-details">
					<div class="ppb-addon-name">
						<h3><?php echo $plugin['Name']; ?></h3>
					</div>
					<div class="desc ppb-addon-description">
						<p class="ppb-addon-description"><?php echo strip_tags( $plugin['Description'], '<a>' ); ?></p>
						<cite><?php echo "By <a href='$plugin[AuthorURI]'>$plugin[Author]</a>"; ?></cite>
					</div>
				</div>
				<div class="ppb-addon-footer">
					<div class="ppb-addon-controls ppb-addon-installed">
						<?php
						if ( strpos( $card_classes, 'active' ) ) {
							echo '<span class="dashicons dashicons-yes"></span>';
							_e( 'This module is active!', 'ppbpro' );
						} else if ( is_dir( WP_PLUGIN_DIR . '/' . $slug ) ) {
							$activate_url = admin_url( "plugins.php" );
							echo "<a href='$activate_url' target='_blank' class='button pootle right'>" . __( 'Activate' ) . "</a>";
						} else {
							echo "<a href='$plugin[InstallURI]' class='button pootle right'>" . __( 'Install' ) . "</a>";
						}
						?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	?>
	<div class="clear"></div>
</div>
<div class="ppb-modules modules-pro">
	<h2>Pro Module Addons</h2>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'ppbpro_active_addons' );
		settings_errors();

		foreach ( $ppbpro_addons_data as $addon ) {
			if ( empty( $addon['free'] ) ) {
				$slug   = $addon['path'];
				$plugin = $addon['plugin'];
				$active = isset( $active_addons[ $slug ] ) ? $active_addons[ $slug ] : '';
				?>
				<div class="ppb-addon-card-wrap">
					<div class="ppb-addon-card <?php if ( $active ) {
						echo 'active';
					} ?>">
						<div class="ppb-addon-img">
							<a class="thickbox"
							   style="background-image: url(<?php echo $addon['img']; ?>);"></a>
						</div>

						<div class="ppb-addon-details">
							<div class="ppb-addon-name">
								<h3><?php echo $plugin['Name']; ?></h3>
							</div>
							<div class="desc ppb-addon-description">
								<p class="ppb-addon-description"><?php echo strip_tags( $plugin['Description'], '<a>' ); ?></p>
								<cite>By <a href="//pootlepress.com">pootlepress</a></cite>
							</div>
						</div>
						<div class="ppb-addon-footer">
							<div class="ppb-addon-controls ppb-addon-installed">
								<input type="hidden" name="ppbpro_active_addons[<?php echo $slug; ?>]"
								       value="<?php echo $active; ?>">
								<a class="button pootle activate"><?php _e( 'Activate' ) ?></a>

								<p class="deactivate">
									<span class="dashicons dashicons-yes"></span>
									<?php
									if ( $active ) {
										_e( 'This addon is active!', 'ppbpro' );
									} else {
										_e( 'Hit "Save Changes" to activate!', 'ppbpro' );
									}
									?>
								</p>
								<a class="button deactivate"><?php _e( 'Deactivate' ) ?></a>
							</div>
						</div>
					</div>
				</div>
				<?php
			} else {
				$plugin = $addon;
				$card_classes = class_exists( $plugin['active_class'] ) ? 'ppb-addon-card active' : 'ppb-addon-card';
				?>
				<div id="<?php echo $slug ?>" class="ppb-addon-card-wrap">
					<div class="<?php echo $card_classes; ?>">
						<div class="ppb-addon-img">
							<a class="thickbox"
							   style="background-image: url(<?php echo $plugin['Image']; ?>);"></a>
						</div>

						<div class="ppb-addon-details">
							<div class="ppb-addon-name">
								<h3><?php echo $plugin['Name']; ?></h3>
							</div>
							<div class="desc ppb-addon-description">
								<p class="ppb-addon-description"><?php echo strip_tags( $plugin['Description'], '<a>' ); ?></p>
								<cite><?php echo "By <a href='$plugin[AuthorURI]'>$plugin[Author]</a>"; ?></cite>
							</div>
						</div>
						<div class="ppb-addon-footer">
							<div class="ppb-addon-controls ppb-addon-installed">
								<?php
								if ( strpos( $card_classes, 'active' ) ) {
									echo '<span class="dashicons dashicons-yes"></span>';
									_e( 'This addon is active!', 'ppbpro' );
								} else if ( is_dir( WP_PLUGIN_DIR . '/' . $slug ) ) {
									$activate_url = admin_url( "plugins.php" );
									echo "<a href='$activate_url' target='_blank' class='button pootle right'>" . __( 'Activate' ) . "</a>";
								} else {
									echo "<a href='$plugin[InstallURI]' class='button pootle right'>" . __( 'Install' ) . "</a>";
								}
								?>
								<div class="clear"></div>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
		}

		?>
		<div class="clear"></div>
		<?php
		submit_button();
		?>
	</form>
</div>
