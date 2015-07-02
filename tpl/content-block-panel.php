<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 26/6/15
 * Time: 11:56 PM
 * @since 0.1.0
 */
?>
	<div class="ppb-cool-panel-wrap">
		<ul class="ppb-acp-sidebar">

			<li>
				<a class="ppb-tabs-anchors ppb-block-anchor ppb-editor" <?php selected( true ) ?> href="#pootle-editor-tab">
					<?php echo apply_filters( 'pootlepb_content_block_editor_title', 'Editor', $request ); ?>
				</a>
			</li>

			<?php if ( class_exists( 'WooCommerce' ) ) { ?>
				<li><a class="ppb-tabs-anchors" href="#pootle-wc-tab">WooCommerce</a></li>
			<?php } ?>

			<li class="ppb-seperator"></li>

			<li><a class="ppb-tabs-anchors" href="#pootle-style-tab">Style</a></li>

			<li><a class="ppb-tabs-anchors" href="#pootle-advanced-tab">Advanced</a></li>
		</ul>

		<?php ?>
		<div id="pootle-editor-tab" class="pootle-content-module tab-contents content-block">

			<?php echo do_action( 'pootlepb_content_block_editor_form', $request ); ?>

		</div>

		<div id="pootle-style-tab" class="pootle-style-fields pootle-content-module tab-contents">
			<?php
			pootlepb_widget_styles_dialog_form();
			?>
		</div>

		<div id="pootle-advanced-tab" class="pootle-style-fields pootle-content-module tab-contents">
			<?php
			pootlepb_widget_styles_dialog_form( 'advanced' );
			?>
		</div>

		<?php if ( class_exists( 'WooCommerce' ) ) { ?>
			<div id="pootle-wc-tab" class="pootle-content-module tab-contents">
				<?php do_action( 'pootlepb_add_content_woocommerce_tab' ); ?>
			</div>
		<?php } ?>

	</div>
