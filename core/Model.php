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
	public static $file_messages = array();
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
	
	public function getAttribute($name, $lang=null) {
		if(isset(static::$properties[$name]['i18n']) && static::$properties[$name]['i18n']) {
			if(!$lang)
				$lang = Config::get('locale');
			if($lang == 'all') {
				$langs = Config::get('locales');
				$res = array();
				foreach($langs as $lang)
					$res[$lang] = $this->getAttribute($name, $lang);
				return $res;
			}
			if(Coxis::get('in_view') && is_string($this->data['properties'][$name][$lang]))
				return HTML::sanitize($this->data['properties'][$name][$lang]);
			elseif(isset($this->data['properties'][$name][$lang]))
				return $this->data['properties'][$name][$lang];
			else {
				$res = static::myDM()->getI18N($lang);
				if(!$res)
					return null;
				unset($res['id']);
				unset($res['locale']);
				foreach($res as $k=>$v)
					$this->{'set'.$k}($v, $lang);
					
				if(isset($this->data['properties'][$name][$lang]))
					return $this->data['properties'][$name][$lang];
			}
		}
		else
			if(Coxis::get('in_view') && is_string($this->data['properties'][$name]))
				return HTML::sanitize($this->data['properties'][$name]);
			elseif(isset($this->data['properties'][$name]))
				return $this->data['properties'][$name];
		return null;
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
	
	public function __get($name) {
		if(in_array($name, array_keys(static::$properties)) || $name == 'id') {
			return $this->getAttribute($name);
		}
		elseif(array_key_exists($name, $this::$files)) {
			$file = new ModelFile($this, $name);
			return $file;
		}
		elseif(array_key_exists($name, $this::$relationships)) {
			$res = $this->getRelation($name);
			//~ d($res);
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
			return $this->getAttribute($what, $lang);
		}
		else {
			if(array_key_exists($name, $this::$relationships)) {
				return $this->getRelation($name);
			}
		}
	}
	
	public static function __callStatic($name, $arguments) {
		if(strpos($name, 'loadBy') === 0) {
			preg_match('/^loadBy(.*)/', $name, $matches);
			$property = $matches[1];
			$val = $arguments[0];
			#todo ORM i18n
			return static::getDataMapper()->where(array($property => $val))->first();
		}
		else {
			$dm = static::getDataMapper();
			if(method_exists($dm, $name))
				return call_user_func_array(array($dm, $name), $arguments);
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
	
	public static function relationData($model, $name) {
		$relations = $model::$relationships;
		$relation = $relations[$name];
		
		$res = array();
		$res['type'] = $relation['type'];
		$res['model'] = $relation_model = $relation['model'];
		if($res['type'] == 'hasMany')
			$res['link'] = $model::getModelName().'_id';
		elseif($res['type'] == 'HMABT') {
			$res['link_a'] = $model::getModelName().'_id';
			$res['link_b'] = $relation_model::getModelName().'_id';
			if($model::getModelName() < $relation_model::getModelName())
				$res['join_table'] = Config::get('database', 'prefix').$model::getModelName().'_'.$relation_model::getModelName();
			else
				$res['join_table'] = Config::get('database', 'prefix').$relation_model::getModelName().'_'.$model::getModelName();
		}
		elseif($res['type'] == 'hasOne')
			$res['link'] = $model::getModelName().'_id';
		elseif($res['type'] == 'belongsTo')
			$res['link'] = $relation_model::getModelName().'_id';
		
		return $res;
	}

	#todo put it in orm
	public function getRelation($name) {
		$rel = static::relationData($this, $name);
		$relation_type = $rel['type'];
		$model = $rel['model'];
		
		switch($relation_type) {
			case 'hasOne':
				if($this->isNew())
					return null;
					
				$link = $rel['link'];
				return $model::where(array($link => $this->id))->first();
			case 'belongsTo':
				if($this->isNew())
					return null;
					
				$link = $rel['link'];
				return $model::where(array('id' => $this->$link))->first();
			case 'hasMany':
			case 'HMABT':
				if($this->isNew())
					return array();
					
				$collection = new Collection($this, $name);
				return $collection;
			default:	
				throw new \Exception('Relation '.$relation_type.' does not exist.');
		}
	}
	
	public static function getTable() {
		return Config::get('database', 'prefix').static::getModelName();
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
	
	public function loadFromID($id) {
		$res = static::getDataMapper()->dal()->where(array('id' => $id))->first();
			
		if($res) {
			$this->set($res);
			return true;
		}
		return false;
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
		//~ d($this->data['properties']);
		
		foreach($attrs as $attr) {
			if(!isset($this->data['properties'][$attr]))
				$vars[$attr] = '';
			else
				$vars[$attr] = $this->data['properties'][$attr];
		}
		//~ d($vars);
		
		return $vars;
	}
	
	public function myDM() {
		if($this->isNew())
			return static::getDataMapper();
		else
			return static::getDataMapper()->where(array('id'=>$this->id));
	}
	
	public static function getDataMapper() {
		return IoC::resolve('DataMapper', static::getClassName());
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
	
	/* PERSISTENCE */
	public function save($params=null, $force=false) {
		$this->pre_save($params);
		$this->_save($params, $force);

		return $this;
	}
	
	public function pre_save($params=null) {
		#set $params if any
		if($params)
			$this->set($params);
		
		#handle behaviors	
		$model_behaviors = static::$behaviors;
		foreach($model_behaviors as $behavior => $params)
			if($params)
				Event::trigger('behaviors_presave_'.$behavior, $this);	
		
		Event::trigger('presave_'.$this->getClassName(), $this);
	}
	
	public function _save($params=null, $force=false) {
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
		
		$vars = $this->getVars();
		
		//Persist local id field
		foreach(static::$relationships as $relationship => $params) {
			if(!isset($this->data[$relationship]))
				continue;
			$rel = static::relationData($this, $relationship);
			$type = $rel['type'];
			if($type == 'belongsTo') {
				$link = $rel['link'];
				$vars[$link] = $this->data[$relationship];
			}
		}
		
		#apply filters before saving
		foreach($vars as $col => $var) {
			if(isset(static::$properties[$col]['filter'])) {
				$filter = static::$properties[$col]['filter']['to'];
				$vars[$col] = static::$filter($var);
			}
			elseif(isset(static::$properties[$col]['type'])) {
				if(static::$properties[$col]['type']=='array')
					$vars[$col] = serialize($var);
				elseif(static::$properties[$col]['type']=='date')
					$vars[$col] = $var->datetime();
			}
		}
		
		//~ d($vars);
		#with relationships
		//~ static::getDataMapper()->save($this);
		
		$values = array();
		$i18n = array();
		foreach($vars as $p => $v) {
			if(isset(static::$properties[$p]['i18n']) && static::$properties[$p]['i18n'])
				foreach($v as $lang=>$lang_value)
					$i18n[$lang][$p] = $lang_value;
			else
				$values[$p] = $v;
		}
		
		//new
		if(!isset($this->id))
			$this->id = static::getDataMapper()->insert($values);
		//existing
		elseif(sizeof($vars) > 0) {
			$dm = static::getDataMapper();
			if(!$dm->where(array('id'=>$this->id))->update($values))
				$this->id = $dm->insert($values);
		}		
		
		foreach($i18n as $lang=>$values) {
			$dm = static::getDataMapper()->setTable(static::getTable().'_translation');
			if(!$dm->where(array('id'=>$this->id, 'locale'=>$lang))->update($values))
				$dm->insert(
					array_merge(
						$values, 
						array(
							'locale'=>$lang,
							'id'=>$this->id,
						)
					)
				);
		}
	
		//Persist relationships
		foreach(static::$relationships as $relationship => $params) {
			if(!isset($this->data[$relationship]))
				continue;
			$rel = static::relationData($this, $relationship);
			$type = $rel['type'];
				
			if($type == 'hasOne') {
				$relation_model = $rel['model'];
				$link = $rel['link'];
				$relation_model::where(array($link => $this->id))->update(array($link => 0));
				$relation_model::where(array('id' => $this->data[$relationship]))->update(array($link => $this->id));
			}
			elseif($type == 'hasMany' || $type == 'HMABT')
				$this->$relationship()->sync($this->data[$relationship]);
		}
	}
	
	public function destroy() {
		foreach(static::$files as $name=>$v)
			$this->$name->delete();
		
		//todo delete all cascade models and files
		return static::getDataMapper()->where(array('id' => $this->id))->delete();
	}
	
	public static function destroyOne($id) {
		if($model = static::load($id))
			return $model->destroy();
		return false;
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