$(document).ready(function() {

	// Confirm to submit
	$('.js-confirm').on('click', function() {
		return confirm("Are you sure you want to continue?");
	});

	// Confirm to delete
	$('.js-delete').on('click', function(event) {
		event.preventDefault();

		var csrfToken = $(this).attr('data-token');

		if (confirm('Are you sure you want to continue?')) {
			$.ajax({
				url: $(this).attr('href'),
				type: 'DELETE',
				data: { _token: csrfToken },
				success: function(data) {
					window.location.reload();
				}
			});
		}
	});

	// Edit
	$('.js-edit').on('click', function(event) {
		event.preventDefault();

		// Trigger HTML5 form validation
		var valid = $('#form-edit')[0].reportValidity();
		if (!valid) {
			return;
		}

		var href = $(this).attr('data-href');
		$.ajax({
			url: href,
			type: "PUT",
			data: $('#form-edit').serialize(),
			success: function(data, textStatus, jqXHR) {
				window.location.href = data;
			}
		});
	});

//------------------------------------------

	$('.js-upload')
	.on("drag dragstart dragend dragover dragenter dragleave drop", function(e) {
		e.preventDefault();
		e.stopPropagation();
	})
	.on("dragover dragenter", function(e) {
		$(this).addClass('admin-upload-dragover');
	})
	.on("dragend dragleave drop", function(e) {
		$(this).removeClass('admin-upload-dragover');
	})
	.on("drop", function(e) {
		// Set file input to the dropped files
		$(this).find('input[type=file]')[0].files = e.originalEvent.dataTransfer.files;

		updateUploadLabel($(this));
	})
	.change(function(e) {
		updateUploadLabel($(this));
	});

	function updateUploadLabel(element) {
		var files = element.find('input[type=file]')[0].files;
		var text = element.find('span');

		// Update label text
		if (files.length == 1) {
			text.html(files[0].name);
		}
		else {
			text.html(files.length + ' files selected');

			// Set tooltip
			var fileNames = '';
			Array.from(files).forEach(file => { fileNames += file.name + '\n'; });
			element.prop('title', fileNames);
		}
	}

//------------------------------------------

	// CodeMirror editor
	var editor;
	var editorOptions = {
		lineSeparator: '\n',
		theme: 'tomorrow-night-eighties',
		mode: 'text/html',
		indentUnit: 4,
		lineNumbers: true,
		lineWrapping: true,
		cursorBlinkRate: 0,
		keyMap: 'vim',
	};

	// Copy editor selection to clipboard on <C-c>
	function registerEditorCopy() {
		editor.on('vim-keypress', function(e) {
			if (e === '<C-c>' && editor.state.vim.visualMode === true) {
				document.execCommand("copy");
			}
		});
	}

	// Enable CodeMirror Editor
	$.fn.codeMirror = function() {
		if (this.length === 0) return;

		editor = CodeMirror.fromTextArea($(this)[0], editorOptions);
		editor.setSize('100%', '500');
		registerEditorCopy();
	}
	$('textarea#syntax-code').codeMirror();

	// Enable WYSIWYG Editor
	$('textarea#summernote').summernote({
		tabDisable: true,
		tabsize: 4,
		minHeight: 450,
		maxHeight: null,
		focus: true,
		fontNames: [
			'Helvetica', 'Arial', 'Verdana', 'Trebuchet MS', 'sans-serif',
			'Georgia', 'Times New Roman', 'Courier New', 'monospace'
		],
		fontNamesIgnoreCheck: [],
		toolbar: [
			['style', ['style']],
			['font', ['bold', 'underline', 'italic', 'strikethrough', 'clear']],
			['fontname', ['fontname']],
			['color', ['color']],
			['para', ['ul', 'ol', 'paragraph']],
			['table', ['table']],
			['insert', ['link', 'picture', 'video']],
			['view', ['fullscreen', 'codeview', 'help']],
		],
		callbacks: {
			onInit: function() {
				$("#summernote").summernote('codeview.activate');

				editor = $('.CodeMirror')[0].CodeMirror;
				registerEditorCopy();
			}
		},
		prettifyHtml: false,
		codemirror: editorOptions,
	});

	var syntaxLanguages = {
		'c': 'text/x-csrc',
		'cpp': 'text/x-c++src',
		'css': 'text/css',
		'html': 'text/html',
		'javascript': 'text/javascript',
		'php': 'application/x-httpd-php',
		'python': 'text/x-python',
		'shell': 'text/x-sh',
	};

	// Syntax Language selection
	$('#syntax-language').on('change', function() {
		// Set the editor language mode
		editor.setOption('mode', syntaxLanguages[this.value]);

		// Set the language class
		var parse = $('code');
		parse.removeClass().addClass('language-' + this.value);
	})
	.trigger('change');

	// Syntax Highlight
	$('#syntax-highlight').on('click', function() {
		// Set the code
		var parse = $('code');
		parse.text(editor.getValue());

		// Highlight the <code> DOM element
		Prism.highlightAll();
	});

	// Copy highlighted syntax to the clipboard
	$('#syntax-copy').on('click', function() {
		// Copy text into hidden textarea
		var parse = $('div#syntax-parse').html();
		var copy = $('textarea#syntax-parse-copy');
		copy.val(parse);

		// Select the text field
		copy = copy[0];
		copy.select();
		copy.setSelectionRange(0, copy.value.length);

		// Copy the text inside the field to the clipboard
		document.execCommand("copy");

		alert("Copied to the clipboard");
	});

//------------------------------------------

	// Hotkeys
	$(window).keydown(function(e) {

		// Save form
		if (e.ctrlKey && e.keyCode == 83) { // Ctrl + S
			e.preventDefault();

			// Codeview needs to be deactived before saving
			$('#summernote').summernote('codeview.deactivate');

			$('.js-edit').click();
		}

		// Toggle codeview
		if (e.ctrlKey && e.keyCode == 71) { // Ctrl + G
			e.preventDefault();

			$('#summernote').summernote('codeview.toggle');
		}

	});

//------------------------------------------

	// Developer mode
	$('#development-mode').on('click', function(e)
	{
		e.preventDefault();

		if (!confirm('Are you sure you want to continue?')) {
			return;
		}

		$.get('/admin/toggle-development-mode').done(function(data)
		{
			const response = JSON.parse(data);
			if (response.success == false) {
				console.log(data);
				alert("Development mode could not be enabled!");
				return;
			}

			if (response.result.value == 'on') {
				e.target.checked = true;
				$('#develop-enabled').css('visibility', 'visible');
				$('#develop-remaining').text('03:00:00');
			}
			else {
				e.target.checked = false;
				$('#develop-enabled').css('visibility', 'hidden');
			}

			alert("Development mode has been set to: '" + response.result.value + "'");
		});
	});

});

// @Todo
// - Look at converting .ajax() into the JS fetch API (native)
