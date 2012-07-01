<?php
class ModelForm extends Form {
	protected $model;

	function __construct(
		$model, 
		$params=array('action' => '', 'method' => 'post')
	) {
		$this->model = $model;
	
	//~ d($model->getProperties());
	
		$widgets = array();
		foreach($model->getProperties() as $name=>$properties) {
			$widget_params = array();
			//~ if($name=='filename_logo')
				//~ d($properties, $model->getProperties());
			if(isset($properties['editable']) && !$properties['editable'])
				continue;
			if(!$model->isNew())
				$widget_params['default'] = $model->raw($name);
			if($properties['type'] == 'boolean')
				$widget_params['type'] = 'boolean';
			if(isset($properties['in']))
				foreach($properties['in'] as $v)
					$widget_params['choices'][$v] = $v;
				//~ $widget_params['choices'] = $properties['in'];
			if(isset($properties['multiple']) && $properties['multiple'])
				$widget_params['multiple'] = true;
				
			$widgets[$name] = new Widget($widget_params);
		}
		
		//~ echo ModelDecorator::$files;
		//~ $model::getStatic('files');
		//~ echo ModelDecorator::$files;
		$modelName = $model->getModelName();
		
		foreach($modelName::$files as $name=>$file) {
			$widget_params = array('type'=>'file');
			//~ $widget_params = array('type'=>'text');
			$widgets[$name] = new Widget($widget_params);
		}
		
		foreach($modelName::$relationships as $name=>$relation) {
			$property_name = $name.'_id';
			
			$ids = array();
			foreach($relation['model']::find() as $v)
				$ids[] = $v->id;
					
			if($relation['type'] == 'hasOne' || $relation['type'] == 'belongsTo') {
				$widget_params = array(
					'type'	=>	'integer',
					'in'		=>	$ids,
					'default'	=>	$model->$property_name,
				);
				$widgets[$property_name] = new Widget($widget_params);
			}
			elseif($relation['type'] == 'HMABT' || $relation['type'] == 'hasMany') {
				$widget_params = array(
					//~ 'type'	=>	'integer',
					'in'		=>	$ids,
					'default'	=>	array(2,3),//todo ...
				);
				$widgets[$property_name] = new Widget($widget_params);
			}
		}
		
		//~ d($widgets);
		
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
		
		if(is_subclass_of($widget, 'AbstractGroup')) {
			if(is_a($widget, 'ModelForm') || is_subclass_of($widget, 'ModelForm'))
				$errors = $widget->my_errors();
			elseif(is_a($widget, 'Form') || is_subclass_of($widget, 'Form'))
				$errors = $widget->errors();
				
			foreach($widget as $name=>$sub_widget) {
				if(is_subclass_of($sub_widget, 'AbstractGroup')) {
					$widget_errors = $this->errors($sub_widget);
					if(sizeof($widget_errors) > 0)
						$errors[$name] = $widget_errors;
				}
			}
		}
		
		return $errors;
	}
	
	public function my_errors() {
		$this->model->set($this->getData());
		$this->model->setFiles($this->files);
		$this->model->pre_save();
		
		return array_merge(parent::my_errors(), $this->model->errors());
	}
	
	public function save() {
		//~ d($this->style_musical);
		if($errors = $this->errors()) {
			$e = new FormException;
			$e->errors = $errors;
			throw $e;
		}
	
		return $this->_save();
	}
	
	public function _save($group=null) {
		if(!$group)
			$group = $this;
			
		if(is_a($group, 'ModelForm') || is_subclass_of($group, 'ModelForm'))
			$group->model->_save();
			
		if(is_subclass_of($group, 'AbstractGroup'))
			foreach($group->widgets as $name=>$widget)
				if(is_subclass_of($widget, 'AbstractGroup'))
					$widget->_save($widget);
	}
	
	public function getModel() {
		return $this->model;
	}
}