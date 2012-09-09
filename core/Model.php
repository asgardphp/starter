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
	public $_is_loaded;

	public function __construct($param='') {
		$this->_is_loaded = false;
		// $this->triggerOn('construct', array($this, $param));
		$this->filter('construct', null, array($this, $param));
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
		return $this->get($name);
	}
	
	public function __isset($name) {
		return isset($this->data['properties'][$name]);
	}
	
	public function __unset($name) {
		unset($this->data['properties'][$name]);
	}

	public static function __callStatic($name, $arguments) {
		$chain = new FilterChain();
		$chain->found = false;

		$res = static::filterChain($chain, 'callStatic', null, array($name, $arguments));

		if(!$chain->found)
			throw new \Exception('Static method '.$name.' does not exist for model '.static::getModelName());

		return $res;
	}

	public function __call($name, $arguments) {
		$chain = new FilterChain;
		$chain->found = false;

		$res = static::filterChain($chain, 'call', null, array($this, $name, $arguments));

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
		
		$this->filter('save', null, array($this));
		// if(!$this->filter('save', null, array($this)))
		// 	throw new \Exception('Cannot save non-persistent models');

		return $this;
	}
	
	public function destroy() {
		$this->filter('destroy', null, array($this));
		// if(!$this->filter('destroy', null, array($this)))
		// 	throw new \Exception('Cannot destroy non-persistent models');
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
	
	public function valid() {
		return !$this->errors();
	}
	
	public function errors() {
		#before validation
		#todo use model hooks
		foreach(static::$behaviors as $behavior => $params)
			if($params)
				Event::trigger('behaviors_presave_'.$behavior, $this);
				
		$data = $this->toArray();

		$errors = null;
		$model = $this;
		$this->filter('validation', function($chain, $data, &$errors) use($model) {
			$errors = $model->getValidator()->errors($data);
		}, array($data), $errors);
		
		return $errors;
	}

	#cannot use references with get_func_args
	protected static function filter($name, $cb=null, $args=array(), &$filter1=null, &$filter2=null, &$filter3=null, &$filter4=null, &$filter5=null, &$filter6=null,
		&$filter7=null, &$filter8=null, &$filter9=null, &$filter10=null) {
		return static::filterChain(new FilterChain, $name, $cb, $args, $filter1, $filter2, $filter3, $filter4, $filter5, $filter6,
			$filter7, $filter8, $filter9, $filter10);
	}

	#cannot use references with get_func_args
	protected static function filterChain($chain, $name, $cb=null, $args=array(), &$filter1=null, &$filter2=null, &$filter3=null, &$filter4=null, &$filter5=null, &$filter6=null,
		&$filter7=null, &$filter8=null, &$filter9=null, &$filter10=null) {
		if(count(func_get_args()) > 14)
			throw new \Exception("filter() can only accept up to 13 arguments");

#todo filter -> hook
#below

// Actualite::hook('call', 'before', function() {});
// Hook::addHook('models', 'actualite', 'call', 'before', function() {});

// Actualie::trigger('call', function() {});
// Hook::trigger('models', 'actualite', 'call', function() {});
// Actualie::trigger('call', 'before', function() {});
// Hook::trigger('models', 'actualite', 'call', 'before', function() {});
// Actualie::trigger('call', 'before');
// Hook::trigger('models', 'actualite', 'call', 'before');

		#todo remove filter calls
		$chain->calls = array_merge(
			isset(static::$meta['filters']['before'][$name]) ? static::$meta['filters']['before'][$name]:array(),
			$cb !== null ? array($cb):array(),
			isset(static::$meta['filters']['on'][$name]) ? static::$meta['filters']['on'][$name]:array(),
			isset(static::$meta['filters']['after'][$name]) ? static::$meta['filters']['after'][$name]:array()
		);

		$filters = array(&$filter1, &$filter2, &$filter3, &$filter4, &$filter5, &$filter6, &$filter7, &$filter8, &$filter9, &$filter10);
		return $chain->run($args, $filters);
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

	public function raw($name, $lang=null) {
		$res = $this->get($name, $lang);

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
	
	public function get($name, $lang=null) {
		$res = null;
		#todo go for data[$name] if orm fetch failed
		static::filter('get', function($chain, $model, $name, $lang, &$res) {
			if($model::hasProperty($name)) {
				if($model::property($name)->i18n) {
					if(!$lang)
						$lang = Config::get('locale');
					if($lang == 'all') {
						$langs = Config::get('locales');
						$res = array();
						foreach($langs as $lang)
							$res[$lang] = $model->get($name, $lang);
					}
					elseif(isset($model->data['properties'][$name][$lang]))
						$res = $model->data['properties'][$name][$lang];
				}
				elseif(isset($model->data['properties'][$name])) 
					$res = $model->data['properties'][$name];
			}
			elseif(isset($model->data[$name]))
				$res = $model->data[$name];
			if($res)
				$chain->stop();
		}, array($this, $name, $lang), $res);

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
	public static function filterBefore($what, $cb) {
		static::$meta['filters']['before'][$what][] = $cb;
	}
	
	public static function filterOn($what, $cb) {
		static::$meta['filters']['on'][$what][] = $cb;
	}
	
	public static function filterAfter($what, $cb) {
		static::$meta['filters']['after'][$what][] = $cb;
	}
}