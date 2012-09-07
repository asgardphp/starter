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
		if(is_array($param))
			$this->loadDefault()
			       ->loadFromArray($param);
		elseif($param != '')
			$this->loadFromID($param);
		else
			$this->loadDefault();
	}
	
	/* MAGIC METHODS */
	public function __set($name, $value) {
		$this->setAttribute($name, $value);
	}
	
	public function __get($name) {
		if(static::hasProperty($name))
			return $this->getVar($name);
		elseif(array_key_exists($name, $this::$relationships)) {
			$res = $this->getRelation($name);
			if($res instanceof \Coxis\Core\Collection)
				return $res->get();
			else
				return $res;
		}
		elseif(isset(static::$meta['hooks']['get'][$name])) {
			$hook = static::$meta['hooks']['get'][$name];
			return $hook($this);
		}
	}
	
	public function __isset($name) {
		return isset($this->data['properties'][$name]);
	}
	
	public function __unset($name) {
		unset($this->data['properties'][$name]);
	}

	public function __call($name, $arguments) {
		//called when setting or getting a related model
		$todo = substr($name, 0, 3);
		$what = strtolower(substr($name, 3));
		
		if(isset(static::$meta['hooks']['call'][$name])) {
			$hook = static::$meta['hooks']['call'][$name];
			return call_user_func_array($hook, array_merge(array($this), $arguments));
		}
		elseif($todo=='set') {
			$value = $arguments[0];
			$lang = null;
			if(isset($arguments[1]))
				$lang = $arguments[1];
			return $this->setAttribute($what, $value, $lang);
		}
		elseif($todo=='get') {
			$lang = null;
			if(isset($arguments[0]))
				$lang = $arguments[0];
			return $this->getVar($what, $lang);
		}
		elseif(array_key_exists($name, $this::$relationships))
			return $this->getRelation($name);

		throw new \Exception('Method '.$name.' does not exist for model '.static::getModelName());
	}
	
	/* INIT AND MODEL CONFIGURATION */
	public static function _autoload() {
		if(static::getClassName() == 'coxis\core\model')
			return;
		// static::trigger('loadModel', array(get_called_class(), 'loadModel'), array(get_called_class()));
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
	
		$model_behaviors = static::$behaviors;
		
		foreach($model_behaviors as $behavior => $params)
			if($params)
				Event::trigger('behaviors_load_'.$behavior, static::getClassName());
	}
	
	/* MISC */
	public function set($vars) {
		foreach($vars as $k=>$v)
			$this->$k = $v;
				
		return $this;
	}

	public static function hasProperty($name) {
		return isset(static::$properties[$name]);
	}
	
	public function raw($name) {
		return $this->data['properties'][$name];
	}
	
	public static function getClassName() {
		return strtolower(get_called_class());
		#todo move strtolower to getModelName
	}
	
	public static function getModelName() {
		return basename(static::getClassName());
	}
	
	public function isNew() {
		return !(isset($this->data['properties']['id']) && $this->data['properties']['id']);
	}

	public static function create($values=array()) {
		$m = new static($values);
		return $m->save();
	}
	
	public static function load($id) {
		$model = new static;
		if($model->loadFromID($id)) {
			$model->configure();
			return $model;
		}
		return null;
	}
	
	public static function isI18N() {
		foreach(static::$properties as $prop)
			if($prop->i18n)
				return true;
		return false;
	}
	
	public function loadFromArray($cols) {
		foreach($cols as $col=>$value) {
			if(!static::hasProperty($col))
				$this->$col = $value;
			elseif(static::property($col)->filter) {
				$filter = static::property($col)->filter['from'];
				$this->$col = $model::$filter($value);
			}
			else
				$this->$col = static::property($col)->unserialize($value);
		}
		
		return $this;
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
	
	public static function getAttributes() {
		return array_keys(static::$properties);
	}
	
	public function getVars() {
		$attrs = $this->getAttributes();
		$vars = array();
		
		foreach($attrs as $attr) {
			if(!isset($this->data['properties'][$attr]))
				$vars[$attr] = '';
			else
				$vars[$attr] = $this->data['properties'][$attr];
		}
		
		return $vars;
	}
	
	public function getVar($name, $lang=null) {
		$res = null;
		if(static::property($name)->i18n) {
			if(!$lang)
				$lang = Config::get('locale');
			if($lang == 'all') {
				$langs = Config::get('locales');
				$res = array();
				foreach($langs as $lang)
					$res[$lang] = $this->getVar($name, $lang);
				return $res;
			}
			if(isset($this->data['properties'][$name][$lang]))
				$res = $this->data['properties'][$name][$lang];
		}
		elseif(isset($this->data['properties'][$name])) 
			$res = $this->data['properties'][$name];
		
		if($res === null && method_exists($this, 'fetch'))
			$res = $this->fetch($name, $lang);
		
		if(Coxis::get('in_view') && is_string($res))
			return HTML::sanitize($res);
		else
			return $res;
	}
	
	public function setAttribute($name, $value, $lang=null) {
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
				
		$data = $this->getVars();

		#validation
		$errors = $this->getValidator()->errors($data);
		
		#after validation
		
		
		return $errors;
	}
	
	/* PERSISTENCE */
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
		
		#before save
		$this->dosave();#save
		#after save

		return $this;
	}

	public static function triggerBefore($what, $args=array()) {
		if(isset(static::$meta['hooks']['before'][$what])) {
			$hook = static::$meta['hooks']['before'][$what];
			call_user_func_array($hook, $args);
		}
	}

	public static function triggerOn($what, $args=array()) {
		if(isset(static::$meta['hooks']['on'][$what])) {
			$hook = static::$meta['hooks']['on'][$what];
			call_user_func_array($hook, $args);
		}
	}

	public static function triggerAfter($what, $args=array()) {
		if(isset(static::$meta['hooks']['after'][$what])) {
			$hook = static::$meta['hooks']['after'][$what];
			call_user_func_array($hook, $args);
		}
	}

	public function dosave() {
		$this->triggerOn('save', array($this));
	}
	
	public function destroy() {
		$this->triggerOn('destroy', array($this));
	}
	
	public static function properties() {
		return static::$properties;
	}
	
	public static function property($name) {
		return static::$properties[$name];
	}

	protected static function trigger($name, $cb, $args=array()) {
		static::triggerBefore($name, $args);
		call_user_func_array($cb, $args);
		static::triggerOn($name, $args);
		static::triggerAfter($name, $args);
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

	public static function hookStaticCall($what, $cb) {
		static::$meta['hooks']['staticcall'][$what] = $cb;
	}

	public static function before($what, $cb) {
		static::$meta['hooks']['before'][$what] = $cb;
	}

	public static function on($what, $cb) {
		static::$meta['hooks']['on'][$what] = $cb;
	}

	public static function after($what, $cb) {
		static::$meta['hooks']['after'][$what] = $cb;
	}
}