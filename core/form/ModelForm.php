<?php
namespace Coxis\Core\Form;

class ModelForm extends Form {
	protected $model;

	function __construct(
		$model, 
		$params=array('action' => '', 'method' => 'post')
	) {
		$this->model = $model;
	
		$widgets = array();
		foreach($model->properties() as $name=>$properties) {
			if(isset($params['only']))
				if(!in_array($name, $params['only']))
					continue;
			if(isset($params['except']))
				if(in_array($name, $params['except']))
					continue;
		
			$widget_params = array();

			if($properties->editable === false)
				continue;
			if($properties->form_hidden)
				$widget_params['default'] = '';
			elseif($model->isOld())
				$widget_params['default'] = $model->$name;
			if($properties->type == 'boolean')
				$widget_params['type'] = 'boolean';
			elseif($properties->type == 'file')
				$widget_params['type'] = 'file';
			if($properties->in)
				foreach($properties->in as $v)
					$widget_params['choices'][$v] = $v;
			if($properties->multiple)
				$widget_params['multiple'] = true;

			$widgets[$name] = new Widget($widget_params);
		}
		
		foreach($model::getDefinition()->relations() as $name=>$relation) {
			if(isset($params['only']))
				if(!in_array($name, $params['only']))
					continue;
			if(isset($params['except']))
				if(in_array($name, $params['except']))
					continue;
		
			$property_name = $name;
			#todo why using _id instead of name?!
			
			$ids = array();
			foreach($relation['model']::all() as $v)
				$ids[$v->id] = (string)$v;
					
			if($relation['type'] == 'hasOne' || $relation['type'] == 'belongsTo') {
				$widget_params = array(
					'type'	=>	'integer',
					'choices'		=>	$ids,
					'default'	=>	(isset($model->$property_name->id) ? $model->$property_name->id:null),
				);
				$widgets[$property_name] = new Widget($widget_params);
			}
			elseif($relation['type'] == 'HMABT' || $relation['type'] == 'hasMany') {
				$defaults = array();
				foreach($this->model->$name as $r)
					$defaults[] = $r->id;
				$widget_params = array(
					'choices'		=>	$ids,
					'default'	=>	$defaults,
				);
				$widgets[$property_name] = new Widget($widget_params);
			}
		}

		parent::__construct(
			$model->getModelName(),
			$params,
			$widgets
		);
	}
	
	public function errors($widget=null) {
		if(!$widget)
			$widget = $this;
			
		$errors = array();
		
		if(is_subclass_of($widget, 'Coxis\Core\Form\AbstractGroup')) {
			if($widget instanceof \Coxis\Core\Form\ModelForm)
				$errors = $widget->my_errors();
			elseif($widget instanceof \Coxis\Core\Form\Form)
				$errors = $widget->errors();
				
			foreach($widget as $name=>$sub_widget) {
				if(is_subclass_of($sub_widget, 'Coxis\Core\Form\AbstractGroup')) {
					$widget_errors = $this->errors($sub_widget);
					if(sizeof($widget_errors) > 0)
						$errors[$sub_widget->name] = $widget_errors;
				}
			}
		}
		
		$this->errors = $errors;

		return $errors;
	}
	
	public function getModel() {
		return $this->model;
	}
	
	public function my_errors() {
		$data = $this->getData();
		$res = $this->callback('pre_test', array($data));
		if($res)
			$data = $res;			
		$data = array_filter($data, function($v) {
			return $v !== null;
		});
		foreach($data as $k=>$v)
			if(!$v && $this->model->property($k)->form_hidden)
				unset($data);
		$this->model->set($data);

		return array_merge(parent::my_errors(), $this->model->errors());
	}
	
	public function save() {
		if($errors = $this->errors()) {
			$e = new FormException;
			$e->errors = $errors;
			throw $e;
		}
	
		$this->callback('pre_save');
	
		return $this->_save();
	}
	
	public function _save($group=null) {
		if(!$group)
			$group = $this;
			
		if(is_a($group, 'Coxis\Core\Form\ModelForm') || is_subclass_of($group, 'Coxis\Core\Form\ModelForm'))
			$group->model->save();
			
		if(is_subclass_of($group, 'Coxis\Core\Form\AbstractGroup'))
			foreach($group->widgets as $name=>$widget)
				if(is_subclass_of($widget, 'Coxis\Core\Form\AbstractGroup'))
					$this->_save($widget);
	}
}