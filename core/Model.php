<?php
namespace Coxis\Core;

class ModelException extends \Exception {
	public $errors = array();
}

class Model {
	#public for behaviors
	public $data = array(
		'properties'	=>	array(),
	);

	public function __construct($param='') {
		$chain = new HookChain();
		$chain->found = false;
		$this->triggerChain($chain, 'construct', array($this, $param));
		if(!$chain->found) {
			if(is_array($param))
				$this->loadDefault()->set($param);
			else
				$this->loadDefault();
		}
	}
	
	/* MAGIC METHODS */
	public function __set($name, $value) {
		$this->set($name, $value);
	}

	public function __get($name) {
		return $this->get($name);
	}
	
	public function __isset($name) {
		return isset($this->data['properties'][$name]);
	}
	
	public function __unset($name) {
		unset($this->data['properties'][$name]);
	}

	public static function __callStatic($name, $arguments) {
		// if(preg_match('/^_/', $name)) {
		// 	$name = preg_replace('/^_/', '', $name);
		// 	$inst = Context::instance()->get(get_called_class());
		// 	return call_user_func_array(array($inst, $name), $arguments);
		// }

		$chain = new HookChain();
		$chain->found = false;
		$res = static::triggerChain($chain, 'callStatic', array($name, $arguments));
		if(!$chain->found)
			throw new \Exception('Static method '.$name.' does not exist for model '.static::getModelName());

		return $res;
	}

	public function __call($name, $arguments) {
		$chain = new HookChain;
		$chain->found = false;
		$res = static::triggerChain($chain, 'call', array($this, $name, $arguments));
		if(!$chain->found) {
			try {
				return static::__callStatic($name, $arguments);
			} catch(\Exception $e) {
				throw new \Exception('Method '.$name.' does not exist for model '.static::getModelName());
			}
		}

		return $res;
	}
	
	/* INIT AND MODEL CONFIGURATION */
	public static function _autoload() {
		if(get_called_class() == 'Coxis\Core\Model')
			return;

		\Hook::trigger('behaviors_pre_load', get_called_class());

		$properties = static::$properties;
		foreach($properties as $k=>$v) {
			if(is_int($k)) {
				static::$properties = 
					\Coxis\Core\Tools\Tools::array_before(static::$properties, $k) +
					array($v => array()) +
					\Coxis\Core\Tools\Tools::array_after(static::$properties, $k);
			}
			elseif(is_string($v))
					static::$properties[$k] = array('type'=>$v);
		}
		foreach(static::$properties as $k=>$params)
			static::addProperty($k, $params);
		
		foreach(static::$behaviors as $behavior => $params)
			if($params)
				\Hook::trigger('behaviors_load_'.$behavior, get_called_class());

		static::configure();
	}
	
	protected static function configure() {}

	public function loadDefault() {
		foreach(static::properties() as $name=>$property)
			$this->$name = $property->getDefault();
				
		return $this;
	}
	
	/* PROPERTIES */
	public static function hasProperty($name) {
		return isset(static::$properties[$name]);
	}

	public static function addProperty($property, $params) {
		if(!isset($params['required']))
			$params['required'] = true;
		#todo multiple values - not atomic.. ?
		if(!isset($params['type'])) {
			if(isset($params['multiple']) && $params['multiple'])
				$params['type'] = 'array';
			else
				$params['type'] = 'text';
		}

		$propertyClass = static::trigger('propertyClass', array($params['type']), function($chain, $type) {
			return '\Coxis\Core\Properties\\'.ucfirst($type).'Property';
		});

		static::$properties[$property] = new $propertyClass(get_called_class(), $property, $params);
	}
	
	public static function property($name) {
		return static::$properties[$name];
	}
	
	public static function properties() {
		return static::$properties;
	}

	/* PERSISTENCY */
	public function save($values=null, $force=false) {
		#set $values if any
		if($values)
			$this->set($values);
		
		if(!$force) {
			#validate params and files
			if($errors = $this->errors()) {
				$msg = implode("\n", \Coxis\Core\Tools\Tools::flateArray($errors));
				$e = new ModelException($msg);
				$e->errors = $errors;
				throw $e;
			}
		}
		
		$chain = new HookChain;
		$this->triggerChain($chain, 'save', array($this));
		if(!$chain->executed)
			throw new \Exception('Cannot save non-persistent models');

		return $this;
	}
	
	public function destroy() {
		$chain = new HookChain;
		$this->triggerChain($chain, 'destroy', array($this));
		if(!$chain->executed)
			throw new \Exception('Cannot destroy non-persistent models');
	}

	public static function create($values=array()) {
		$m = new static($values);
		return $m->save();
	}
	
	/* VALIDATION */
	public function getValidator() {
		$constrains = array();
		$model = $this;
		$this->trigger('constrains', array(&$constrains), function($chain, &$constrains) use($model) {
			foreach($model::$properties as $name=>$property)
				$constrains[$name] = $property->getRules();
		});
		
		if(isset(static::$messages))
			$messages = static::$messages;
		else
			$messages = array();
		
		$validator = new Validator($constrains, $messages);
		$validator->model = $this;

		return $validator;
	}
	
	public function valid() {
		return !$this->errors();
	}
	
	public function errors() {
		#before validation
		#todo use model hooks
		foreach(static::$behaviors as $behavior => $params)
			if($params)
				\Hook::trigger('behaviors_presave_'.$behavior, $this);
				
		$data = $this->toArray();

		$errors = null;
		// $model = $this;
		$this->trigger('validation', array($this, &$data, &$errors), function($chain, $model, &$data, &$errors) {
			$errors = $model->getValidator()->errors($data);
		});
		
		return $errors;
	}

	/* MISC */
	public static function isI18N() {
		foreach(static::$properties as $prop)
			if($prop->i18n)
				return true;
		return false;
	}
	
	public static function getModelName() {
		return \Coxis\Core\Tools\Tools::classBasename(strtolower(get_called_class()));
	}
	
	public function set($name, $value=null, $lang=null) {
		if(is_array($name)) {
			$vars = $name;
			foreach($vars as $k=>$v)
				$this->$k = $v;
		}
		else {
			if(static::hasProperty($name)) {
				if(static::property($name)->setHook) {
					$hook = static::property($name)->setHook;
					$value = call_user_func_array($hook, array($value));
				}

				if(static::property($name)->i18n) {
					if(!$lang)
						$lang = \Config::get('locale');
					if($lang == 'all')
						foreach($value as $one => $v)
							$this->data['properties'][$name][$one] = static::property($name)->set($v);
					else
						$this->data['properties'][$name][$lang] = static::property($name)->set($value);
				}
				else
					$this->data['properties'][$name] = static::property($name)->set($value);
			}
			elseif(isset(static::$meta['hooks']['set'][$name])) {
				$hook = static::$meta['hooks']['set'][$name];
				$hook($this, $value);
			}
			else
				$this->data[$name] = $value;
		}
				
		return $this;
	}

	public function raw($name, $lang=null) {
		$res = $this->get($name, $lang, true);
		return $res;
	}
	
	public function get($name, $lang=null, $raw=false) {
		if(!$lang)
			$lang = \Config::get('locale');

		#todo go for data[$name] only if orm fetch failed
		// $res = static::trigger('get', array($this, $name, $lang), function($chain, $model, $name, $lang) {
		$res = static::trigger('get', array($this, $name, $lang), function($chain, $model, $name, $lang) {
			if($model::hasProperty($name)) {
				if($model::property($name)->i18n) {
					if($lang == 'all') {
						$langs = \Config::get('locales');
						$res = array();
						foreach($langs as $lang)
							$res[$lang] = $model->get($name, $lang);
						return $res;
					}
					elseif(isset($model->data['properties'][$name][$lang]))
						return $model->data['properties'][$name][$lang];
				}
				elseif(isset($model->data['properties'][$name])) 
					return $model->data['properties'][$name];
			}
			elseif(isset($model->data[$name]))
				return $model->data[$name];
		});

		#todo into a hook
		if(is_string($res) && !$raw && \Memory::get('in_view'))
			return HTML::sanitize($res);

		return $res;
	}
	
	public function toArray() {
		$attrs = $this->propertyNames();
		$vars = array();
		
		foreach($attrs as $attr) {
			if(!isset($this->data['properties'][$attr]))
				$vars[$attr] = null;
			else
				$vars[$attr] = $this->data['properties'][$attr];
		}
		
		return $vars;
	}
	
	public static function propertyNames() {
		return array_keys(static::$properties);
	}

	/* HOOKS */
	#cannot use references with get_func_args
	protected static function trigger($name, $args=array(), $cb=null, &$filter1=null, &$filter2=null, &$filter3=null, &$filter4=null, &$filter5=null, &$filter6=null,
		&$filter7=null, &$filter8=null, &$filter9=null, &$filter10=null) {
		return \Hook::trigger(array('models', get_called_class(), $name), $args, $cb, $filter1, $filter2, $filter3, $filter4, $filter5, $filter6, $filter7,
			$filter8, $filter9, $filter10);
	}

	#cannot use references with get_func_args
	protected static function triggerChain($chain, $name, $args=array(), $cb=null, &$filter1=null, &$filter2=null, &$filter3=null, &$filter4=null, &$filter5=null, &$filter6=null,
		&$filter7=null, &$filter8=null, &$filter9=null, &$filter10=null) {
		return \Hook::triggerChain($chain, array('models', get_called_class(), $name), $args, $cb, $filter1, $filter2, $filter3, $filter4, $filter5, $filter6, $filter7,
			$filter8, $filter9, $filter10);
	}

	public static function hook() {
		return call_user_func_array(array('Hook', 'hook'), func_get_args());
	}

	public static function hookOn($hookName, $cb) {
		$args = array(array_merge(array('models', get_called_class()), array($hookName)), $cb);
		return call_user_func_array(array('Hook', 'hookOn'), $args);
	}

	public static function hookBefore($hookName, $cb) {
		$args = array(array_merge(array('models', get_called_class()), array($hookName)), $cb);
		return call_user_func_array(array('Hook', 'hookBefore'), $args);
	}

	public static function hookAfter($hookName, $cb) {
		$args = array(array_merge(array('models', get_called_class()), array($hookName)), $cb);
		return call_user_func_array(array('Hook', 'hookBefore'), $args);
	}
}
