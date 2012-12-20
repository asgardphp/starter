<?php
namespace Coxis\Core\Form\Widgets;

class WysiwygWidget extends \Coxis\Core\Form\Widgets\HTMLWidget {
	public function render($options=null) {
		if($options === null)
			$options = $this->options;
		
		$attrs = array(
			'rows'	=>	10,
			'cols'	=>	80,
		);
		if(isset($options['attrs']))
			$attrs = $options['attrs'];
		$id = isset($options['id']) ? $options['id']:null;
		
		HTML::include_js('bundles/ckeditor/ckeditor/ckeditor.js');
		HTML::include_js('bundles/ckeditor/ckeditor/_samples/sample.js');
		HTML::include_css('bundles/ckeditor/ckeditor/_samples/sample.css');
		return HTMLHelper::tag('textarea', array(
			'name'	=>	$this->name,
			'id'	=>	$id,
		)+$attrs,
		$this->value ? HTML::sanitize($this->value):'').
		"<script>
		//<![CDATA[
			$(function(){
				var CKEDITOR_BASEPATH = '".\URL::to('bundles/ckeditor/ckeditor/')."';
				CKEDITOR.basePath = '".\URL::to('bundles/ckeditor/ckeditor/')."';
				var editor = CKEDITOR.instances['".$id."'];
				if (editor)
					editor.destroy(true);
				CKEDITOR.replace('".$id."'
											, {
								customConfig : '".\URL::to('bundles/page/ckeditor_config.js')."'
							}
									);
			});
		//]]>
		</script>";
	}
}