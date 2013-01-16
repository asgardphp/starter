<?php
namespace App\Admin\Libs\Form;

class AdminForm extends Form {
	public static function h3($title) {
		echo '<h3>'.$title.'</h3>';
	}

	public static function h4($title) {
		echo '<h4>'.$title.'</h4>';
	}
	
	public static function select($widget, $label, $options, $choices) {
		if(isset($widget->params['multiple']) && $widget->params['multiple']) {
			$options['multiple'] = true;
			$options['attrs']['style'] = 'height:200px';
		}
			
		if(!isset($options['class']))
			$options['class'] = 'styled';
		
		echo '<p>';
		$widget->label($label);
		if($widget->getError())
			echo '<span style="color:#f00">'.$widget->getError().'</span>';
		echo '<br>';
		if(isset($options['note'])) {
			echo '<span style="font-size:9px; font-weight:normal">'.$options['note'].'</span>';
			echo '<br>';
		}
		$widget->select($options, $choices);
		echo '</p>';
	}
	
	public static function input($widget, $label, $options=array()) {
		if(!isset($options['class']))
			$options['class'] = 'text big';
		else
			$options['class'] .= ' text big';

		echo '<p>';
		$widget->label($label);
		if($widget->getError())
			echo '<span style="color:#f00">'.$widget->getError().'</span>';
		echo '<br>';
		if(isset($options['note'])) {
			echo '<span style="font-size:9px; font-weight:normal">'.$options['note'].'</span>';
			echo '<br>';
		}
		$widget->input($options);
		if(isset($options['nb']))
			echo '<span>'.$options['nb'].'</span>';
		echo '</p>';
	}
	
	public static function password($widget, $label, $options=array()) {
		if(!isset($options['class']))
			$options['class'] = 'text big';
		else
			$options['class'] .= ' text big';
			
		echo '<p>';
		$this->$widget->label($label);
		if($widget->getError())
			echo '<span style="color:#f00">'.$widget->getError().'</span>';
		echo '<br>';
		if(isset($options['note'])) {
			echo '<span style="font-size:9px; font-weight:normal">'.$options['note'].'</span>';
			echo '<br>';
		}
		$this->$widget->password($options);
		if(isset($options['nb']))
			echo '<span>'.$options['nb'].'</span>';
		echo '</p>';
	}
	
	public static function file($widget, $label, $options=array()) {
		echo '<p>';
		$widget->label($label);
		if($widget->getError())
			echo '<span style="color:#f00">'.$widget->getError().'</span>';
		echo '<br>';
		if(isset($options['note'])) {
			echo '<span style="font-size:9px; font-weight:normal">'.$options['note'].'</span>';
			echo '<br>';
		}
		$widget->file(array(
			'class'=>'text big',
		));
		echo '</p>';
	
		if(isset($options['nb']))
			echo '<span>'.$options['nb'].'</span>';
	}
	
	public static function textarea($widget, $label, $options=array()) {	
		echo '<p>';
		$widget->label($label);
		if($widget->getError())
			echo '<span style="color:#f00">'.$widget->getError().'</span>';
		echo '<br>';
		if(isset($options['note'])) {
			echo '<span style="font-size:9px; font-weight:normal">'.$options['note'].'</span>';
			echo '<br>';
		}
		$widget->textarea(array(
			'class'=>'text big',
		));
		
		if(isset($options['nb']))
			echo '<span>'.$options['nb'].'</span>';

		echo '</p>';
	}
	
	public static function wysiwyg($widget, $label, $options=array()) {
		$options = array_merge(
			$options,
			array(
				'attrs'	=>	array(
					'rows'	=>	10,
					'cols'	=>	80,
				),
			)
		);
			
		echo '<p>';
		$widget->label($label);
		if($widget->getError())
			echo '<span style="color:#f00">'.$widget->getError().'</span>';
		echo '<br>';
		if(isset($options['note'])) {
			echo '<span style="font-size:9px; font-weight:normal">'.$options['note'].'</span>';
			echo '<br>';
		}
		$widget->wysiwyg($options);
		echo '</p>';
	}
	
	public static function checkbox($widget, $label, $options=array()) {
		echo '<p>';
		$widget->label($label);
		$widget->checkbox($options);

		if($widget->getError())
			echo '<br/><span style="color:#f00">'.$widget->getError().'</span>';

		if(isset($options['note'])) {
			echo '<br>';
			echo '<span style="font-size:9px; font-weight:normal">'.$options['note'].'</span>';
		}
		echo '</p>';
	}
}