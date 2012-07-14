<?php
require_once('core/form/SingleInput.php');

class CheckboxInput extends SingleInput {
	public function input($options=array()) {
		$widget = $this->dad->params;
		
		$widget['view']	=	array_merge(isset($widget['view']) ? $widget['view']:array(), $options);
		
		$values = array();
		if($this->dad->value !== null)
			$values = $this->dad->value;
		elseif(isset($widget['default']))
			$values = $widget['default'];
			
		$params = array(
			'id'	=>	$this->getID(),
			'type'	=>	'checkbox',
			'name'	=>	$this->dad->getName().'[]',
			'value'	=>	$this->key,
		);

		if(in_array($this->key, $values))
			$params['checked'] = 'checked';
		if(isset($widget['view']['class']))
			if(is_array($widget['view']['class']))
				$params['class'] = implode(' ', $widget['view']['class']);
			else
				$params['class'] = $widget['view']['class'];
				
		if(isset($options['attrs']))
			$params = array_merge($options['attrs'], $params);
				
		$res = HTMLHelper::tag('input', $params);
		
		echo $res;
		
		return $this;
	}
}