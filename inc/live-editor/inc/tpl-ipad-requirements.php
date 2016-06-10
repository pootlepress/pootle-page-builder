<style>
	.panel-grid:first-child{ margin-top:0 }
	.pootle-live-editor.ppb-live-add-object.add-row, .panel-grid:hover .ppb-edit-row:hover span.dashicons-before, .ppb-block:hover .pootle-live-editor:hover span.dashicons-before { display: none;  }
	.panel-grid:hover .ppb-edit-row:hover span.dashicons-editor-code, .ppb-block:hover .pootle-live-editor:hover span.dashicons-screenoptions { display: inline-block;  }
	.pootle-live-editor-active .ppb-tabs-nav {
		font-size: 16px;
	}

	.ppb-tabs-panel .field > * {
		font-size: 16px;
		font-weight: 300;
	}
	.ppb-tabs-panel .field {
		margin-top: 25px
	}
	.ppb-tabs-nav li {
		margin: 16px 0;
	}

	.pootle-live-editor-active .mce-toolbar .mce-btn-group .mce-btn, .pootle-live-editor-active .qt-dfw {
		margin: 3px
	}

	.pootle-live-editor-active .ppb-dialog .button,
	.pootle-live-editor-active .ppb-dialog button,
	.pootle-live-editor-active .ppb-dialog .ui-button.pootle-live-editor-active,
	.pootle-live-editor-active .wp-color-result,
	.pootle-live-editor-active .wp-color-result:after  {
		font-size: 14px;
		line-height: 34px;
		height: 34px;
		font-weight: 300;
	}
	.pootle-live-editor-active .ppb-dialog .ppb-dialog-buttonpane button.ui-button,
	.pootle-live-editor-active .ppb-dialog [type="text"],
	.pootle-live-editor-active .ppb-dialog [type="number"],
	.pootle-live-editor-active .ppb-dialog .button,
	.pootle-live-editor-active .ppb-dialog button,
	.pootle-live-editor-active .ppb-dialog .ui-button.pootle-live-editor-active,
	.pootle-live-editor-active .wp-color-result:after {
		font-size: 16px;
		padding: 9px 16px !important;
		line-height: 16px;
		height: auto;
		font-weight: 300;
	}

	.pootle-live-editor-active .ppb-dialog [type="number"] {
		width: 120px;
		margin-right: 7px;
	}
	.mce-toolbar div.mce-btn button,
	.mce-toolbar div.mce-btn button.mce-open {
		padding: 7px 9px !important;
	}
	.ppb-dialog-titlebar {
		font-weight: 300;
	}
	.pootle-live-editor-active .ppb-dialog [type="checkbox"] {
		height: 20px;
		width: 20px;
		margin:5px 0;
	}
	.ppb-dialog .ui-button.ppb-dialog-titlebar-close {
		padding: 2px !important;
	}
	.ui-resizable-handle.ui-resizable-w {
		border: none;
		padding: 7px;
		margin-left: -7px;
	}
	.ui-resizable-handle.ui-resizable-w:before {
		content: '';
		display: block;
		position: absolute;
		top: 0;
		bottom: 0;
		margin-left: -1px;
		border-right: 2px dotted #ef4832;
		padding: 0;
		cursor: ew-resize;
	}
	#pootlepb-set-title + .ppb-dialog-buttonpane .ui-button-text-icon-primary {
		background: #0085ba !important;
		border-color: #0073aa #006799 #006799 !important;
		-webkit-box-shadow: 0 1px 0 #006799 !important;
		box-shadow: 0 1px 0 #006799 !important;
		color: #fff !important;
		text-decoration: none;
		text-shadow: 0 -1px 1px #006799,1px 0 1px #006799,0 1px 1px #006799,-1px 0 1px #006799 !important;
		font-weight: 500;
	}
	#ppb-ipad-updated-notice, #ppb-ipad-color-picker {
		padding: 50px 50px;
		position: fixed;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		width: 340px;
		height: 340px;
		z-index: 9999;
		text-align: center;
		margin: auto;
		background: #fff;
		display: none;
	}
	#ppb-ipad-updated-notice {
		-webkit-box-sizing: border-box;
		box-sizing: border-box;
		background: #0c7;
		border-radius: 50%;
		-webkit-animation: fade-in-out 2.5s 1 both;
		animation: fade-in-out 2.5s 1 both;
	}

	#ppb-ipad-updated-notice > * {
		display: inline-block;
		width: auto;
		height: auto;
		vertical-align: middle;
		color: #fff;
		font-size: 25px;
		letter-spacing: 2px;
	}

	#ppb-ipad-updated-notice .dashicons:before {
		font-size: 160px;
		color: inherit;
		width: auto;
		height: auto;
		display: block;
		line-height: 1;
	}

	#ppb-ipad-color-picker .wp-picker-container {
		margin: 0 auto 25px;
		width: 403px;
		display: block;
		float: none;
	}

	#ppb-ipad-color-picker {
		width: 439px;
		padding: 16px;
		height: 550px;
	}

	span.wp-picker-input-wrap .button,
	a.wp-color-result.wp-picker-open {
		display:none !important;
	}

	#ppb-ipad-color-input,
	#ppb-ipad-color-done {
		width: 403px;
		font-size: 25px;
		margin: auto;
	}

	#ppb-ipad-color-done {
		background: #777;
	}

	@-webkit-keyframes fade-in-out {
		0%   { opacity: 0; -webkit-transform: translate3d(0,-25%,0) }
		34%  { opacity: 1; -webkit-transform: none                  }
		61%  { opacity: 1; -webkit-transform: none                  }
		100% { opacity: 0; -webkit-transform: translate3d(0,25%,0)  }
	}
	@keyframes fade-in-out {
		0%   { opacity: 0; transform: translate3d(0,-25%,0) }
		34%  { opacity: 1; transform: none                  }
		61%  { opacity: 1; transform: none                  }
		100% { opacity: 0; transform: translate3d(0,25%,0)  }
	}
</style>
<div id="ppb-ipad-updated-notice">
	<span class="dashicons dashicons-yes"></span>
	<h3>Changes Saved</h3>
</div>
<div id="ppb-ipad-color-picker">
	<input type="text" id="ppb-ipad-color-input">
	<input onclick="ppbIpad.format.SetColor()" type="button" value="Choose Color" id="ppb-ipad-color-done">
</div>
