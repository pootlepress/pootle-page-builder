<div id="ppb-tour-dialog" style="display: none;">
	<div class="tour-header">
		<h3><span class="dashicons dashicons-lightbulb"></span> <span class="tour-heading">Content Block</span></h3>
	</div>
	<div class="tour-content">
		This is the content block, click here to edit this and hover over it to show more options.
	</div>
	<div class="tour-footer">
		<a href="javascript:0" onclick="jQuery(this).parents('#ppb-tour-dialog').hide()">
			<span class="dashicons dashicons-dismiss"></span> <?php _e( 'I know...' ) ?>
		</a>
		<a href="javascript:0" class="tour-next-slide">
			<span class="dashicons dashicons-controls-play"></span> <?php _e( 'Tell me more!' ) ?>
		</a>
	</div>
</div>

<style>
	#ppb-tour-dialog {
		margin: 16px 0 0 -23px;
		position: absolute;
		width: 340px;
		-webkit-box-shadow: 0 1px 5px rgba(0,0,0,0.25);
		-moz-box-shadow: 0 1px 5px rgba(0,0,0,0.25);
		box-shadow: 0 1px 5px rgba(0,0,0,0.25);
		top: 520px;
		left: 160px;
		z-index: 999;
		background: #fff;
		font-size: 16px;
	}
	#ppb-tour-dialog * {
		vertical-align: middle;
		margin: 0;
		padding: 0;
		font-weight: normal;
		color: inherit;
	}
	#ppb-tour-dialog .dashicons {
		height: auto;
		width: auto;
		font-size: 1.24em;
	}
	#ppb-tour-dialog > [class^="tour-"] {
		padding: 7px 16px;
	}
	#ppb-tour-dialog .tour-header {
		background: #ef4832;
		color: #fff;
	}
	#ppb-tour-dialog .tour-header:before {
		content: '';
		display: block;
		border: 16px solid transparent;
		border-bottom-color: #ef4832;
		position: absolute;
		bottom: 100%;
		left: 7px;
	}
	#ppb-tour-dialog .tour-header h3 {
		font-size: 22px;
	}
	#ppb-tour-dialog .dashicons-lightbulb {
		margin-left: -5px;
	}
	#ppb-tour-dialog .tour-footer {
		color: #e04030;
	}
	#ppb-tour-dialog .tour-footer a {
		display:inline-block;
	}
	#ppb-tour-dialog .tour-footer a:last-of-type {
		float: right;
	}
	#ppb-tour-dialog .tour-footer .dashicons {
		margin: -2px -2px 0 -1px;
	}

	#ppb-tour-dialog .tour-footer .dashicons-controls-play {
		margin-right: -5px;
	}
</style>