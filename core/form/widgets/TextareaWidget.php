<?php
namespace Coxis\Core\Form\Widgets;

class TextareaWidget extends \Coxis\Core\Form\Widgets\HTMLWidget {
	public function render($options=null) {
		if($options === null)
			$options = $this->options;
		
		$attrs = array();
		if(isset($options['attrs']))
			$attrs = $options['attrs'];
		return HTMLHelper::tag('textarea', array(
			'name'	=>	$this->name,
			'id'	=>	isset($options['id']) ? $options['id']:null,
		)+$attrs,
		$this->value ? HTML::sanitize($this->value):'');
	}
}