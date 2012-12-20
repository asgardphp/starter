<?php
namespace Coxis\Core\Form;

class ModelForm extends Form {
	protected $model;
	protected $i18n = false;

	public function getNewField($model, $name, $properties, $locale=null) {
		$field_params = array();

		$field_params['form'] = $this;

		if($properties->form_hidden)
			$field_params['default'] = '';
		elseif($model->isOld())
			$field_params['default'] = $model->get($name, $locale);

		$field_type = 'text';
		if($properties->type == 'boolean')
			$field_type = 'boolean';
		elseif($properties->type == 'file') {
			if($properties->multiple)
				$field_type = 'MultipleFile';
			else
				$field_type = 'file';
		}
		elseif($properties->type == 'date')
			$field_type = 'date';

		if($properties->in) {
			foreach($properties->in as $v)
				$field_params['choices'][$v] = $v;
			if($properties->multiple)
				$field_type = 'multipleselect';
			else
				$field_type = 'select';
		}

		$field_class = $field_type.'Field';

		return new $field_class($field_params);
	}

	public function addRelation($name) {
		$model = $this->model;
		$relation = ORMHandler::relationData($model, $name);

		$ids = array();
		foreach($relation['model']::all() as $v)
			$ids[$v->id] = (string)$v;
				
		if($relation['has'] == 'one') {
			$this->addField(new SelectField(array(
				'type'	=>	'integer',
				'choices'		=>	$ids,
				'default'	=>	(isset($model->$name->id) ? $model->$name->id:null),
			)), $name);
		}
		elseif($relation['has'] == 'many') {
			$this->addField(new MultipleSelectField(array(
				'type'	=>	'integer',
				'choices'		=>	$ids,
				'default'	=>	$this->model->$name()->ids(),
			)), $name);
		}
	}

	function __construct(
		$model, 
		$params=array()
	) {
		$this->model = $model;

		$this->i18n = isset($params['i18n']) && $params['i18n'];
	
		$fields = array();
		foreach($model->properties() as $name=>$properties) {
			if(isset($params['only']) && !in_array($name, $params['only']))
					continue;
			if(isset($params['except']) && in_array($name, $params['except']))
					continue;
			if($properties->editable === false)
				continue;

			if($this->i18n && $properties->i18n) {
				$i18ngroup = array();
				foreach(\Config::get('locales') as $locale)
					$i18ngroup[$locale] = $this->getNewField($model, $name, $properties, $locale);
				$fields[$name] = $i18ngroup;
			}
			else
				$fields[$name] = $this->getNewField($model, $name, $properties);
		}

		parent::__construct(
			isset($params['name']) ? $params['name']:$model->getModelName(),
			$params,
			$fields
		);
	}
	
	public function errors($field=null) {
		if(!$field)
			$field = $this;
			
		$errors = array();

		#check post_max_size
		if(\Server::get('CONTENT_LENGTH') > (int)ini_get('post_max_size')*1024*1024)
			$errors['_form'] = __('Data exceeds upload size limit. Maybe your file is too heavy.');

		if(!$this->isSent())
			return $errors;

		if(is_subclass_of($field, 'Coxis\Core\Form\AbstractGroup')) {
			if($field instanceof \Coxis\Core\Form\ModelForm)
				$errors = $field->my_errors();
			elseif($field instanceof \Coxis\Core\Form\Form)
				$errors = $field->errors();
				
			foreach($field as $name=>$sub_field) {
				if(is_subclass_of($sub_field, 'Coxis\Core\Form\AbstractGroup')) {
					$field_errors = $this->errors($sub_field);
					if(sizeof($field_errors) > 0)
						$errors[$sub_field->name] = $field_errors;
				}
			}
		}
		
		$this->setErrors($errors);
		$this->errors = $errors;

		return $errors;
	}
	
	public function getModel() {
		return $this->model;
	}
	
	public function my_errors() {
		$data = $this->getData();
		$data = array_filter($data, function($v) {
			return $v !== null;
		});
		foreach($data as $k=>$v)
			if(!$v && $this->model->hasProperty($k) && $this->model->property($k)->form_hidden)
				unset($data);
		if($this->i18n)
			$this->model->set($data, 'all');
		else
			$this->model->set($data);

		return array_merge(parent::my_errors(), $this->model->errors());
	}
	
	public function save() {
		if(!$this->isSent())
			return;

		if($errors = $this->errors()) {
			$e = new FormException;
			$e->errors = $errors;
			throw $e;
		}
	
		$this->trigger('pre_save');
	
		return $this->_save();
	}
	
	public function _save($group=null) {
		if(!$group)
			$group = $this;
			
		if(is_a($group, 'Coxis\Core\Form\ModelForm') || is_subclass_of($group, 'Coxis\Core\Form\ModelForm'))
			$group->model->save();

		if(is_subclass_of($group, 'Coxis\Core\Form\AbstractGroup'))
			foreach($group->fields as $name=>$field)
				if(is_subclass_of($field, 'Coxis\Core\Form\AbstractGroup'))
					$this->_save($field);
	}
}
