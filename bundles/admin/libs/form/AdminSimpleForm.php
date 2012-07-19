<?php
namespace Coxis\Bundles\Admin\Libs\Form;

class AdminSimpleForm extends Form {
	public function prepareLabel($widget, $options) {
		if(!isset($options['label']))
			$label = ucfirst(str_replace('_', ' ', $widget));
		else
			$label = $options['label'];
		
		if(get($this->$widget->params, 'required'))
			$label .= '*';
			
		return $label;
	}
	
	public function select($widget, $options=array(), $choices=array()) {
	}
	
	public function input($widget, $options=array()) {
		$label = $this->prepareLabel($widget, $options);
		
		AdminForm::input($this->$widget, $label, $options);
	}
	
	public function password($widget, $options=array()) {
		$label = $this->prepareLabel($widget, $options);
		
		AdminForm::password($this->$widget, $label, $options);
	}
	
	public function file($widget, $options=array()) {
		$label = $this->prepareLabel($widget, $options);
		
		AdminForm::file($this->$widget, $label, $options);
	}
	
	public function textarea($widget, $options=array()) {
		$label = $this->prepareLabel($widget, $options);
		
		AdminForm::textarea($this->$widget, $label, $options);
	}
	
	public function wysiwyg($widget, $options=array()) {
		$label = $this->prepareLabel($widget, $options);
		
		AdminForm::wysiwyg($this->$widget, $label, $options);
	}
	
	public function checkbox($widget, $options=array()) {
		$label = $this->prepareLabel($widget, $options);
		
		AdminForm::checkbox($this->$widget, $label, $options);
	}

	public function end($submits=null) {
		echo '													
					<p>
					<input name="stay" type="submit" class="submit long" value="Envoyer"> 
					</p>';
		parent::end();
		
		return $this;
	}

	public function h3($title) {
		echo '<h3>'.$title.'</h3>';
		
		return $this;
	}

	public function h4($title) {
		echo '<h4>'.$title.'</h4>';
		
		return $this;
	}
}