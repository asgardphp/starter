<?php
namespace Coxis\Core;

class ModelException extends \Exception {
	public $errors = array();
}

abstract class Model {
	#public for behaviors
	public $data = array(
		'properties'	=>	array(),
	);

	public function __construct($param='') {
		$this->_is_loaded = false;
		$this->triggerOn('construct', array($this, $param));
		if(!$this->_is_loaded) {
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
		$res = static::triggerOn('__get', array($this, $name), true);
		if($res !== null)
			return $res;
		elseif(static::hasProperty($name))
			return $this->get($name);
		elseif(isset(static::$meta['hooks']['get'][$name])) {
			$hook = static::$meta['hooks']['get'][$name];
			return $hook($this);
		}
		elseif(isset($this->data[$name]))
			return $this->data[$name];
	}
	
	public function __isset($name) {
		return isset($this->data['properties'][$name]);
	}
	
	public function __unset($name) {
		unset($this->data['properties'][$name]);
	}

	public static function __callStatic($name, $arguments) {
		if(isset(static::$meta['hooks']['callstatic'][$name]))
			return call_user_func_array(static::$meta['hooks']['callstatic'][$name], $arguments);
		else {
			$res = static::triggerOn('__callStatic', array($name, $arguments), true);
			if($res !== null)
				return $res;
		}

		throw new \Exception('Static method '.$name.' does not exist for model '.static::getModelName());
	}

	public function __call($name, $arguments) {
		if(isset(static::$meta['hooks']['call'][$name])) 
			return call_user_func_array(static::$meta['hooks']['call'][$name], array_merge(array($this), $arguments));
		elseif(isset(static::$meta['hooks']['callstatic'][$name]))
			return call_user_func_array(static::$meta['hooks']['callstatic'][$name], array_merge(array($this), $arguments));
		else {
			$res = static::triggerOn('__call', array($this, $name, $arguments), true);
			if($res !== null)
				return $res;
			$res = static::triggerOn('__callStatic', array($name, $arguments), true);
			if($res !== null)
				return $res;
		}

		throw new \Exception('Method '.$name.' does not exist for model '.static::getModelName());
	}
	
	/* INIT AND MODEL CONFIGURATION */
	public static function _autoload() {
		if(static::getClassName() == 'Coxis\Core\Model')
			return;
		static::loadModel();
		static::configure();
	}
	
	protected static function configure() {}

	public function loadDefault() {
		foreach(static::properties() as $name=>$property)
			$this->$name = $property->getDefault();
				
		return $this;
	}
	
	public static function loadModel() {
		$properties = static::$properties;
		foreach($properties as $k=>$v)
			if(is_int($k)) {
				static::$properties = 
					Tools::array_before(static::$properties, $k) +
					array($v => array()) +
					Tools::array_after(static::$properties, $k);
			}
		foreach(static::$properties as $k=>$params)
			static::addProperty($k, $params);

		static::loadBehaviors();
	}
	
	public static function loadBehaviors() {
		Event::trigger('behaviors_pre_load', static::getClassName());
		
		foreach(static::$behaviors as $behavior => $params)
			if($params)
				Event::trigger('behaviors_load_'.$behavior, static::getClassName());
	}
	
	/* PROPERTIES */
	public static function hasProperty($name) {
		return isset(static::$properties[$name]);
	}
	
	public static function addProperty($property, $params) {
		if(!isset($params['required']))
			$params['required'] = true;
		#todo multiple values - not atomic.. ?
		// if(isset($params['multiple']) && $params['multiple'])
		// 	$params[$property]['type'] = 'array';
		if(!isset($params['type']))
			$params['type'] = 'text';

		$propertyClass = $params['type'].'Property';
		#todo full class namespace

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
				$msg = implode('<br>'."\n", $errors);
				$e = new ModelException($msg);
				$e->errors = $errors;
				throw $e;
			}
		}
		
		if(!$this->trigger('save', null, array($this)))
			throw new \Exception('Cannot save non-persistent models');

		return $this;
	}
	
	public function destroy() {
		if(!$this->trigger('destroy', array($this)))
			throw new \Exception('Cannot destroy non-persistent models');
	}

	public static function create($values=array()) {
		$m = new static($values);
		return $m->save();
	}
	
	/* VALIDATION */
	public function getValidator() {
		$constrains = array();
		foreach(static::$properties as $name=>$property)
			$constrains[$name] = $property->getRules();
		
		if(isset(static::$messages))
			$messages = static::$messages;
		else
			$messages = array();
		
		$validator = new Validator($constrains, $messages);

		return $validator;
	}
	
	public function isValid() {
		return !$this->errors();
	}
	
	public function errors() {
		#before validation
		#todo use model hooks
		foreach(static::$behaviors as $behavior => $params)
			if($params)
				Event::trigger('behaviors_presave_'.$behavior, $this);
				
		$data = $this->toArray();

		#validation
		$errors = $this->getValidator()->errors($data);
		
		#after validation
		
		
		return $errors;
	}

	/* MISC */
	public static function isI18N() {
		foreach(static::$properties as $prop)
			if($prop->i18n)
				return true;
		return false;
	}
	
	#todo remove
	public static function getClassName() {
		return get_called_class();
	}
	
	public static function getModelName() {
		return basename(strtolower(static::getClassName()));
	}
	
	public function get($name, $arg1=null, $arg2=null) {
		$lang = null;
		$raw = 0;	#0 auto; 1 raw; 2 sanitize
		if(is_int($arg1))
			$raw = $arg1;
		else {
			$lang = $arg1;
			if(is_int($arg2))
				$raw = $arg2;
		}

		$res = null;
		if(static::property($name)->i18n) {
			if(!$lang)
				$lang = Config::get('locale');
			if($lang == 'all') {
				$langs = Config::get('locales');
				$res = array();
				foreach($langs as $lang)
					$res[$lang] = $this->get($name, $lang);
				return $res;
			}
			if(isset($this->data['properties'][$name][$lang]))
				$res = $this->data['properties'][$name][$lang];
		}
		elseif(isset($this->data['properties'][$name])) 
			$res = $this->data['properties'][$name];

		$res = static::triggerOn('get', array($this, $name, $lang, $res), true);
		
		#todo put this into a behavior, with filter()
		if(is_string($res)) {
			if($raw === 2)
				return HTML::sanitize($res);
			elseif($raw == 0 && Coxis::get('in_view'))
				return HTML::sanitize($res);
		}
		
		return $res;
	}
	
	public function set($name, $value=null, $lang=null) {
		if(is_array($name)) {
			$vars = $name;
			foreach($vars as $k=>$v)
				$this->$k = $v;
		}
		else {
			if(static::hasProperty($name)) {
				if(static::property($name)->setFilter) {
					$filter = static::property($name)->setFilter;
					$value = call_user_func_array($filter, array($value));
				}

				if(static::property($name)->i18n) {
					if(!$lang)
						$lang = Config::get('locale');
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
		if($lang)
			return $this->get($name, $lang, 1);
		else
			return $this->get($name, 1);
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

	public static function triggerBefore($what, $args=array(), $return=false) {
		if(isset(static::$meta['hooks']['before'][$what])) {
			foreach(static::$meta['hooks']['before'][$what] as $hook) {
				$res = call_user_func_array($hook, $args);
				if($return && $res)
					return $res;
			}
			if(!$return)
				return true;
		}
	}

	public static function triggerOn($what, $args=array(), $return=false) {
		if(isset(static::$meta['hooks']['on'][$what])) {
			foreach(static::$meta['hooks']['on'][$what] as $hook) {
				$res = call_user_func_array($hook, $args);
				#return asked for so return it
				if($return && $res)
					return $res;
			}
			#at least one hook was executed, and no result asked for, so return true
			if(!$return)
				return true;
		}
	}

	public static function triggerAfter($what, $args=array(), $return=false) {
		if(isset(static::$meta['hooks']['after'][$what])) {
			foreach(static::$meta['hooks']['after'][$what] as $hook) {
				$res = call_user_func_array($hook, $args);
				if($return && $res)
					return $res;
			}
			if(!$return)
				return true;
		}
	}

	protected static function trigger($name, $cb=null, $args=array()) {
		$res = static::triggerBefore($name, $args);
		if($cb)
			call_user_func_array($cb, $args);
		$res |= static::triggerOn($name, $args);
		$res |= static::triggerAfter($name, $args);
		return $res;
	}

	public static function hookGet($what, $cb) {
		static::$meta['hooks']['get'][$what] = $cb;
	}

	public static function hookSet($what, $cb) {
		static::$meta['hooks']['set'][$what] = $cb;
	}

	public static function hookCall($what, $cb) {
		static::$meta['hooks']['call'][$what] = $cb;
	}

	public static function hookCallStatic($what, $cb) {
		static::$meta['hooks']['callstatic'][$what] = $cb;
	}

	public static function before($what, $cb) {
		static::$meta['hooks']['before'][$what][] = $cb;
	}

	public static function on($what, $cb) {
		static::$meta['hooks']['on'][$what][] = $cb;
	}

	public static function after($what, $cb) {
		static::$meta['hooks']['after'][$what][] = $cb;
	}
}