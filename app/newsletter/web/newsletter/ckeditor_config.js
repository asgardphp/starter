CKEDITOR.editorConfig = function( config )
{
		config.filebrowserBrowseUrl = '<?php echo Router::getFullAddress(); ?>bundles/_ckeditor/kcfinder/browse.php?type=files';
		config.filebrowserImageBrowseUrl = '<?php echo Router::getFullAddress(); ?>bundles/_ckeditor/kcfinder/browse.php?type=images';
		config.filebrowserFlashBrowseUrl = '<?php echo Router::getFullAddress(); ?>bundles/_ckeditor/kcfinder/browse.php?type=flash';
		config.filebrowserUploadUrl = '<?php echo Router::getFullAddress(); ?>bundles/_ckeditor/kcfinder/upload.php?type=files';
		config.filebrowserImageUploadUrl = '<?php echo Router::getFullAddress(); ?>bundles/_ckeditor/kcfinder/upload.php?type=images';
		config.filebrowserFlashUploadUrl = '<?php echo Router::getFullAddress(); ?>bundles/_ckeditor/kcfinder/upload.php?type=flash';
	
		config.contentsCss = '<?php echo Router::getFullAddress(); ?>bundles/newsletter/day_wysiwyg.css';

		config.baseHref = '<?php echo Router::getFullAddress() ?>';
	
		CKEDITOR.config.toolbar_Full = [
			{ name: 'document', items : [ 'Source','-','Save','NewPage','Preview','Print','-','Templates' ] },
			{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
			{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
			'/',
			{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
			{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-'] },
			{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
			{ name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','SpecialChar'] },
			'/',
			{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
			{ name: 'colors', items : [ 'TextColor','BGColor' ] },
			{ name: 'tools', items : [ 'Maximize', 'ShowBlocks'] }
		];
};

	  /*CKEDITOR.stylesSet.add('default', [
			{ name: 'Break', element: 'div', attributes: {'class': 'break'}},
			{ name: 'Time', element: 'span', attributes: {'class': 'time'}},
			{ name: 'Green', element: 'span', styles: {'color': '#6ab023'}},
			{ name: 'Red', element: 'span', styles: {'color': '#cf142b'}},
			{ name: 'Orange', element: 'span', styles: {'color': '#eb9f0c'}},
	  ]); */