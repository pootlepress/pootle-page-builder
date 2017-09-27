###*
# Plugin front end scripts
#
# @package Pootle_Page_Builder_Live_Editor
# @version 1.0.0
# @developer shramee <shramee@wpdevelopment.me>
###

###*
# Moves the elements in array
# @param oldI
# @param newI
# @returns Array
###

Array::ppbPrevuMove = (oldI, newI) ->
	@splice newI, 0, @splice(oldI, 1)[0]
	this

ppbPrevuDebug = 1
ppbIpad = {}

# Comment the code below to log console
logPPBData = (a, b, c) ->
	if 'undefined' == typeof ppbPrevuDebug or !ppbPrevuDebug
		return
	log =
		message: a
		content: []
		cells: []
		rows: []
	$ = jQuery
	$.each ppbData.widgets, (i, v) ->
		if !v or !v.info
			log.content.push 'Content ' + i + ' undefined info'
		else
			log.content.push 'Content ' + i + ' in Grid: ' + v.info.grid + ' Cell: ' + v.info.cell + ' Text: \'' + $(v.text).text().substring(0, 16) + '\''
		return
	$.each ppbData.grid_cells, (i, v) ->
		log.cells.push 'Cell ' + i + ' in Grid: ' + v.grid + ' with Weight: ' + v.weight
		return
	$.each ppbData.grids, (i, v) ->
		if !v.style
			log.rows.push 'Row ' + i + ' original id' + v.id + ' Contains: ' + v.cells + ' cells' + ' with Styles undefined'
		else
			log.rows.push 'Row ' + i + ' original id' + v.id + ' Contains: ' + v.cells + ' cells' + ' with BG: ' + v.style.background + ' ' + v.style.background_image
		return
	if log.hasOwnProperty(c)
		console.log log[c]
	else
		console.log log
	if b
		console.log b
	return

jQuery ($) ->
	$.each ppbData.grids, (i, v) ->
		ppbData.grids[i].id = i
		return

	$.each ppbData.grid_cells, (i, v) ->
		ppbData.grid_cells[i].id = i
		return

	$.fn.prevuBlockInit = ->
		$(this).each ->
			$t = $(this)
			$t.draggable prevu.contentDraggable
			$t.resizable prevu.contentResizable
			$t.droppable prevu.moduleDroppable
			$t.removeClass 'ppb-content-v-center ppb-content-h-center' # No centering classes

			return
		return

	$.fn.prevuRowInit = ->
		$t = $(this)
		$t.find('.ppb-block').prevuBlockInit()
		$t.find('.panel-grid-cell-container > .panel-grid-cell').resizable prevu.resizableCells
		tinymce.init prevu.tmce
		$ppb.sortable 'refresh'
		return

	$contentPanel = $('#pootlepb-content-editor-panel')
	$rowPanel = $('#pootlepb-row-editor-panel')
	$panels = $rowPanel.add( $contentPanel )
	$deleteDialog = $('#pootlepb-confirm-delete')
	$deletingWhat = $('#pootlepb-deleting-item')
	$addRowDialog = $('#pootlepb-add-row')
	$setTitleDialog = $('#pootlepb-set-title')
	$designTemplateDialog = $('#pootlepb-design-templates')
	$designTemplatePreview = $('#pootlepb-design-templates-preview-wrap')
	$postSettingsDialog = $('#pootlepb-post-settings')
	$ppbIpadColorDialog = $('#ppb-ipad-color-picker')
	$iconPicker = $('#ppb-iconpicker')
	$ppb = $('#pootle-page-builder')
	$mods = $('#pootlepb-modules-wrap')
	$body = $('body')
	$loader = $('#ppb-loading')
	dialogAttr =
		dialogClass: 'ppb-cool-panel'
		autoOpen: false
		draggable: false
		resizable: false
		title: 'Edit content block'
		height: $(window).height() - 50
		width: $(window).width() - 50
		buttons: Done: -> return
	prevu =
		noRedirect: false
		debug: true
		unSavedChanges: false
		justClickedEditRow: false
		justClickedEditBlock: false
		syncAjax: ->
			jQuery.post ppbAjax.url, ppbAjax, (response) ->
				if !response.replace(RegExp(' ', 'g'), '')
					console.log 'Error: No response from server at ' + ppbAjax.url
					return
				$response = $($.parseHTML(response, document, true))
				if 'function' == typeof prevu.ajaxCallback
					callback = prevu.ajaxCallback
					delete prevu.ajaxCallback
					callback $response, ppbAjax, response
					ppbCorrectOnResize()
				$('style#pootle-live-editor-styles').html $response.find('style#pootle-live-editor-styles').html()
				if ppbAjax.publish
					prevu.unSavedChanges = false
					if !prevu.noRedirect
						if window.ppbAjaxDebug
							$body.append '<div class="ajax-debug">' + response + '</div>'
						else
							window.location = response
				ppbAjax.publish = 0
				$loader.fadeOut 250
				return
		sync: (callback, publish) ->
			logPPBData 'Before sync'
			prevu.ajaxCallback = callback
			prevu.unSavedChanges = true
			prevu.saveTmceBlock $('.mce-edit-focus')
			delete ppbAjax.data
			ppbAjax.data = ppbData

			$loader.fadeIn 250

			if publish
				ppbAjax.publish = publish
				$body.trigger 'savingPPB'
				if ppbAjax.title
					butt = [ {
						text: publish
						icons: primary: if publish == 'Publish' then 'ipad-publish' else ''
						click: ->
							$setTitleDialog.ppbDialog 'close'
							prevu.syncAjax()
							return

					} ]

					butt[publish] = ->

					$setTitleDialog.parent().attr 'data-action', publish
					$setTitleDialog.ppbDialog 'open'
					$setTitleDialog.ppbDialog 'option', 'buttons', butt
					return
			else
				delete ppbAjax.publish
			logPPBData 'After sync'
			prevu.syncAjax()
			return
		reset: (nosort) ->
			allIDs = {}
			remove = []
			if !nosort
				prevu.resort()
			$.each ppbData.widgets, (i, v) ->
				if v and v.info
					$t = $('.ppb-edit-block[data-i_bkp="' + v.info.id + '"]')
					$p = $t.closest('.ppb-block')
					id = 'panel-' + ppbAjax.post + '-' + v.info.grid + '-' + v.info.cell + '-'
					loopI = 0
					while loopI < 25
						if !allIDs.hasOwnProperty(id + loopI)
							allIDs[id + loopI] = 1
							id = id + loopI
							break
						loopI++
					$t.data('index', i).attr 'data-index', i
					$p.attr 'id', id
					ppbData.widgets[i].info.id = i
				else
					remove.push i
				return
			$.each remove, (i, v) ->
				delete ppbData.widgets[v]
				return
			$.each ppbData.grids, (i, v) ->
				$t = $('.ppb-edit-row[data-i_bkp="' + v.id + '"]')
				$p = $t.closest('.ppb-row')
				$rStyle = $p.children('.panel-row-style').children('style')
				oldIdRegex = new RegExp($p.attr('id'), 'g')
				id = 'pg-' + ppbAjax.post + '-' + i
				$t.data('index', i).attr 'data-index', i
				$rStyle.html $rStyle.html().replace(oldIdRegex, id)
				$p.attr 'id', id
				allIDs[id] = 1
				ppbData.grids[i].id = i
				return
			$.each ppbData.grid_cells, (i, v) ->
				gi = undefined
				if v.hasOwnProperty('old_grid')
					gi = v.old_grid
					delete v.old_grid
				else
					gi = v.grid
				id = 'pgc-' + ppbAjax.post + '-'
				old_id = id + gi + '-'
				$p = undefined
				id += v.grid + '-'
				loopI = 0
				while loopI < 25
					if !allIDs.hasOwnProperty(id + loopI)
						id += loopI
						allIDs[id] = 1
						break
					loopI++
				old_id += loopI
				$p = $('#' + old_id)
				$p.data 'newID', id
				ppbData.grid_cells[i].id = i
				return
			$('.ppb-live-edit-object').each ->
				$t = $(this)
				i = $t.data('index')
				$t.data('i_bkp', i).attr 'data-i_bkp', i
				return
			$('.ppb-col').each ->
				$t = $(this)
				id = $t.data('newID')
				$(this).attr 'id', id
				$t.removeData 'newID'
				return
			return
		resort: ->
			ppbData.widgets.sort (a, b) ->
				if !a.info
					return 1
				if !b.info
					return -1
				ag = parseInt(a.info.grid)
				ac = parseInt(a.info.cell)
				ai = parseInt(a.info.id)
				bg = parseInt(b.info.grid)
				bc = parseInt(b.info.cell)
				bi = parseInt(b.info.id)
				ag * 10000 + ac * 1000 + ai - (bg * 10000 + bc * 1000 + bi)
			ppbData.grid_cells.sort (a, b) ->
				ag = parseInt(a.grid)
				ai = parseInt(a.id)
				bg = parseInt(b.grid)
				bi = parseInt(b.id)
				ag * 100 + ai - (bg * 100 + bi)
			prevu.unSavedChanges = true
			return
		rowBgToggle: ->
			$t = $rowPanel.find('[data-style-field=background_toggle]')
			$('.bg_section').hide()
			$($t.val()).show()
			return
		editPanel: ->
			if 'undefined' == typeof ppbData.widgets[window.ppbPanelI]
				return
			# Add event handlers
			panels.addInputFieldEventHandlers $contentPanel
			dt = ppbData.widgets[window.ppbPanelI]
			st = JSON.parse( dt.info.style )
			panels.setStylesToFields $contentPanel, st
			tinyMCE.get('ppbeditor').setContent dt.text
			$('html').trigger 'pootlepb_admin_editor_panel_done', [
				$contentPanel
				st
			]
			return
		savePanel: ->
			ppbData.widgets[window.ppbPanelI].text = tinyMCE.get('ppbeditor').getContent()
			st = JSON.parse( ppbData.widgets[window.ppbPanelI].info.style )
			st = panels.getStylesFromFields($contentPanel, st)
			ppbData.widgets[window.ppbPanelI].info.style = JSON.stringify(st)
			$t = $('.ppb-block.active')
			prevu.sync ($r, qry) ->
				id = $t.attr('id')
				$blk = $r.find('#' + id)
				style = $blk.closest('.panel-grid-cell').children('style').html()
				$cell = $t.closest('.panel-grid-cell')
				$blk.addClass 'pootle-live-editor-new-content-block'
				$t.replaceWith $blk
				$blk = $('.pootle-live-editor-new-content-block')
				$('html').trigger 'pootlepb_le_content_updated', [ $blk ]
				$blk.removeClass('pootle-live-editor-new-content-block').addClass('active').prevuBlockInit()
				if $cell.children('style').length
					$cell.children('style').html style
				else if style
					$style = $('<style>').html style
					$cell.prepend $style
				tinymce.init prevu.tmce
				return
#			$contentPanel.ppbDialog 'close'
			return
		editRow: ->
			$bgToggle = $rowPanel.find('[data-style-field=background_toggle]')
			prevu.rowBgToggle()
			$bgToggle.on 'change', prevu.rowBgToggle
			if 'undefined' == typeof ppbData.grids[window.ppbRowI]
				return
			dt = ppbData.grids[window.ppbRowI]
			st = dt.style
			$rowPanel.find('[data-style-field]').each ->
				$t = $(this)
				key = $t.attr('data-style-field')

				if 'undefined' == typeof st[key]
					st[key] = ''

				if $t.attr('type') == 'checkbox'
					$t.prop 'checked', false
					if st[key]
						$t.prop 'checked', true
				else if $t.attr('data-style-field-type') == 'slider'
					$t.siblings('.ppb-slider').slider 'value', st[key]
				else if $t.attr('data-style-field-type') == 'color'
					$t.wpColorPicker 'color', st[key]
				else
					$t.val st[key]
				$t.change()
				return
			return
		saveRow: ->
			dt = ppbData.grids[window.ppbRowI]
			st = ppbData.grids[window.ppbRowI].style
			$rowPanel.find('[data-style-field]').each ->
				$t = $(this)
				key = $t.attr('data-style-field')
				if $t.attr('type') == 'checkbox'
					st[key] = ''
					if $t.prop('checked')
						st[key] = 1
				else
					st[key] = $t.val()
				return
			ppbData.grids[window.ppbRowI].style = st
			prevu.sync ($r, qry) ->
				id = '#pg-' + qry.post + '-' + window.ppbRowI
				$ro = $r.find(id)
				$ro.addClass 'pootle-live-editor-new-row'
				$(id).replaceWith $ro
				$ro = $('.pootle-live-editor-new-row')
				$('html').trigger 'pootlepb_le_content_updated', [ $ro ]
				$ro.removeClass 'pootle-live-editor-new-cell'
				$(id).prevuRowInit()
				return
#			$rowPanel.ppbDialog 'close'
			return
		addRow: (callback, blockData, rowStyle, cellWidths) ->
			window.ppbRowI = ppbData.grids.length
			num_cells = parseInt($('#ppb-row-add-cols').val())
			cellWidths = if cellWidths then cellWidths else []
			logPPBData 'Adding row'
			row =
				id: window.ppbRowI
				cells: num_cells
				style: if 'object' == typeof rowStyle then rowStyle else
					background: ''
					background_image: ''
					background_image_repeat: ''
					background_image_size: 'cover'
					background_parallax: ''
					background_toggle: ''
					bg_color_wrap: ''
					bg_image_wrap: ''
					bg_mobile_image: ''
					bg_overlay_color: ''
					bg_overlay_opacity: '0.5'
					bg_video: ''
					bg_video_wrap: ''
					bg_wrap_close: ''
					class: ''
					col_class: ''
					col_gutter: '1'
					full_width: ''
					hide_row: ''
					margin_bottom: '0'
					margin_top: '0'
					row_height: '0'
					style: ''

			ppbData.grids.push row

			cells =
				grid: window.ppbRowI
				weight: 1 / num_cells

			defaultText =  if typeof blockData == 'string' then blockData else '<h2>Hi there,</h2><p>I am a new content block, go ahead, edit me and make me cool...</p>'

			if ( typeof blockData != 'object' )
				blockData = []

			block =
				text: defaultText
				info:
					class: 'Pootle_PB_Content_Block'
					grid: window.ppbRowI
					style: '{"background-color":"","background-transparency":"","text-color":"","border-width":"","border-color":"","padding":"","rounded-corners":"","inline-css":"","class":"","wc_prods-add":"","wc_prods-attribute":"","wc_prods-filter":null,"wc_prods-ids":null,"wc_prods-category":null,"wc_prods-per_page":"","wc_prods-columns":"","wc_prods-orderby":"","wc_prods-order":""}'
			i = 0
			firstCellI = null

			while i < num_cells
				id = ppbData.grid_cells.length

				if ( typeof firstCellI != 'number' )
					firstCellI = id

				cells.id = id
				cells.weight = 1 / num_cells

				if ( cellWidths[i] )
					if ( cellWidths[i].weight )
						cells.weight = cellWidths[i].weight
					else if typeof cellWidths[i] == 'number'
						cells.weight = cellWidths[i]

				i++
				ppbData.grid_cells.push $.extend(true, {}, cells)

			num_content = if blockData.length then blockData.length else num_cells;

			i = 0
			while i < num_content
				id = ppbData.widgets.length
				block.info.cell = i
				block.info.id = id
				if ( blockData[ i ] )
					block.text = if typeof blockData[ i ].text == 'string' then blockData[ i ].text else defaultText
					block.info.style = if typeof blockData[ i ].style == 'string' then blockData[ i ].style else '{}'
					if ( typeof blockData[ i ].cell != 'undefined' )
						block.info.cell = blockData[ i ].cell
				i++
				ppbData.widgets.push $.extend(true, {}, block)

			logPPBData 'Row added'
			$addRowDialog.ppbDialog 'close'
			prevu.sync ($r, qry, html) ->
				$ro = $r.find('#pg-' + qry.post + '-' + window.ppbRowI)
				$cols = $ro.find('.panel-grid-cell-container > .panel-grid-cell')
				$('.ppb-block.active, .ppb-row.active').removeClass 'active'
				$ro.find('.pootle-live-editor-realtime:eq(0)').parents('.ppb-block, .ppb-row').addClass 'active'
				$('.pootle-live-editor.add-row').before $ro
				$ro = $('#pg-' + qry.post + '-' + window.ppbRowI)
				$ro.prevuRowInit()
				if 'function' == typeof callback
					callback $ro
				return
			return
		syncRowPosition: ( olI, newI ) ->
			diff = -1
			$focussedContent = $('.mce-edit-focus')
			# Save content block
			prevu.saveTmceBlock $focussedContent
			$focussedContent.removeClass 'mce-edit-focus'
			if `newI == olI`
				return
			ppbData.grids.ppbPrevuMove olI, newI
			range = [
				olI
				newI
			].sort((a, b) ->
				a - b
			)
			if newI < olI
				diff = 1
			$.each ppbData.widgets, (i, v) ->
				if v and v.info
					gi = parseInt(v.info.grid)
					if range[0] <= gi and range[1] >= gi
						if `gi == olI`
							ppbData.widgets[i].info.grid = newI
						else
							ppbData.widgets[i].info.grid = gi + diff
				return
			$.each ppbData.grid_cells, (i, v) ->
				if v
					gi = parseInt(v.grid)
					ppbData.grid_cells[i].old_grid = gi
					if range[0] <= gi and range[1] >= gi
						if `gi == olI`
							ppbData.grid_cells[i].grid = newI
						else
							ppbData.grid_cells[i].grid = gi + diff
				return
			prevu.resort()
			prevu.sync ->
				prevu.reset 'noSort'
				return
			logPPBData 'Moved row ' + olI + ' => ' + newI
		rowsSortable:
			items: '> .panel-grid'
			handle: '.ppb-edit-row .drag-handle'
			start: (e, ui) ->
				$( this ).data 'draggingRowI', $ppb.children( '.ppb-row' ).index( ui.item )
				return
			update: (e, ui) ->
				prevu.syncRowPosition( $ppb.data( 'draggingRowI' ), $ppb.children( '.ppb-row' ).index( ui.item ) );
				return
		resizableCells:
			handles: 'w'
			start: ->
				prevu.resizableCells.correctCellData $(this)
				$(this).siblings('.panel-grid-cell').each ->
					prevu.resizableCells.correctCellData $(this)
					return
				return
			stop: (event, ui) ->
				$(this).parent().removeClass 'ppb-cols-resizing'
				return
			resize: (event, ui) ->
				$t = $(this)
				$p = $t.parent()
				$prev = $t.prev()
				widthTaken = 1
				widthNow = ui.size.width
				originalWidth = ui.originalSize.width
				totalWidth = $p.innerWidth()
				$p.addClass 'ppb-cols-resizing'
				$t.css 'width', 100 * $t.innerWidth() / totalWidth + '%'
				$prev.siblings('.panel-grid-cell').each ->
					widthTaken += $(this).outerWidth()
					return
				widthTaken += parseInt($prev.css('padding-left')) + parseInt($prev.css('padding-right'))
				$prev.css 'width', (100 * ( totalWidth - widthTaken - 1 ) / totalWidth) + '%'
				prevu.resizableCells.correctCellData $t
				prevu.resizableCells.correctCellData $prev
				prevu.unSavedChanges = true
				if originalWidth < widthNow #Increasing width
					if $p.width() * 0.93 < widthTaken
						$t.resizable('widget').trigger 'mouseup'
				else #Decreasing width
					if $p.width() * 0.07 > $t.width()
						$t.resizable('widget').trigger 'mouseup'
				return
			correctCellData: ($t) ->
				width = $t.outerWidth()
				pWidth = $t.parent().width() + 1
				i = $('.panel-grid-cell-container > .panel-grid-cell').not('.ppb-block *').index($t)
				weight = Math.floor(10000 * width / pWidth) / 10000
				$t.find('.pootle-live-editor.resize-cells').html '<div class="weight">' + Math.round(1000 * weight) / 10 + '%</div>'
				ppbData.grid_cells[i].weight = weight
				weight
		contentDraggable:
			handle: '.ppb-edit-block .drag-handle'
			grid: [
				5
				5
			]
			start: (e, ui) ->
				$t = $(this)
				$ro = $t.closest('.panel-row-style')
				roMinHi = $ro.css('min-height')
				if roMinHi
					$ro.find('.panel-grid-cell-container, .ppb-col').not('.ppb-block *').css 'min-height', roMinHi
				$t.find('.ppb-edit-block .dashicons-before:first').click()
				ui.position.left = parseInt($t.css('margin-left'))
				ui.position.top = parseInt($t.css('margin-top'))
				return
			drag: (e, ui) ->
				$t = $(this)
				$p = $t.parent()
				$ro = $t.closest('.panel-row-style')
				mg =
					t: parseInt($t.css('margin-top'))
					l: parseInt($t.css('margin-left'))
				top = ui.position.top + mg.t
				left = ui.position.left + mg.l
				hiMrgn = (parseInt($ro.css('min-height')) - $t.outerHeight()) / 2
				wiMrgn = ($p.width() - parseInt($t.outerWidth())) / 2
				if top < -25 or left < -25
					$t.draggable('widget').trigger 'mouseup'
				$ro.removeClass 'pootle-guides-x pootle-guides-y'
				if hiMrgn > 25 and Math.abs(hiMrgn - top) < 25
					$ro.addClass 'pootle-guides-x'
					ui.position.top = hiMrgn - (mg.t)
				if wiMrgn > 25 and Math.abs(wiMrgn - left) < 25
					$ro.addClass 'pootle-guides-y'
					ui.position.left = wiMrgn - (mg.l)
				return
			stop: (e, ui) ->
				st = JSON.parse(ppbData.widgets[window.ppbPanelI].info.style)
				margin = {}
				$t = $(this)
				center =
					h: 'ppb-content-h-center'
					v: 'ppb-content-v-center'
				$ro = $t.closest('.panel-row-style')

				# Ensuring st['class'] exists
				st['class'] = if st['class'] then st['class'] else ''

				if ( $ro.hasClass 'pootle-guides-y' )
					st['class'] += if 0 > st['class'].indexOf( center.h ) then " #{center.h}" else ''
				else
					st['class'] = st['class'].replace( new RegExp("[ ]?#{center.h}", "gi"), '' )

				if ( $ro.hasClass 'pootle-guides-x' )
					st['class'] += if 0 > st['class'].indexOf( center.v ) then " #{center.v}" else ''
				else
					st['class'] = st['class'].replace( new RegExp("[ ]?#{center.v}", "gi"), '' )

				$ro.removeClass 'pootle-guides-x pootle-guides-y'
				st['margin-top'] = Math.max(1, ui.position.top + parseInt($t.css('margin-top')))
				st['margin-left'] = Math.max(1, ui.position.left + parseInt($t.css('margin-left')))
				$t.css
					marginTop: st['margin-top']
					top: ''
					marginLeft: st['margin-left']
					left: ''
					width: ''
					height: ''
				ppbData.widgets[window.ppbPanelI].info.style = JSON.stringify(st)
				return
		contentResizable:
			handles:
				e: '.ui-resizable-e'
				w: '.ui-resizable-w'
			start: (e, ui) ->
				$t = $(this)
				$t.find('.ppb-edit-block .dashicons-before:first').click()
				$t.css maxWidth: 9999
				return
			stop: (event, ui) ->
				st = JSON.parse(ppbData.widgets[window.ppbPanelI].info.style)
				$t = $(this)
				$p = $t.parent()
				st['width'] = Math.round(parseInt($t.width()))
				st['margin-left'] = Math.max(1, ui.position.left + parseInt($t.css('margin-left')))
				$t.css
					maxWidth: st['width']
					marginLeft: st['margin-left']
					left: ''
					width: ''
				ppbData.widgets[window.ppbPanelI].info.style = JSON.stringify(st)
				return
			resize: (event, ui) ->
				st = JSON.parse(ppbData.widgets[window.ppbPanelI].info.style)
				$t = $(this)
				$p = $t.parent()
				if $t.outerWidth() - 7 > $p.width()
					$t.css 'width', ''
					$t.resizable('widget').trigger 'mouseup'
				return
		moduleDraggable:
			helper: 'clone'
			start: ->
				$mods.removeClass 'toggle'
				return
		insertModule: ($contentblock, $module) ->
			tab = $module.data('tab')
			$contentblock.find('.dashicons-move').click()
			$ed = $contentblock.find('.mce-content-body')
			ed = tinymce.get($ed.attr('id'))
			if ( ed )
				ed.selection.select tinyMCE.activeEditor.getBody(), true
				ed.selection.collapse false
			if $module.data('callback')
				if typeof window.ppbModules[$module.data('callback')] == 'function'
					window.ppbModules[$module.data('callback')] $contentblock, ed, $ed
			if tab
				if 0 < tab.indexOf('-row-tab')
					$('.panel-grid.active').find('.ppb-edit-row .settings-dialog').click()
				else
					$contentblock.find('.ppb-edit-block .settings-dialog').click()
				$('a.ppb-tabs-anchors[href="' + tab + '"]').click()
			return
		moduleDroppable:
			accept: '.ppb-module-existing-row'
			activeClass: 'ppb-drop-module'
			hoverClass: 'ppb-hover-module'
			drop: (e, ui) ->
				$m = ui.draggable
				$t = $(this)
				if $t.hasClass('add-row')
					if $m.data('row-callback') && typeof window.ppbModules[$m.data('row-callback')] == 'function'
						window.ppbModules[ $m.data('row-callback') ]()
					else
						$('#ppb-row-add-cols').val '1'
						prevu.addRow (($row) ->
							setTimeout (->
								prevu.insertModule $row.find('.ppb-block').last(), $m
								return
							), 106
							return
						), '<p>&nbsp;</p>'
				else
					prevu.insertModule $t, $m
				return
		insertImage: ->
# If the media frame already exists, reopen it.
			if prevu.insertImageFrame
				prevu.insertImageFrame.open()
				return
			# Create the media frame.
			prevu.insertImageFrame = wp.media(
				library: type: 'image'
				displaySettings: true
				displayUserSettings: false
				title: 'Choose Image'
				button: text: 'Insert in Content Block'
				multiple: false)
			prevu.insertImageFrame.on 'attach', ->
				$('.setting[data-setting="url"]').before '<label class="setting" data-setting="url">' + '<span class="name">Size</span>' + '<input type="text" value="" readonly="">' + '</label>'
				return
			# When an image is selected, run a callback.
			prevu.insertImageFrame.on 'select', ->
# We set multiple to false so only get one image from the uploader
				img = prevu.insertImageFrame.state().get('selection').first().toJSON()
				# Do something with img.id and/or img.url here
				$img = '<figure id="attachment_' + img.id + '" class="' + (if img.caption then 'wp-caption' else '') + '">' + '<img class="size-medium wp-image-' + img.id + '" src="' + img.url + '" alt="' + img.alt + '">' + (if img.caption then '<figcaption class="wp-caption-text">' + img.caption + '</figcaption>' else '') + '</figure>'
				ed = tinymce.get(prevu.activeEditor.attr('id'))
				ed.selection.select tinyMCE.activeEditor.getBody(), true
				ed.selection.collapse false
				ed.execCommand 'mceInsertContent', false, $img
				return
			# Finally, open the modal
			prevu.insertImageFrame.open()
			return
		saveTmceBlock: ($ed) ->
			if !$ed or !$ed.length
				return
			blockI = $ed.siblings('.pootle-live-editor').data('index')
			if !ppbData.widgets[blockI]
				return
			ppbData.widgets[blockI].text = $ed.html()
			prevu.unSavedChanges = true
			return
		postSettings: ->
			$postSettingsDialog.ppbDialog 'open'
			return
		tmce: $.extend(true, {}, tinyMCEPreInit.mceInit.ppbeditor)
		sidePanelNav: ->
			$t = $ this
			$p = $t.closest '.ppb-cool-panel'
			if $t.hasClass 'back'
				$p.removeClass 'show-panel'
			else
				$p.addClass 'show-panel'
		closeSidePanel: (callback) ->
			(e,ui)->
				$body.css 'margin-left', 0
				$( this ).closest( '.show-panel' ).removeClass 'show-panel'
				ppbCorrectOnResize( )
				if typeof callback is 'function' then callback( )
		openSidePanel: (callback) ->
			->
				$body.css 'margin-left', 300
				ppbCorrectOnResize()
				if typeof callback is 'function' then callback()
		saveFieldsOnChange: ()		->
			$t = $ this
			$d = $t.closest '.ppb-dialog-buttons.show-panel'
			if ( $d.length )
				to = $d.data 'saveTimeout'
				if ( to == 'saving' )
					return;
				else if ( to )
					clearTimeout( to )

				$d.data( 'saveTimeout', setTimeout(
					->
						$d.data( 'saveTimeout', 'saving' )
						$d.find( '.ppb-dialog-buttonset button' ).click()
						$d.data( 'saveTimeout', '' )
				, 2500
				) )

	prevu.showdown = new (showdown.Converter)
	dialogAttr.open = prevu.openSidePanel( prevu.editPanel )
	dialogAttr.buttons.Done =  prevu.savePanel
	dialogAttr.close = prevu.closeSidePanel() # Returns callback
	$contentPanel.ppbTabs().ppbDialog dialogAttr

	dialogAttr.title = 'Edit row'
	dialogAttr.open = prevu.openSidePanel( prevu.editRow )
	dialogAttr.buttons.Done = prevu.saveRow
	$rowPanel.ppbTabs( {
		activate: ( e, ui ) ->
			if ui.newPanel
				ui.newPanel.find( '#ppbeditor_ifr' ).css( 'height', ui.newTab.innerHeight() - 268 )
	} ).ppbDialog dialogAttr

	$panels.find( 'a' ).click prevu.sidePanelNav

	$panels.on 'change', '[data-style-field], [dialog-field], input, textarea', prevu.saveFieldsOnChange

	setTimeout(
		->
			tinyMCE.get('ppbeditor').on(
				'change keyup paste',
				->
					prevu.saveFieldsOnChange.apply this.container
			)
		, 700
	);

	panels.addInputFieldEventHandlers $rowPanel

	dialogAttr.title = 'Add row'
	dialogAttr.dialogClass = dialogAttr.open = null
	dialogAttr.buttons.Done = () ->
		prevu.addRow $addRowDialog.callback
		$addRowDialog.callback = null

	dialogAttr.height = if ppbAjax.ipad then 268 else 232
	dialogAttr.width = 340
	$addRowDialog.ppbDialog dialogAttr

	dialogAttr.title = 'Are you sure'
	dialogAttr.buttons =
		'Yes': ->
			if 'function' == typeof prevu.deleteCallback
				prevu.deleteCallback()
			delete prevu.deleteCallback
			$deleteDialog.ppbDialog 'close'
			return
		'Cancel': ->
			$deleteDialog.ppbDialog 'close'
			return
	dialogAttr.height = if ppbAjax.ipad then 241 else 200
	dialogAttr.width = 430
	$deleteDialog.ppbDialog dialogAttr
	dialogAttr.buttons = Done: ->
		$setTitleDialog.ppbDialog 'close'
		prevu.syncAjax()
		return
	dialogAttr.height = if ppbAjax.ipad then 232 else 227
	dialogAttr.width = 430
	dialogAttr.title = $setTitleDialog.data('title')

	dialogAttr.close = ->
		ppbAjax.title = $('#ppble-live-page-title').val()
		return

	$setTitleDialog.ppbDialog dialogAttr

	dialogAttr.close = false

	dialogAttr.buttons = Cancel: ->
		$setTitleDialog.ppbDialog 'close'
	dialogAttr.height = window.innerHeight - 50
	dialogAttr.open = ( event, ui ) ->
		$(this).find( '.templates-wrap' ).masonry()
	dialogAttr.width = window.innerWidth - 50
	dialogAttr.title = $designTemplateDialog.data('title')
	$designTemplateDialog.ppbDialog dialogAttr

	delete dialogAttr.open

	dialogAttr.height = 610
	dialogAttr.width = 520
	dialogAttr.title = 'Insert icon'
	dialogAttr.buttons = [
		{
			text: 'Remove icon'
			class: 'ui-button-link'
			click: ->
				if 'function' == typeof pickFaIcon.callback
					pickFaIcon.callback
						html: ''
						attr: ''
						style: ''
						class: ''
						size: ''
						color: ''
				$iconPicker.ppbDialog 'close'
				return

		}
		{
			text: 'Insert'
			click: ->
				$iconPicker.ppbDialog 'close'
				iclas = $iconPicker.clas.val()
				icolr = $iconPicker.colr.val()
				isize = $iconPicker.size.val()
				ilink = $iconPicker.link.val()
				style = 'font-size:' + isize + 'px;color:' + icolr
				attr = 'style="' + style + '" class="fa ' + iclas + '"'
				icon = '<i ' + attr + '><span style="display:none">' + iclas + '</span></i>'
				if 'function' == typeof pickFaIcon.callback
					pickFaIcon.callback
						html: icon
						attr: attr
						style: style
						class: iclas
						size: isize
						color: icolr
						link: ilink
				return

		}
	]

	dialogAttr.close = ->

	$iconPicker.ppbDialog dialogAttr
	$iconPicker.find('#ppb-icon-choose').iconpicker placement: 'inline'
	$iconPicker.clas = $ '#ppb-icon-choose'
	$iconPicker.colr = $ '#ppb-icon-color'
	$iconPicker.size = $ '#ppb-icon-size'
	$iconPicker.link = $ '#ppb-icon-link'
	$iconPicker.prvu = $ '#ppb-icon-preview'

	prevu.iconPrevu = (e) ->
		iclas = $iconPicker.clas.val()
		icolr = $iconPicker.colr.val()
		isize = $iconPicker.size.val()
		style = 'font-size:' + isize + 'px;color:' + icolr
		attr = 'style="' + style + '" class="fa ' + iclas + '"'
		$iconPicker.prvu.html '<i ' + attr + '><span style="display:none">' + iclas + '</span></i>'
		return

	$iconPicker.clas.on 'iconpickerUpdated', prevu.iconPrevu
	$iconPicker.colr.wpColorPicker change: prevu.iconPrevu
	$iconPicker.size.change prevu.iconPrevu

	pickFaIcon = (callback, properties) ->
		$iconPicker.ppbDialog 'open'
		pickFaIcon.callback = callback
		$iconPicker.clas.add($iconPicker.find('.iconpicker-search')).val ''
		$iconPicker.prvu.html ''
		if !properties
			return
		if properties.class
			$iconPicker.clas.val(properties.class).change()
		if properties.color
			$iconPicker.colr.val(properties.color).change()
		if properties.size
			$iconPicker.size.val(parseInt(properties.size)).change()
		if properties.link
			$iconPicker.link.val(properties.link).change()
		return

	if $postSettingsDialog.length
		dialogAttr.height = 700
		dialogAttr.height = if ppbAjax.ipad then 529 else 502
		dialogAttr.width = 610
		dialogAttr.title = 'Post settings'

		dialogAttr.close = ->
			ppbAjax.category = $postSettingsDialog.find('.post-category').val()
			ppbAjax.tags = $postSettingsDialog.find('.post-tags').val()
			return

		dialogAttr.buttons.Done = ->
			$postSettingsDialog.ppbDialog 'close'
			prevu.syncAjax()
			return

		$postSettingsDialog.ppbDialog dialogAttr

	###
	$setTitleDialog = $postSettingsDialog;
	$('.panel-grid-cell-container > .panel-grid-cell').not('.ppb-block *').each ->
		prevu.resizableCells.correctCellData $(this)
		return
	###
	$ppb.delegate '.pootle-live-editor .dashicons-before', 'mousedown', ->
		$('.pootle-live-editor-realtime.has-focus').blur()
		return
	$ppb.delegate '.ppb-edit-row .dashicons-before', 'click', ->
		window.ppbRowI = $(this).closest('.pootle-live-editor').data('index')
		return
	$ppb.delegate '.ppb-edit-row .settings-dialog', 'click', ->
		$rowPanel.ppbDialog 'open'
		return
	$ppb.delegate '.ppb-edit-row .dashicons-admin-page', 'click', ->
		prevu.reset()
		$t = $(this).closest('.pootle-live-editor')
		rowI = $t.data('i')
		row = $.extend(true, {}, ppbData.grids[rowI])
		nuI = rowI + 1
		cells = []
		blocks = []
		window.ppbRowI = $t.closest('.pootle-live-editor').data('index')
		ppbData.grids.splice rowI, 0, row
		$.each ppbData.widgets, (i, v) ->
			if v and v.info
				blocks.push $.extend(true, {}, v)
				gi = parseInt(v.info.grid)
				if `gi == rowI`
					newBlock = $.extend(true, {}, v)
					newBlock.info.grid = nuI
					blocks.push newBlock
			return
		ppbData.widgets = $.extend(true, [], blocks.sort((a, b) ->
			a.info.grid - (b.info.grid)
		))
		$.each ppbData.grid_cells, (i, v) ->
			if v
				cells.push $.extend(true, {}, v)
				gi = parseInt(v.grid)
				if `gi == rowI`
					newCell = $.extend(true, {}, v)
					newCell.grid = nuI
					cells.push newCell
			return
		ppbData.grid_cells = $.extend(true, [], cells.sort((a, b) ->
			a.grid - (b.grid)
		))
		prevu.sync ($r, qry) ->
			$ro = $r.find('#pg-' + qry.post + '-' + window.ppbRowI)
			$cols = $ro.find('.panel-grid-cell-container > .panel-grid-cell')
			$cols.css 'width', 101 / $cols.length - 1 + '%'
			$ro.prevuRowInit()
			$t.closest('.panel-grid').after $ro
			return
		prevu.reset()
		logPPBData()
		return
	$ppb.delegate '.ppb-edit-row .dashicons-no', 'click', ->
		removeCells = []
		removeBlocks = []
		$t = $(this)
		rowI = parseInt $t.closest('.pootle-live-editor').data('index')

		prevu.deleteCallback = ->
			# Save current block and remove class
			prevu.saveTmceBlock $( '.mce-edit-focus' ).removeClass( 'mce-edit-focus' )
			ppbData.grids.splice rowI, 1
			$.each ppbData.widgets, (i, v) ->
				if v and v.info
					if rowI == parseInt v.info.grid
						removeBlocks.push i
					else if rowI < v.info.grid
						ppbData.widgets[i].info.grid--
				return
			$.each ppbData.grid_cells, (i, v) ->
				if v
					gi = parseInt v.grid
					if rowI == gi
						removeCells.push i
					else if rowI < gi
						ppbData.grid_cells[i].old_grid = gi
						ppbData.grid_cells[i].grid = --gi
				return
			#Sort in decending order
			removeBlocks.sort (a, b) ->
				b - a
			removeCells.sort (a, b) ->
				b - a
			$.each removeBlocks, (i, v) ->
				ppbData.widgets.splice v, 1
				return
			$.each removeCells, (i, v) ->
				ppbData.grid_cells.splice v, 1
				return
			ppbData.grids.filter ->
				true
			ppbData.widgets.filter ->
				true
			ppbData.grid_cells.filter ->
				true
			#Remove row from preview
			$t.closest('.panel-grid').remove()
			prevu.sync ->
				prevu.reset()
				return
			return

		$deletingWhat.html 'row'
		$deleteDialog.ppbDialog 'open'
		return
	$ppb.delegate '.ppb-edit-block .dashicons-before', 'click touchstart', ->
		$t = $(this)
		$('.ppb-block.active, .ppb-row.active').removeClass 'active'
		$t.parents('.ppb-block, .ppb-row').addClass 'active'
		window.ppbPanelI = $t.closest('.pootle-live-editor').data('index')
		prevu.activeEditor = $(this).closest('.ppb-block').children('.pootle-live-editor-realtime')
		return
	$ppb.delegate '.ppb-edit-block .settings-dialog', 'click', ->
		$contentPanel.ppbDialog 'open'
		return
	$ppb.delegate '.ppb-edit-block .dashicons-no', 'click', ->
		prevu.reset()
		# Reset the indices
		$t = $(this)
		i = $t.closest('.pootle-live-editor').data('index')

		prevu.deleteCallback = ->
			ppbData.widgets.splice i, 1
			# Remove the content block data
			$t.closest('.ppb-block').remove()
			# Remove block html element
			prevu.reset()
			# Reset the indices again
			return

		$deletingWhat.html 'content block'
		$deleteDialog.ppbDialog 'open'
		return
	$ppb.delegate '.ppb-edit-block .pootle-live-editor-addons .pootle-live-editor-addon', 'click', ->
		$t = $(this)
		$contentPanel.ppbDialog 'open'
		$contentPanel.find('a[href="#pootle-' + $t.data('id') + '-tab"]').click()
		return
	$ppb.delegate '.ppb-edit-block .dashicons-format-image', 'click', (e) ->
		e.preventDefault()
		prevu.insertImage()
		return
	$ppb.delegate '.pootle-live-editor.add-row .dashicons-plus', 'click', ->
		$addRowDialog.ppbDialog 'open'
		$lastRow = $('.panel-grid:last-child')
		if $lastRow.length
			$('html, body').animate { scrollTop: $lastRow.height() + $lastRow.offset().top }, 1000
			return false
		return
	$body.on 'click touchstart', (e) ->
		$t = $(e.target)
		if !$t.closest('.ppb-block').length or $t.closest('.ppb-edit-row .dashicons-before, .ppb-edit-block .dashicons-before').length
			try
				webkit.messageHandlers.heySwift.postMessage 'hideTextFormatting'
				webkit.messageHandlers.heySwift.postMessage 'hideKeyboard'
			catch err
		else
			try
				webkit.messageHandlers.heySwift.postMessage 'showTextFormatting'
			catch err
		return

	$ppb.delegate '.ppb-edit-row .insert-row', 'click', ->
		$row = $( this ).closest '.ppb-row'
		$addRowDialog.callback = ( $t ) ->
			$ppb.data 'draggingRowI', $t.index()
			$t.insertBefore( $row )
			$ppb.sortable 'refresh'
			prevu.syncRowPosition $ppb.data( 'draggingRowI' ), $t.index()
		$ppb
			.find '.pootle-live-editor.add-row .dashicons-plus'
			.click()

	$ppb.delegate '.ppb-edit-row .dashicons-editor-code', 'click', ->
		if prevu.justClickedEditRow
			try
				webkit.messageHandlers.heySwift.postMessage 'hideKeyboard'
			catch err
			$t = $(this)
			window.ppbRowI = $t.closest('.pootle-live-editor').data('index')
			$rowPanel.ppbDialog 'open'
		else
			prevu.justClickedEditRow = true
			setTimeout (->
				prevu.justClickedEditRow = false
				return
			), 520
		return
	$ppb.delegate '.ppb-edit-block .dashicons-move', 'click', ->
		if prevu.justClickedEditBlock
			$t = $(this)
			window.ppbPanelI = $t.closest('.pootle-live-editor').data('index')
			$contentPanel.ppbDialog 'open'
		else
			prevu.justClickedEditBlock = true
			setTimeout (->
				prevu.justClickedEditBlock = false
				return
			), 520
		return
	ppbIpad.updatedNotice = $('#ppb-ipad-updated-notice')
	ppbIpad.notice = $('#ppb-ipad-notice')

	ppbIpad.AddRow = ->
		$addRowDialog.ppbDialog 'open'
		return

	ppbIpad.StyleRow = ->
		$row = $('.panel-grid.active')
		if $row.length != 1
			alert 'Please select a row by touching any of it\'s content blocks to start editing.'
			return
		$editBar = $row.children('.pootle-live-editor')
		window.ppbRowI = $editBar.data('index')
		$rowPanel.ppbDialog 'open'
		return

	ppbIpad.StyleContent = ->
		$block = $('.ppb-block.active')
		if $block.length != 1
			alert 'Please select a content block to start editing.'
			return
		$editBar = $block.children('.pootle-live-editor')
		window.ppbPanelI = $editBar.data('index')
		$contentPanel.ppbDialog 'open'
		return

	ppbIpad.insertImage = ->
		$block = $('.ppb-block.active')
		if $block.length != 1
			alert 'Please select a content block to start editing.'
			return
		prevu.activeEditor = $block.children('.pootle-live-editor-realtime')
		tinymce.execCommand 'mceFocus', false, prevu.activeEditor.attr('id')
		prevu.insertImage()
		return

	ppbIpad.preview = ->
		prevu.sync null, 'Publish'
		return

	ppbIpad.postSettings = ->
		prevu.postSettings()
		return

	ppbIpad.AddRow = ->
		$addRowDialog.ppbDialog 'open'
		return

	ppbIpad.Update = ->

		prevu.ajaxCallback = (no1, no2, url) ->
			window.location = url + '?ppb-ipad=preview'
			return

		prevu.unSavedChanges = true
		prevu.saveTmceBlock $('.mce-edit-focus')
		ppbAjax.data = ppbData
		ppbAjax.publish = 'Publish'
		prevu.noRedirect = 1
		if ppbAjax.title
			butt = [
				{
					text: 'Save Draft'
					click: ->
						$setTitleDialog.ppbDialog 'close'
						try
							webkit.messageHandlers.heySwift.postMessage 'updatedLoadingPreview'
						catch err
							console.log 'The native context does not exist yet'
						ppbIpad.notice.show 0
						ppbAjax.publish = 'Save Draft'
						prevu.syncAjax()
						return

				}
				{
					text: 'Publish'
					icons: primary: 'ipad-publish'
					click: ->
						$setTitleDialog.ppbDialog 'close'
						try
							webkit.messageHandlers.heySwift.postMessage 'updatedLoadingPreview'
						catch err
							console.log 'The native context does not exist yet'
						ppbIpad.notice.show 0
						ppbAjax.publish = 'Publish'
						prevu.syncAjax()
						return

				}
			]
			$setTitleDialog.parent().data 'action', 'Publish'
			$setTitleDialog.ppbDialog 'option', 'buttons', butt
			$setTitleDialog.ppbDialog 'open'
			return
		else
			try
				webkit.messageHandlers.heySwift.postMessage 'updatedLoadingPreview'
			catch err
				console.log 'The native context does not exist yet'
			ppbIpad.notice.show 0
		prevu.syncAjax()
		return

	$ppbIpadColorDialog.delegate '.ppb-ipad-color-picker span', 'mousedown', (e) ->
		e.preventDefault()
		false
	$ppbIpadColorDialog.delegate '.ppb-ipad-color-picker span', 'click', (e) ->
		e.preventDefault()
		tinymce.activeEditor.execCommand 'ForeColor', false, $(this).data('color')
		$ppbIpadColorDialog.hide()
		return
	ppbIpad.format =
		H1: ->
			tinymce.activeEditor.execCommand 'mceToggleFormat', false, 'h1'
			return
		H2: ->
			tinymce.activeEditor.execCommand 'mceToggleFormat', false, 'h2'
			return
		H3: ->
			tinymce.activeEditor.execCommand 'mceToggleFormat', false, 'h3'
			return
		H4: ->
			tinymce.activeEditor.execCommand 'mceToggleFormat', false, 'h4'
			return
		Quote: ->
			tinymce.activeEditor.execCommand 'mceBlockQuote'
			return
		Color: ->
			posTop = Math.max($(window).scrollTop(), $('.ppb-block.active').offset().top)
			$ppbIpadColorDialog.show().css 'top', posTop
			return
		Link: ->
			tinymce.activeEditor.execCommand 'PPB_Link'
			return
		Bold: ->
			tinymce.activeEditor.execCommand 'Bold'
			return
		Italic: ->
			tinymce.activeEditor.execCommand 'Italic'
			return
		Left: ->
			tinymce.activeEditor.execCommand 'JustifyLeft'
			return
		Center: ->
			tinymce.activeEditor.execCommand 'JustifyCenter'
			return
		Right: ->
			tinymce.activeEditor.execCommand 'JustifyRight'
			return
	$ppb.delegate '.pootle-live-editor.add-content .dashicons-plus', 'click', ->
		$t = $(this)
		id = $t.closest('.panel-grid-cell').attr('id')
		data = id.split('-')
		$t.closest('.panel-grid-cell').addClass 'this-cell-is-waiting'
		ppbData.widgets.push
			text: '<h2>Hi there,</h2><p>I am a new content block, go ahead, edit me and make me cool...</p>'
			info:
				class: 'Pootle_PB_Content_Block'
				grid: data[2]
				cell: data[3]
				style: '{"background-color":"","background-transparency":"","text-color":"","border-width":"","border-color":"","padding":"","rounded-corners":"","inline-css":"","class":"","wc_prods-add":"","wc_prods-attribute":"","wc_prods-filter":null,"wc_prods-ids":null,"wc_prods-category":null,"wc_prods-per_page":"","wc_prods-columns":"","wc_prods-orderby":"","wc_prods-order":""}'
		prevu.reset()
		ppbAjax.customData = id
		prevu.sync ($r, qry) ->
			$col = $r.find('#' + ppbAjax.customData)
			$col.addClass 'pootle-live-editor-new-cell'
			$('.this-cell-is-waiting').replaceWith $col
			$col = $('.pootle-live-editor-new-cell')
			$('html').trigger 'pootlepb_le_content_updated', [ $col ]
			$col.removeClass 'pootle-live-editor-new-cell'
			ppbAjax.customData = undefined
			return
		prevu.reset()
		return
	prevu.tmce.selector = '.pootle-live-editor-realtime:not(.mce-content-body)'
	#prevu.tmce.selector		= '.site-info';
	prevu.tmce.verify_html = false
	prevu.tmce.inline = true
	prevu.tmce.theme = 'ppbprevu'
	prevu.tmce.fontsize_formats = '20px 25px 30px 35px 40px 50px 70px 100px'
	if !ppbAjax.ipad
		prevu.tmce.toolbar = [
			'h1'
			'h2'
			'h3'
			'h4'
			'shrameeFonts'
			'fontsizeselect'
			'blockquote'
			'forecolor'
			'ppblink'
			'bold'
			'italic'
			'alignleft'
			'aligncenter'
			'alignright'
			'ppbInsertImage'
		]
		$postSettingsDialog.find('select').chosen()
	else
		prevu.tmce.plugins = prevu.tmce.plugins.replace('wpeditimage,', '').replace('wplink,', 'ppblink,')
		$('a').click (e) ->
			e.preventDefault()
			return
	prevu.tmce.content_css = ppbAjax.site + '/wp-includes/css/dashicons.min.css?ver=5.0.0'

	prevu.tmce.setup = (editor) ->
		editor.onDblClick.add (ed, e) ->
			$i = $(e.target)
			if $i.hasClass('fa')
				$a = $i.parent('a')
				pickFaIcon ((icon) ->
					if icon.class
						$i.attr
							class: 'fa ' + icon.class
							style: icon.style
						if icon.link
							if !$a.length
								$i.wrap '<a></a>'
								$a = $i.parent('a')
							$a.attr 'href', icon.link
						else
							if $i.parent('a').length
								$i.unwrap()
					else
						$i.closest('div.ppb-fa-icon').remove()
					prevu.saveTmceBlock $($i.closest('.mce-content-body'))
					return
				),
					class: $i.attr('class').replace('fa ', '')
					color: $i.css('color')
					size: $i.css('font-size')
					link: $a.attr('href')
			return
		editor.on 'change', (e) ->
			prevu.saveTmceBlock $(e.target.targetElm)
			return
		editor.on 'focus', (e) ->
			$t = $(e.target.targetElm)
			$('.ppb-block.active, .ppb-row.active').removeClass 'active'
			$t.parents('.ppb-block, .ppb-row').addClass 'active'
			return
		editor.addButton 'ppbInsertImage',
			text: ''
			icon: 'dashicons dashicons-format-image'
			onclick: ->
				ppbIpad.insertImage()
				return
		editor.addButton 'ppbAlign', ->
			items = [
				{
					icon: 'alignleft'
					tooltip: 'Align left'
					value: 'alignleft'
				}
				{
					icon: 'aligncenter'
					tooltip: 'Align center'
					value: 'aligncenter'
				}
				{
					icon: 'alignright'
					tooltip: 'Align right'
					value: 'alignright'
				}
			]
			{
				type: 'listbox'
				text: ''
				icon: 'alignleft'
				minWidth: 70
				onclick: ->
				onselect: (e) ->
					ed = tinymce.activeEditor
					val = @value().replace('align', '')
					ed.execCommand 'Justify' + val[0].toUpperCase() + val.substring(1)
					return
				values: items
				onPostRender: ->
					ed = tinymce.activeEditor
					self = this
					ed.on 'nodeChange', (e) ->
						formatter = ed.formatter
						value = null
						$.each e.parents, (ni, node) ->
							$.each items, (ii, item) ->
								if formatter.matchNode(node, item.value)
									self.value item.value
									self.settings.icon = item.icon
									return false
								return
							return
						return
					return

			}
		editor.addButton 'shrameeFonts', ->
			items = [
				{
					text: 'Default'
					value: 'inherit'
				}
				{
					text: 'Georgia'
					value: 'Georgia, serif'
				}
				{
					text: 'Arial Black'
					value: '"Arial Black", Gadget, sans-serif'
				}
				{
					text: 'Comic Sans MS'
					value: '"Comic Sans MS", cursive, sans-serif'
				}
				{
					text: 'Impact'
					value: 'Impact, Charcoal, sans-serif'
				}
				{
					text: 'Courier New'
					value: '"Courier New", Courier, monospace'
				}
				{
					text: 'Abril Fatface'
					value: 'Abril Fatface'
				}
				{
					text: 'Amatic SC'
					value: 'Amatic SC'
				}
				{
					text: 'Dancing Script'
					value: 'Dancing Script'
				}
				{
					text: 'Droid Serif'
					value: 'Droid Serif'
				}
				{
					text: 'Great Vibes'
					value: 'Great Vibes'
				}
				{
					text: 'Inconsolata'
					value: 'Inconsolata'
				}
				{
					text: 'Indie Flower'
					value: 'Indie Flower'
				}
				{
					text: 'Lato'
					value: 'Lato'
				}
				{
					text: 'Lobster'
					value: 'Lobster'
				}
				{
					text: 'Lora'
					value: 'Lora'
				}
				{
					text: 'Oswald'
					value: 'Oswald'
				}
				{
					text: 'Pacifico'
					value: 'Pacifico'
				}
				{
					text: 'Passion One'
					value: 'Passion One'
				}
				{
					text: 'Patua One'
					value: 'Patua One'
				}
				{
					text: 'Playfair Display'
					value: 'Playfair Display'
				}
				{
					text: 'Poiret One'
					value: 'Poiret One'
				}
				{
					text: 'Raleway'
					value: 'Raleway'
				}
				{
					text: 'Roboto'
					value: 'Roboto'
				}
				{
					text: 'Roboto Condensed'
					value: 'Roboto Condensed'
				}
				{
					text: 'Roboto Mono'
					value: 'Roboto Mono'
				}
				{
					text: 'Roboto Slab'
					value: 'Roboto Slab'
				}
				{
					text: 'Shadows Into Light'
					value: 'Shadows Into Light'
				}
				{
					text: 'Sigmar One'
					value: 'Sigmar One'
				}
				{
					text: 'Source Sans Pro'
					value: 'Source Sans Pro'
				}
				{
					text: 'Ubuntu Mono'
					value: 'Ubuntu Mono'
				}
			]
			{
				type: 'listbox'
				text: 'Font'
				icon: false
				minWidth: 70
				classes: 'shramee-fonts-control'
				onclick: ->
				onselect: (e) ->
					ed = tinymce.activeEditor
					val = @value()
					if !val
						ed.formatter.remove 'shrameeFontFormat'
					if -1 == val.indexOf(',')
						ed.formatter.apply 'shrameeFontFormat',
							font: val
							gfont: val.replace(' ', '+')
						$body.append '<link href="https://fonts.googleapis.com/css?family=' + val.replace(' ', '+') + '"  rel="stylesheet">'
					else
						ed.formatter.apply 'shrameeFontFormat', font: val
					return
				values: items
				onPostRender: ->
					ed = tinymce.activeEditor
					self = this
					ed.on 'nodeChange', (e) ->
						value = null
						$(e.parents).each ->
							font = $(this).css('font-family')
							$.each items, (ii, item) ->
								if -1 < font.indexOf(item.text)
									value = item.value
									$('.mce-shramee-fonts-control').find('.mce-txt').html item.text
									return false
								return
							if value
								return false
							return
						if !value
							$('.mce-shramee-fonts-control').find('.mce-txt').html 'Font'
							value = 'inherit'
						self.state.set 'value', value
						return
					return

			}
		editor.addButton 'ppbFontStyles', ->
			items = [
				{
					text: 'Elegant shadow'
					value: 'ppbfost-elegant-shadow'
				}
				{
					text: 'Deep shadow'
					value: 'ppbfost-deep-shadow'
				}
				{
					text: 'Inset shadow'
					value: 'ppbfost-inset-shadow'
				}
				{
					text: 'Retro shadow'
					value: 'ppbfost-retro-shadow'
				}
			]
			lastVal = ''
			{
				type: 'listbox'
				text: 'Font Style'
				icon: false
				minWidth: 70
				classes: 'ppbFoStField'
				onselect: (e) ->
					ed = tinymce.activeEditor
					val = @value()
					$.each items, (ii, item) ->
						ed.formatter.remove 'ppbFoStFormat', value: item.value
						return
					ed.formatter.apply 'ppbFoStFormat', value: val
					return
				values: items
				onPostRender: ->
					ed = tinymce.activeEditor
					self = this
					ed.on 'nodeChange', (e) ->
						value = null
						$(e.parents).each ->
							$t = $(this)
							$.each items, (ii, item) ->
								if $t.hasClass(item.value)
									value = item.value
									$('.mce-ppbFoStField').find('.mce-txt').html item.text
									return false
								return
							if value
								return false
							return
						if !value
							$('.mce-ppbFoStField').find('.mce-txt').html 'Font Style'
							value = 'inherit'
						lastVal = value
						self.state.set 'value', value
						return
					return

			}
		return

	prevu.tmce.formats =
		shrameeFontFormat:
			inline: 'span'
			classes: 'ppb-google-font'
			attributes: 'data-font': '%gfont'
			styles: fontFamily: '%font'
		ppbFoStFormat:
			block: 'h2'
			classes: '%value'
	tinymce.init prevu.tmce
	$ppb.sortable prevu.rowsSortable
	$ppb.find('.panel-grid').each ->
		$(this).prevuRowInit()
		return
	$('[href="#ppb-live-update-changes"]').click ->
		prevu.sync null, 'Save Draft'
		return
	$('[href="#ppb-live-post-settings"]').click ->
		$postSettingsDialog.ppbDialog('option', 'buttons', Done: ->
			$postSettingsDialog.ppbDialog 'close'
			prevu.sync 'Publish'
			return
		).ppbDialog 'open'
		return
	$('[href="#ppb-live-publish-changes"]').click ->
		prevu.sync null, 'Publish'
		return
	$('.ppb-edit-block').click ->
		editorid = $(this).siblings('.mce-content-body').attr('id')
		if tinymce.get(editorid) then tinymce.get(editorid).focus()
		return
	$('#ppble-feat-img-prevu').click ->
		event.preventDefault()
		# If the media frame already exists, reopen it.
		if typeof ppbFeaturedImageFrame != 'undefined'
			ppbFeaturedImageFrame.open()
			return
		# Create the media frame.
		ppbFeaturedImageFrame = wp.media.frames.ppbFeaturedImageFrame = wp.media(
			title: 'Featured Image'
			button: text: 'Set Featured Image'
			multiple: false)
		# When an image is selected, run a callback.
		ppbFeaturedImageFrame.on 'select', ->
# We set multiple to false so only get one image from the uploader
			attachment = ppbFeaturedImageFrame.state().get('selection').first().toJSON()
			# Do something with attachment.id and/or attachment.url here
			ppbAjax.thumbnail = attachment.id
			$('#ppble-feat-img-prevu').css 'background-image', 'url(' + attachment.sizes.thumbnail.url + ')'
			return
		# Finally, open the modal
		ppbFeaturedImageFrame.open()
		return

	window.onbeforeunload = (e) ->
		if prevu.unSavedChanges
			return 'You have unsaved changes! Click \'Update\' in admin bar to save.\n\nYour changes will be lost if you dan\'t save.'
		return

	prevu.resort()
	prevu.reset 'noSort'
	# Modules
	$mods.find('.ppb-module').draggable prevu.moduleDraggable



	prevu.newRowModuleDroppable = jQuery.extend(true, {}, prevu.moduleDroppable )
	prevu.newRowModuleDroppable.accept = '.ppb-module-new-row'

	$ppb.find('.ppb-block, .ppb-live-add-object.add-row').droppable prevu.newRowModuleDroppable

	window.ppbModules.image = ($t, ed) ->
		prevu.insertImage()
		return

	window.ppbModules.chooseIconDialog = ($t, ed, $ed) ->
		pickFaIcon (icon) ->
			ed.selection.setCursorLocation ed.getBody().firstChild, 0
			ed.selection.collapse false

			tag = 'div';

			if icon.link
				icon.html = '<a href="' + icon.link + '">' + icon.html + '</a>'

			if $('#ppb-icon-inline').prop('checked')
				tag = 'span'


			$ed.prepend '<' + tag + ' class="ppb-fa-icon" style="text-align: center;">&nbsp;&nbsp;' + icon.html + '&nbsp;&nbsp;</div>'
			prevu.saveTmceBlock $ed
			return
		return

	window.ppbModules.unsplash = ($t, ed) ->
		ShrameeUnsplashImage (url) ->
			$img = '<img src="' + url + '">'
			ed.selection.select tinyMCE.activeEditor.getBody(), true
			ed.selection.collapse false
			ed.execCommand 'mceInsertContent', false, $img
			return
		return

	window.ppbModules.button = ($t, ed) ->
		ed.execCommand 'pbtn_add_btn_cmd'
		return

	window.ppbModules.heroSection = ($t) ->
		$tlbr = $t.closest('.panel-grid').find('.ppb-edit-row')
		$tlbr.find('.ui-sortable-handle').click()
		ppbData.grids[ppbRowI].style.full_width = true
		ppbData.grids[ppbRowI].style.background_toggle = '.bg_image'
		ppbData.grids[ppbRowI].style.row_height = '500'

	window.ppbModules.onePager = ($t) ->
		$t.find('.ppb-edit-block .settings-dialog').click()
		$('a.ppb-tabs-anchors[href="#pootle-ppb-1-pager-tab"]').click()
		ppbModules.heroSection $t

	$tooltip = $ '#ppb-tooltip'
	$body.on 'mouseenter', 'a.pbtn,.ppb-fa-icon', (e) ->
			$tooltip.show().html( 'Double click to edit' ).css(
				top: e.clientY,
				left: e.clientX,
			)
	$body.on 'mouseleave', 'a.pbtn,.ppb-fa-icon', (e) ->
			$tooltip.hide()

	$body.on 'savingPPB', ->
		ppbAjax.data.google_fonts = []
		$body.find('[data-font]').not('[data-font="%gfont"]').each ->
			ppbAjax.data.google_fonts.push $(this).attr('data-font')

	# Live templates

	window.ppbModules.designTemplateRow = ($t, ed) ->
			$designTemplateDialog.ppbDialog 'open'

	applyDesignTemplate = ( e ) ->
		$target = $( e.target );
		if $target.hasClass( 'fa-search' )
			$designTemplatePreview.find( 'img' ).attr(
				'src',
				$target.siblings( 'img' ).attr( 'src' )
			);
			$designTemplatePreview.fadeIn( )

		else
			$t = $target.closest( '.ppb-tpl' )
			id = $t.data( 'id' )

			if $t.hasClass( 'pro-inactive' )
				ppbNotify 'Template ' + id + ' needs <a href="https://www.pootlepress.com/pootle-pagebuilder-pro/" target="_blank">Pootle Pagebuilder Pro</a> active.'
				return;

			tpl = ppbDesignTpls[id]
			style = if tpl.style then JSON.parse( tpl.style ) else {}
			style = if style.style then style.style else style
			cells = 1
			if tpl.cell
				cells = tpl.cell.length
			else if tpl.content
				cells = tpl.content.length

			$( '#ppb-row-add-cols' ).val cells

			prevu.addRow ( ( $row ) ->
				setTimeout ( ( ) ->
	#				prevu.insertModule $row.find('.ppb-block').last(), $m
				), 106
			), tpl.content, style, tpl.cell
			$designTemplateDialog.ppbDialog 'close'

	$designTemplateDialog.on 'click', '.ppb-tpl', applyDesignTemplate

	# Content updated hook
	$('html').on 'pootlepb_le_content_updated', (e, $t) ->
		ppbSkrollr.refresh $t.find( '.ppb-col' );

ppbTemplateFromRow = (rowI, thumb) ->
	if ! ppbData || ! ppbData.grids || ! ppbData.grids[ rowI ] then return {}

	rowI = parseInt( rowI )

	rowStyle = ppbData.grids[ rowI ].style

	if ! thumb
		thumb = rowStyle.background_image || rowStyle.grad_image || rowStyle.bg_mobile_image

	tpl =
		img: thumb
		content: []
		cell: []
		style: JSON.stringify rowStyle

	parseContent = ( cb ) ->
		tpl.content.push(
			cell: cb.info.cell
			style: cb.info.style
			text: cb.text
		)

	parseCell = ( cell ) ->
		tpl.cell.push parseFloat( cell.weight )

	parseContent cb for cb in ppbData.widgets when cb && cb.info && parseInt( cb.info.grid ) is rowI

	parseCell cell for cell in ppbData.grid_cells when cell && parseInt( cell.grid ) is rowI

	return JSON.stringify tpl

ppbNotify = ( notice ) ->
	$n = jQuery( '#ppb-notify' )
	$n.html( notice )
	$n.fadeIn()
	setTimeout(
		() ->
			$n.fadeOut()
		, 2000
	)