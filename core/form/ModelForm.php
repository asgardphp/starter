<?php
class ModelForm extends Form {
	protected $model;

	function __construct(
		$model, 
		$params=array('action' => '', 'method' => 'post')
	) {
		$this->model = $model;
	
		$widgets = array();
		foreach($model->getProperties() as $name=>$properties) {
			if(isset($params['only']))
				if(!in_array($name, $params['only']))
					continue;
		
			$widget_params = array();

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
		
		$modelName = $model->getModelName();
		
		foreach($modelName::$files as $name=>$file) {
			if(isset($params['only']))
				if(!in_array($name, $params['only']))
					continue;
					
			$widget_params = array('type'=>'file');
			//~ $widget_params = array('type'=>'text');
			$widgets[$name] = new Widget($widget_params);
		}
		
		foreach($modelName::$relationships as $name=>$relation) {
			if(isset($params['only']))
				if(!in_array($name, $params['only']))
					continue;
					
			$property_name = $name.'_id';
			#todo why using _id instead of name?!
			
			$ids = array();
			foreach($relation['model']::find() as $v)
				$ids[$v->id] = $v;
					
			if($relation['type'] == 'hasOne' || $relation['type'] == 'belongsTo') {
				$widget_params = array(
					'type'	=>	'integer',
					'choices'		=>	$ids,
					'default'	=>	$model->$property_name,
				);
				$widgets[$property_name] = new Widget($widget_params);
			}
			#todo hasMany?
			elseif($relation['type'] == 'HMABT') {
				$defaults = array();
				foreach($this->model->getRelation($name) as $r)
					$defaults[] = $r->id;
				$widget_params = array(
					//~ 'type'	=>	'integer',
					'choices'		=>	$ids,
					'default'	=>	$defaults,
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
	
	public function getModel() {
		return $this->model;
	}
	
	public function my_errors() {
		$data = $this->getData();
		$res = $this->callback('pre_set', array($data));
		if($res)
			$data = $res;
		
		$this->model->set($data);
		$this->model->setFiles($this->files);
		$this->model->pre_save();
		
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
			
		if(is_a($group, 'ModelForm') || is_subclass_of($group, 'ModelForm'))
			$group->model->_save();
			
		if(is_subclass_of($group, 'AbstractGroup'))
			foreach($group->widgets as $name=>$widget)
				if(is_subclass_of($widget, 'AbstractGroup'))
					$this->_save($widget);
	}
}