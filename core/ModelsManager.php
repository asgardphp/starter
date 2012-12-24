<?php
namespace Coxis\Core;

class ModelDefinition extends Hookable {
	protected $modelClass;

	public $meta = array();
	public $properties = array();
	public $behaviors = array();
	public $relations = array();
	public $messages = array();

	function __construct($modelClass) {
		$reflectionClass = new \ReflectionClass($modelClass);
		if(!$reflectionClass->IsInstantiable())
			return;

		$this->modelClass = $modelClass;

		$this->relations = isset($modelClass::$relations) ? $modelClass::$relations:array();
		$this->meta = isset($modelClass::$meta) ? $modelClass::$meta:array();
		$this->messages = isset($modelClass::$messages) ? $modelClass::$messages:array();

		if(isset($modelClass::$behaviors))
			foreach($modelClass::$behaviors as $name=>$params) {
				if(is_int($name)) {
					$name = $params;
					$params = true;
				}
				$this->behaviors[$name] = $params;
			}

		$properties = $modelClass::$properties;
		$clone = $properties;
		foreach($clone as $k=>$v) {
			if(is_int($k)) {
				$properties = 
					\Coxis\Core\Tools\Tools::array_before($properties, $k) +
					array($v => array()) +
					\Coxis\Core\Tools\Tools::array_after($properties, $k);
			}
		}
		foreach($properties as $k=>$params)
			$this->addProperty($k, $params);

		\Hook::trigger('behaviors_pre_load', $this);

		foreach($this->behaviors as $behavior => $params) {
			if($params) {
				\Hook::trigger('behaviors_load_'.$behavior, $this);
			}
		}

		$modelClass::configure($this);
	}

	public function __call($name, $arguments) {
		$chain = new HookChain();
		$chain->found = false;
		$res = $this->triggerChain($chain, 'callStatic', array($name, $arguments));
		if(!$chain->found)
			throw new \Exception('Static method '.$name.' does not exist for model '.$this->getClass());

		return $res;
	}

	public function getClass() {
		return $this->modelClass;
	}

	public function addProperty($property, $params) {
		if(is_string($params))
				$params = array('type'=>$params);
		foreach($params as $k=>$v)
			if(is_int($k)) {
				unset($params[$k]);
				$params[$v] = true;
			}
		if(!isset($params['required']))
			$params['required'] = true;
		#todo multiple values - not atomic.. ?
		if(!isset($params['type']) || !$params['type']) {
			if(isset($params['multiple']) && $params['multiple'])
				$params['type'] = 'array';
			else
				$params['type'] = 'text';
		}

		$propertyClass = $this->trigger('propertyClass', array($params['type']), function($chain, $type) {
			return '\Coxis\Core\Properties\\'.ucfirst($type).'Property';
		});

		$this->properties[$property] = new $propertyClass($this->modelClass, $property, $params);
		uasort($this->properties, function($a, $b) {
			if($a->position===null)
				return 1;
			if($b->position===null)
				return -1;
			return $a->position > $b->position;
		});
	}

	public function hasProperty($name) {
		return isset($this->properties[$name]);
	}

	public function property($name) {
		return $this->properties[$name];
	}

	public function properties() {
		return $this->properties;
	}

	public function messages() {
		return $this->messages;
	}

	public function behaviors() {
		return $this->behaviors;
	}

	public function relations() {
		return $this->relations;
	}

	public function isI18N() {
		foreach($this->properties as $prop)
			if($prop->i18n)
				return true;
		return false;
	}
	
	public static function getModelName() {
		return ${$this->getClass()}::getModelName();
	}
}

class ModelsManager {
	protected $models = array();

	public function get($modelClass) {
		if(!isset($this->models[$modelClass]))
			$this->models[$modelClass] = new ModelDefinition($modelClass);
		return $this->models[$modelClass];
	}
}