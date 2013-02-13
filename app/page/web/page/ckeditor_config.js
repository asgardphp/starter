CKEDITOR.editorConfig = function( config )
{
		config.filebrowserBrowseUrl = $('base').attr('href')+'../ckeditor/kcfinder/browse.php?type=files';
		config.filebrowserImageBrowseUrl = $('base').attr('href')+'../ckeditor/kcfinder/browse.php?type=images';
		config.filebrowserFlashBrowseUrl = $('base').attr('href')+'../ckeditor/kcfinder/browse.php?type=flash';
		config.filebrowserUploadUrl = $('base').attr('href')+'../ckeditor/kcfinder/upload.php?type=files';
		config.filebrowserImageUploadUrl = $('base').attr('href')+'../ckeditor/kcfinder/upload.php?type=images';
		config.filebrowserFlashUploadUrl = $('base').attr('href')+'../ckeditor/kcfinder/upload.php?type=flash';
	
		config.contentsCss = $('base').attr('href')+'../page/day_wysiwyg.css';
	
	CKEDITOR.config.toolbar_Full = [
			// { name: 'document', items : [ 'Source','-','Save','NewPage','Preview','Print','-','Templates' ] },
			{ name: 'document', items : [  'Source' ] },
			// { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
			{ name: 'clipboard', items : [ 'PasteText','-','Undo','Redo' ] },
			// { name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
			// '/',
			{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','-','RemoveFormat' ] },
			{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-'] },
			{ name: 'links', items : [ 'Link','Unlink' ] },
			{ name: 'insert', items : [ 'Image',
			// 'Flash',
			'Table','HorizontalRule','SpecialChar'] },
			'/',
			{ name: 'styles', items : [ 'Format','FontSize' ] },
			{ name: 'colors', items : [ 'TextColor','BGColor' ] },
			// ,
			// { name: 'tools', items : [ 'Maximize', 'ShowBlocks'] }
			{ name: 'tools', items : [ 'Maximize'] }
	];

	  /*CKEDITOR.stylesSet.add('default', [
			{ name: 'Break', element: 'div', attributes: {'class': 'break'}},
			{ name: 'Time', element: 'span', attributes: {'class': 'time'}},
			{ name: 'Green', element: 'span', styles: {'color': '#6ab023'}},
			{ name: 'Red', element: 'span', styles: {'color': '#cf142b'}},
			{ name: 'Orange', element: 'span', styles: {'color': '#eb9f0c'}},
	  ]); */
};