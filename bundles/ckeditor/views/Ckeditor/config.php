/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	config.filebrowserBrowseUrl = '<?php echo URL::to('bundles/ckeditor/kcfinder/browse.php?type=files') ?>';
	config.filebrowserImageBrowseUrl = '<?php echo URL::to('bundles/ckeditor/kcfinder/browse.php?type=images') ?>';
	config.filebrowserFlashBrowseUrl = '<?php echo URL::to('bundles/ckeditor/kcfinder/browse.php?type=flash') ?>';
	config.filebrowserUploadUrl = '<?php echo URL::to('bundles/ckeditor/kcfinder/upload.php?type=files') ?>';
	config.filebrowserImageUploadUrl = '<?php echo URL::to('bundles/ckeditor/kcfinder/upload.php?type=images') ?>';
	config.filebrowserFlashUploadUrl = '<?php echo URL::to('bundles/ckeditor/kcfinder/upload.php?type=flash') ?>';

	CKEDITOR.config.toolbar_Full = [
		{ name: 'document', items : [ 'Source','-','Save','NewPage'
			//~ ,'DocProps'
			,'Preview','Print','-','Templates' ] },
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
		//~ { name: 'forms', items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 
			//~ 'HiddenField' ] },
		'/',
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote',
			//'CreateDiv',
		'-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-'
			//,'BidiLtr','BidiRtl' 
			] },
		{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
		{ name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule'
			//,'Smiley'
			,'SpecialChar'
			//,'PageBreak'
			//,'Iframe' 
			] 
			},
		'/',
		{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
		{ name: 'colors', items : [ 'TextColor','BGColor' ] },
		{ name: 'tools', items : [ 'Maximize', 'ShowBlocks'
			//,'-','About' 
			] 
			}
		
		//~ { name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
		//~ { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		//~ { name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
		//~ { name: 'forms', items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 
			//~ 'HiddenField' ] },
		//~ '/',
		//~ { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
		//~ { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv',
		//~ '-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
		//~ { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
		//~ { name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
		//~ '/',
		//~ { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
		//~ { name: 'colors', items : [ 'TextColor','BGColor' ] },
		//~ { name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }
	];
};

	  //~ CKEDITOR.stylesSet.add('default', [
			//~ { name: 'My Custom Block', element: 'h3', styles: { color: 'Blue'} },
			//~ { name: 'My Custom inline style', element: 'q'},
			//~ { name: 'My Custom inline style2', element: 'span', attributes: {'class': 'mine'}}
	  //~ ]); 