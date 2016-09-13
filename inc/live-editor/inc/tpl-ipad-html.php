<style>
	.wp-picker-container .wp-picker-open + .wp-picker-input-wrap {
		display: none;
	}

	.panel-grid:first-child{ margin-top:0 }
	.pootle-live-editor.ppb-live-add-object.add-row {
		position: fixed;
		top:0;
		left: 50vw;
		width:1px;
		height:1px;
		overflow: hidden;
	}

	.panel-grid.tour-active .ppb-edit-row.tour-active span.dashicons-before:not(.dashicons-no):not(.dashicons-editor-code),
	.panel-grid:hover .ppb-edit-row:hover span.dashicons-before:not(.dashicons-no):not(.dashicons-editor-code),
	.ppb-block:hover .pootle-live-editor:hover span.dashicons-before,
	.panel-grid.tour-active .ppb-block .pootle-live-editor.tour-active span.dashicons-before:not(.dashicons-move) {
		display: none;
	}

	.panel-grid:hover .ppb-edit-row:hover span.dashicons-editor-code,
	.ppb-block:hover .pootle-live-editor:hover span.dashicons-move {
		display: inline-block;
	}
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
	.ppb-full-blue-notice, #ppb-ipad-color-picker {
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
	.ppb-full-blue-notice {
		-webkit-box-sizing: border-box;
		box-sizing: border-box;
		background		: #009bf4;
		height			: auto;
		width			: auto;
		padding			: 43% 0 0;
	}
	.fade-in-out {
		-webkit-animation: fade-in-out 2.5s 1 both;
		animation: fade-in-out 2.5s 1 both;
	}
	.ppb-full-blue-notice > .dashicons {
		display: inline-block;
	}
	.ppb-full-blue-notice > *,
	.ppb-full-blue-notice > .dashicons {
		width: auto;
		height: auto;
		vertical-align: middle;
		color: #fff;
		font-size: 25px;
		letter-spacing: 2px;
	}
	span.ppb-rotate.dashicons.dashicons-admin-generic:before {
		padding: 0 0 0.0216em 0.052em;
	}
	.ppb-full-blue-notice .dashicons:before {
		font-size: 160px;
		color: inherit;
		width: auto;
		height: auto;
		display: block;
		line-height: 1;
	}
	#ppb-ipad-color-picker {
		position: absolute;
		top: 0;
		bottom: auto;
		width: 430px;
		padding: 7px;
		height: 340px;
		-moz-box-sizing: content-box;
		-webkit-box-sizing: content-box;
		box-sizing: content-box;
	}
	.ppb-ipad-color-picker span {
		display: block;
		float:left;
		width: 11%;
		box-shadow: 1px 1px 3px rgba(0,0,0,0.25);
		padding-top: 43px;
		margin: 1.636%;
	}
	span.wp-picker-input-wrap .button,
	a.wp-color-result.wp-picker-open {
		display:none !important;
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
<div id="ppb-ipad-updated-notice" class="ppb-full-blue-notice fade-in-out">
	<span class="dashicons dashicons-yes"></span>
	<h3>Changes Saved</h3>
</div>
<div id="ppb-ipad-notice" class="ppb-full-blue-notice">
	<span class="ppb-rotate dashicons dashicons-admin-generic"></span>
	<h2>Saving changes and loading preview...</h2>
</div>
<div id="ppb-ipad-color-picker">
	<!--<h3>Choose Color</h3>-->
	<div class="ppb-ipad-color-picker">
		<span style="background: #000000" data-color="#000000"></span>
		<span style="background: #993300" data-color="#993300"></span>
		<span style="background: #333300" data-color="#333300"></span>
		<span style="background: #003300" data-color="#003300"></span>
		<span style="background: #003366" data-color="#003366"></span>
		<span style="background: #000080" data-color="#000080"></span>
		<span style="background: #333399" data-color="#333399"></span>
		<span style="background: #333333" data-color="#333333"></span>
		<span style="background: #800000" data-color="#800000"></span>
		<span style="background: #FF6600" data-color="#FF6600"></span>
		<span style="background: #808000" data-color="#808000"></span>
		<span style="background: #008000" data-color="#008000"></span>
		<span style="background: #008080" data-color="#008080"></span>
		<span style="background: #0000FF" data-color="#0000FF"></span>
		<span style="background: #666699" data-color="#666699"></span>
		<span style="background: #808080" data-color="#808080"></span>
		<span style="background: #FF0000" data-color="#FF0000"></span>
		<span style="background: #FF9900" data-color="#FF9900"></span>
		<span style="background: #99CC00" data-color="#99CC00"></span>
		<span style="background: #339966" data-color="#339966"></span>
		<span style="background: #33CCCC" data-color="#33CCCC"></span>
		<span style="background: #3366FF" data-color="#3366FF"></span>
		<span style="background: #800080" data-color="#800080"></span>
		<span style="background: #999999" data-color="#999999"></span>
		<span style="background: #FF00FF" data-color="#FF00FF"></span>
		<span style="background: #FFCC00" data-color="#FFCC00"></span>
		<span style="background: #FFFF00" data-color="#FFFF00"></span>
		<span style="background: #00FF00" data-color="#00FF00"></span>
		<span style="background: #00FFFF" data-color="#00FFFF"></span>
		<span style="background: #00CCFF" data-color="#00CCFF"></span>
		<span style="background: #993366" data-color="#993366"></span>
		<span style="background: #FFFFFF" data-color="#FFFFFF"></span>
		<span style="background: #FF99CC" data-color="#FF99CC"></span>
		<span style="background: #FFCC99" data-color="#FFCC99"></span>
		<span style="background: #FFFF99" data-color="#FFFF99"></span>
		<span style="background: #CCFFCC" data-color="#CCFFCC"></span>
		<span style="background: #CCFFFF" data-color="#CCFFFF"></span>
		<span style="background: #99CCFF" data-color="#99CCFF"></span>
		<span style="background: #CC99FF" data-color="#CC99FF"></span>
	</div>
</div>
