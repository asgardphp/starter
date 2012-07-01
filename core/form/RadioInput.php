<?php
class RadioInput extends SingleInput {
	public function input($options=array()) {
		$widget = $this->dad->params;
		
		$widget['view']	=	array_merge(isset($widget['view']) ? $widget['view']:array(), $options);
		
		$value = '';
		if($this->dad->value !== null)
			$value = $this->dad->value;
		elseif(isset($widget['default']))
			$value = $widget['default'];
			
		$params = array(
			'id'	=>	$this->getID(),
			'type'	=>	'radio',
			'name'	=>	$this->dad->getName(),
			'value'	=>	$this->key,
		);
		if($this->key == $value)
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
	}
}