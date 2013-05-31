<?php
namespace Coxis\App\Admin\Libs\Form;

class AdminSimpleForm extends Form {
	function __construct($controller, $params=array()) {
		parent::__construct($params);
		$this->controller = $controller;

		$this->setRenderCallback('text', function($field, $options) {
			$options['attrs']['class'] = 'text big';
			return HTMLWidget::text($field->getName(), $field->getValue(), $options);
		});

		$this->setRenderCallback('textarea', function($field, $options) {
			$options['attrs']['class'] = 'text big';
			return HTMLWidget::textarea($field->getName(), $field->getValue(), $options);
		});

		$this->setRenderCallback('password', function($field, $options) {
			$options['attrs']['class'] = 'text big';
			return HTMLWidget::password($field->getName(), $field->getValue(), $options);
		});

		$this->setRenderCallback('select', function($field, $options) {
			$options['attrs']['class'] = 'styled';
			return HTMLWidget::select($field->getName(), $field->getValue(), $options);
		});

		$this->setRenderCallback('date', function($field, $options) {
			$options['attrs']['class'] = 'text date_picker text big';
			return HTMLWidget::text($field->getName(), $field->getValue(), $options);
		});

		$this->setRenderCallback('\Coxis\Form\Widgets\File', function($field, $options) {
			return new \Coxis\App\Admin\Libs\Form\Widgets\FileWidget($field->getName(), $field->getValue(), $options);
		});

		$this->hook('render', function($hookchain, $form, $field, $widget, $options) {
			if($field instanceof \Coxis\Form\Fields\HiddenField)
				return $widget;
			if($field instanceof \Coxis\Form\Fields\MultipleFileField)
				return $widget->render();
			$label = $field->label();
			if(isset($options['label']))
				$label = $options['label'];

			// if($form->getModel()->hasProperty($field->name) && $form->getModel()->property($field->name)->required
			// 	|| get($form->getModel()->getDefinition()->relations, array($field->name, 'required')))
			// 	$label .= '*';
			#todo required field
			$str = '<p>
				<label for="'.$options['id'].'">'.$label.'</label>';
			if($error=$field->getError())
				$str .= '<span class="error">'.$error.'</span>';
			if(isset($options['note']))
				$str .= '<span class="note">'.$options['note'].'</span>';
			$str .= $widget->render().'
			</p>';

			return $str;
		});
	}

	public function showErrors() {
		if(!$this->errors)
			return;
		$error_found = false;
		foreach($this->errors as $field_name=>$errors) {
			if(!$this->has($field_name) || is_subclass_of($this->$field_name, 'Coxis\Form\Fields\HiddenField')) {
				if(!$error_found) {
					echo '<div class="message errormsg">';
					$error_found = true;
				}
				if(is_array($errors)) {
					foreach(Tools::flateArray($errors) as $error)
						echo '<p>'.$error.'</p>';
				}
				else
					echo '<p>'.$errors.'</p>';
			}
		}
		if($error_found)
			echo '</div>';
	}

	public function h3($title) {
		return '<h3>'.$title.'</h3>';
	}

	public function h4($title) {
		return '<h3>'.$title.'</h3>';
	}


	public function close($submits=null) {
		echo '<hr/>';
		if($submits === null)
			echo '<p>
				'.HTMLWidget::submit('stay', __('Save'), array('attrs'=>array('class'=>'submit long')))->render().'
				'.HTMLWidget::submit('send', __('Save & Leave'), array('attrs'=>array('class'=>'submit long')))->render().'
			</p>';
		else
			echo $submits;
		parent::close();
		return $this;
	}
}