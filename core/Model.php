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
		if(is_array($param))
			$this->loadDefault()
			       ->set($param);
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
		if(in_array($name, array_keys(static::$properties)))
			return $this->getVar($name);
		elseif(array_key_exists($name, $this::$files))
			return new ModelFile($this, $name);
		elseif(array_key_exists($name, $this::$relationships)) {
			$res = $this->getRelation($name);
			if($res instanceof \Coxis\Core\Collection)
				return $res->get();
			else
				return $res;
		}
		throw new \Exception('The attribute "'.$name.'" does not exist for model "'.$this->getClassName().'"');
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
		elseif(array_key_exists($name, $this::$relationships))
			return $this->getRelation($name);
		throw new \Exception('The method "'.$name.'" does not exist for model "'.$this->getClassName().'"');
	}
	
	public static function __callStatic($name, $arguments) {
	}
	
	/* INIT AND MODEL CONFIGURATION */
	public static function _autoload() {
		if(static::getClassName() == 'coxis\core\model')
			return;
		static::loadModel();
		//~ static::configure();
		static::post_configure();
	}
	
	//~ protected static function configure() {}

	protected static function post_configure() {
		foreach(static::$properties as $property=>$params) {
			if(isset($params['multiple']))
				static::$properties[$property]['type'] = 'array';
			if(!isset($params['type']))
				static::$properties[$property]['type'] = 'text';
			if(!isset($params['required']))
				static::$properties[$property]['required'] = true;
		}
	}

	public function loadDefault() {
		foreach(static::getProperties() as $property=>$params) {
			if(isset($params['default']))
				$this->$property = $params['default'];
			elseif($params['type'] == 'array')
				$this->$property = array();
			else
				$this->$property = '';
		}
				
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
					
		static::loadBehaviors();
		static::loadFiles();
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
			foreach($model_files as $file => $params) {
				if(isset($params['multiple']) && $params['multiple']) #multiple
					static::addProperty('filename_'.$file, array('type' => 'array', 'editable'=>false, 'required'=>false));
				else #single
					static::addProperty('filename_'.$file, array('type' => 'text', 'editable'=>false, 'required'=>false));
			}
	}
	
	/* MISC */
	public function set($vars) {
	//~ static $i;
	//~ if($i++>0) {
	//~ $this->date_debut = '06/08/2012';
	//~ d($this->data);
	//~ d($vars);
	//~ }
		$props = $this->getProperties();
		foreach($vars as $k=>$v) {
			//~ if(isset($props[$k]) && $props[$k]['type'] == 'date')
				//~ $this->$k = Date::fromDatetime($v);
			//~ else
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
		return Importer::basename(static::getClassName());
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
	
	public function configure() {
	}
	
	public static function isI18N() {
		foreach(static::$properties as $prop)
			if(isset($prop['i18n']) && $prop['i18n'])
				return true;
		return false;
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
		else {
			try {
				$res = $this->data['properties'][$name];
			} catch(\ErrorException $e) {
				throw new \Exception('The var "'.$name.'" does not exist for model "'.$this->getClassName().'"');
			}
		}
		
		//~ elseif(isset($this->data['properties'][$name])) 
			//~ $res = $this->data['properties'][$name];
		//~ else
			//~ throw new \Exception('The var "'.$name.'" does not exist for model "'.$this->getClassName().'"');
		
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
		
		#todo create a hook for date type
		if(isset(static::$properties[$name]) && (static::$properties[$name]['type'] == 'date' || static::$properties[$name]['type'] == 'datetime'))
			$value = Date::fromDate($value);
			
		if(isset(static::$properties[$name])) {
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
		elseif(isset(static::$files[$name])) {
			$this->data['_files'][$name] = $value;
		}
		else
			$this->data[$name] = $value;
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
	
	public function isValid() {
		return $this->getValidator()->errors();
	}
	
	public function errors() {
		#before validation
		foreach(static::$behaviors as $behavior => $params)
			if($params)
				Event::trigger('behaviors_presave_'.$behavior, $this);
				
		$data = $this->getVars();
		foreach(static::$files as $file=>$params) {
			if(isset($this->data[$file]['tmp_name']) && $this->data[$file]['tmp_name'])
				$data[$file] = $this->data[$file]['tmp_name'];
			else
				$data[$file] = 'web/'.$this->$file->get();
		}
		
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

	public function dosave() {
		$this->move_files();
	}
	
	public function destroy() {
		foreach(static::$files as $name=>$v)
			$this->$name->delete();
	}
	
	/* FILES */
	#getvalidator
		#???
	#errors
		#???
		
	#loadModel
		#loadFiles
		
	#__get()
	#setAttribute
	
	#save / move_files
	
	#hasFile
	
	/*
	*/
	
	#todo
	#loadFiles: behavior?
	//~ Actualite::get_truc(function($model) {
	//~ });
	//~ Actualite::set_truc(function($model, $value) {
	//~ });
	//~ Actualite::onSave(function($model) {
	//~ });
	//~ Actualite::call_hasFile(function($model) {
	//~ });
	
	public function move_files() {
		//~ d($this->data['_files']);
		if(isset($this->data['_files']) && is_array($this->data['_files']))
			foreach($this->data['_files'] as $file=>$arr) {
				if(!isset($arr['tmp_name']))
					continue;
				if($this->hasFile($file) && is_uploaded_file($arr['tmp_name'])) {
					$path = _WEB_DIR_.'/'.$this->$file->dir().'/'.$arr['name'];
					$this->$file->add($arr['tmp_name'], $path);
				}
			}
	}
	
	public function hasFile($file) {
		return array_key_exists($file, static::$files);
	}

	public function properties() {
		return static::$properties;
	}

	public function property($name) {
		return static::$properties[$name];
	}
}