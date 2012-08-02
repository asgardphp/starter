<?php
namespace Coxis\Core;

class ModelException extends \Exception {
	public $errors = array();
}

abstract class Model {
	protected $data = array(
		'properties'	=>	array(),
		#todo with others like files, relationships, ..
	);
	public static $meta = array();
	public static $properties = array();
	public static $files = array();
	public static $relationships = array();
	public static $behaviors = array();
	public static $messages = array();
	
	public function __construct($param='') {
		if(is_array($param)) {
			$this->loadDefault();
			$this->loadFromArray($param);
		}
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
		if(in_array($name, array_keys(static::$properties))) {
			return $this->getVar($name);
		}
		elseif(array_key_exists($name, $this::$files)) {
			$file = new ModelFile($this, $name);
			return $file;
		}
		elseif(array_key_exists($name, $this::$relationships)) {
			$res = $this->getRelation($name);
			if($res instanceof \Coxis\Core\Collection)
				return $res->get();
			else
				return $res;
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
		
		if($todo=='set') {
			$value = $arguments[0];
			$lang = null;
			if(isset($arguments[1]))
				$lang = $arguments[1];
			$this->setAttribute($what, $value, $lang);
		}
		elseif($todo=='get') {
			$lang = null;
			if(isset($arguments[0]))
				$lang = $arguments[0];
			return $this->getVar($what, $lang);
		}
		else {
			if(array_key_exists($name, $this::$relationships)) {
				return $this->getRelation($name);
			}
		}
	}
	
	/* INIT AND MODEL CONFIGURATION */
	final public static function _autoload() {
		if(static::getClassName() == 'coxis\core\model')
			return;
		static::loadModel();
	}
	
	protected static function configure() {}

	private static function post_configure() {
		foreach(static::getProperties() as $property=>$params) {
			if(isset($params['multiple']))
				static::$properties[$property]['type'] = 'array';
			if(!isset($params['type']))
				static::$properties[$property]['type'] = 'text';
			if(!isset($params['required']))
				static::$properties[$property]['required'] = true;
		}
	}

	public function loadDefault() {
		foreach(static::getProperties() as $property=>$params)
			if(isset($params['default']))
				$this->$property = $params['default'];
			elseif($params['type'] == 'array')
				$this->$property = array();
			else
				$this->$property = '';
	}
	
	public static function loadModel() {
		if(!isset(static::$meta['order_by']))
			static::$meta['order_by'] = 'id DESC';
	
		$properties = static::$properties;
		foreach($properties as $k=>$v)
			if(is_int($k)) {
				$properties[$v] = array();
				unset($properties[$k]);
			}
		static::$properties = $properties;
		
		static::addProperty('id', array('type' => 'text', 'editable'=>false, 'required'=>false));
					
		static::loadBehaviors();
		static::loadRelationships();
		static::loadFiles();
		static::configure();
		static::post_configure();
	}
	
	public static function loadBehaviors() {
		Event::trigger('behaviors_pre_load', static::getClassName());
	
		$model_behaviors = static::$behaviors;
		
		foreach($model_behaviors as $behavior => $params)
			if($params)
				Event::trigger('behaviors_load_'.$behavior, static::getClassName());
	}
	
	public static function loadFiles() {
		$model_files = static::$files;
		
		if(is_array($model_files))
			foreach($model_files as $file => $params)
				#multiple
				if(isset($params['multiple']) && $params['multiple'])
					static::addProperty('filename_'.$file, array('type' => 'array', 'default'=>array(), 'editable'=>false, 'required'=>false));
				#single
				else
					static::addProperty('filename_'.$file, array('type' => 'text', 'editable'=>false, 'required'=>false));
	}
	
	public static function loadRelationships() {
		$model_relationships = static::$relationships;
		
		if(is_array($model_relationships))
			foreach($model_relationships as $relationship => $params)
				if($params['type'] == 'belongsTo') {
					$rel = static::relationData(static::getClassName(), $relationship);
					static::addProperty($rel['link'], array('type' => 'integer', 'required' => (isset($params['required']) && $params['required']), 'editable'=>false));
				}
	}
	
	/* MISC */
	public function set($vars) {
		$props = $this->getProperties();
		foreach($vars as $k=>$v) {
			if(isset($props[$k]) && $props[$k]['type'] == 'date')
				$this->$k = Date::fromDatetime($v);
			else
				$this->$k = $v;
		}
				
		return $this;
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
		else
			return null;
	}
	
	public static function isI18N() {
		foreach(static::$properties as $prop)
			if(isset($prop['i18n']) && $prop['i18n'])
				return true;
		return false;
	}
	
	public function loadFromArray($cols) {
		foreach($cols as $col=>$value) {
			if(isset(static::$properties[$col]['filter'])) {
				$filter = static::$properties[$col]['filter']['from'];
				$this->$col = $model::$filter($value);
			}
			elseif(isset(static::$properties[$col]['type'])) {
				if(static::$properties[$col]['type'] === 'array') {#php, seriously.. == 'array'
					try {
						$this->$col = unserialize($value);
					} catch(\ErrorException $e) {
						$this->$col = array($value);
					}
					if(!is_array($this->$col))
						$this->$col = array();
				}
				elseif(static::$properties[$col]['type'] === 'date')
					$this->$col = \Coxis\Core\Tools\Date::fromDatetime($value);
				else
					$this->$col = $value;
			}
			else
				$this->$col = $value;
		}
		
		return $this;
	}


	
	public static function addProperty($property, $params) {
		static::$properties[$property] = $params;
	}
	
	public static function getProperty($prop) {
		return get(static::getProperties(), $prop);
	}

	public static function getProperties() {
		return static::$properties;
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
		if(isset(static::$properties[$name]['i18n']) && static::$properties[$name]['i18n']) {
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
		if(isset(static::$properties[$name]['setFilter'])) {
			$filter = static::$properties[$name]['setFilter'];
			$value = call_user_func_array($filter, array($value));
		}
		if(isset(static::$properties[$name]['i18n']) && static::$properties[$name]['i18n']) {
			if(!$lang)
				$lang = Config::get('locale');
			if($lang == 'all')
				foreach($value as $one => $v)
					$this->data['properties'][$name][$one] = $v;
			else
				$this->data['properties'][$name][$lang] = $value;
		}
		else
			$this->data['properties'][$name] = $value;
	}
	
	/* VALIDATION */
	public function getValidator() {
		$constrains = static::$properties;
		foreach($constrains as $attribute=>$attribute_constrains)
			foreach($attribute_constrains as $rule=>$params)
				if($rule === 'type') {
					$constrains[$attribute][$params] = array();
					unset($constrains[$attribute]['type']);
				}
		foreach(static::$files as $file=>$params) {
			$res = $params;
			if(isset($params['required'])) {
				$res['filerequired'] = $params['required'];
				unset($res['required']);
			}
			if(isset($params['type'])) {
				$res[$params['type']] = null;
				unset($res['type']);
			}
			unset($res['dir']);
			unset($res['multiple']);
			$constrains[$file] = $res;
		}
		
		$messages = static::$messages;
		
		$validator = new Validator($constrains, $messages);

		return $validator;
	}
	
	public function isValid($file) {
		return $this->getValidator()->errors();
	}
	
	public function errors() {
		$data = $this->getVars();
		foreach(static::$files as $file=>$params)
			if(isset($this->data[$file]['tmp_name']) && $this->data[$file]['tmp_name'])
				$data[$file] = $this->data[$file]['tmp_name'];
			else
				$data[$file] = 'web/'.$this->$file->get();
		
		return $this->getValidator()->errors($data);
	}
	
	public function _save($force=false) {
		if(!$force) {
			#validate params and files
			if($errors = $this->errors()) {
				$msg = implode('<br>'."\n", $errors);
				$e = new ModelException($msg);
				$e->errors = $errors;
				throw $e;
			}
		}
			
		$this->move_files();
	}
	
	/* PERSISTENCE */
	public function save($values=null, $force=false) {
		#set $values if any
		if($values)
			$this->set($values);
			
		$this->pre_save();
		$this->_save($force);

		return $this;
	}
	
	public function pre_save() {
		#handle behaviors	
		$model_behaviors = static::$behaviors;
		foreach($model_behaviors as $behavior => $params)
			if($params)
				Event::trigger('behaviors_presave_'.$behavior, $this);
		
		Event::trigger('presave_'.$this->getClassName(), $this);
	}
	
	public function destroy() {
		foreach(static::$files as $name=>$v)
			$this->$name->delete();
	}
	
	/* FILES */
	public function move_files() {
		$model_files = static::$files;
		if(isset($this->data['_files']) && is_array($this->data['_files']))
			foreach($this->data['_files'] as $file=>$arr)
				if($this->hasFile($file) && is_uploaded_file($arr['tmp_name'])) {
					$path = _WEB_DIR_.'/'.$this->$file->dir().'/'.$arr['name'];
					$this->$file->add($arr['tmp_name'], $path);
				}
	}
	
	public function setFiles($files) {
		$this->_files = $files;
				
		return $this;
	}
	
	public function hasFile($file) {
		return array_key_exists($file, static::$files);
	}
}