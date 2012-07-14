<?php
class ModelException extends Exception {
	public $errors = array();
}

abstract class Model {
	public static $_properties;
	public static $order_by = 'id DESC';
	
	public function __construct($param='') {
		$this->configure();
		$this->post_configure();
		if(is_array($param)) {
			$this->loadDefault();
			$this->loadFromArray($param);
		}
		elseif($param != '')
			$this->loadFromID($param);
		else
			$this->loadDefault();
	}
	
	//~ public static function files() { return array(); }
	//~ public static function relationships() { return array(); }
	//~ public static function behaviors() { return array(); }
	public static $files = array();
	public static $relationships = array();
	public static $behaviors = array();
	public static $file_messages = array();
	public static $messages = array();

	public function post_configure() {
		foreach(static::getProperties() as $property=>$params)
			if(isset($params['multiple']))
				Model::$_properties[$this->getModelName()][$property]['type'] = 'array';
	}

	public function loadDefault() {
		foreach(static::getProperties() as $property=>$params)
			if(isset($params['defaultvalue']))
				$this->$property = $params['defaultvalue'];
			elseif($params['type'] == 'array')
				$this->$property = array();
			else
				$this->$property = '';
	}
	
	public static function create($values=array()) {
		return new ModelDecorator(new static($values));
	}
	
	public static function __callStatic($name, $arguments) {
		if(strpos($name, 'loadBy') === 0) {
			preg_match('/^loadBy(.*)/', $name, $matches);
			$property = $matches[1];
			$val = $arguments[0];
			try {
				return static::findOne(array(
					'conditions'=> array('`'.$property.'`=?' => array($val))
				));
			}
			catch(Exception $e) {
				return null;
			}
		}
	}

    public function __call($name, $arguments) {
		//called when setting or getting a related model
		$todo = substr($name, 0, 3);
		$what = strtolower(substr($name, 3));
		
		if($todo=='set') {
			$model = $arguments[0];
			$relationships = static::$relationships;
			if(array_key_exists($what, $relationships) && is_object($model) && get_parent_class($model)=='Model' && isset($model->id)) {
				$id_field = $what.'_id';
				$this->$id_field = $model->id;
				$this->$what = $model;
				
				return 1;
			}
		}
		elseif($todo=='get') {
			if(isset($arguments[0]))
				return $this->getRelation($what, $arguments[0]);
			else
				return $this->getRelation($what);
		}
    }

    public function getRelation($name, $params=array()) {
		$relationships = static::$relationships;
		//~ d($name, $relationships);
		if(!isset($relationships[$name]['type']) || !isset($relationships[$name]['model']))
			throw new Exception('Relation '.$name.' does not exists or is not set properly.');
			
		$relation_type = $relationships[$name]['type'];
		$model = $relationships[$name]['model'];
					
		$pre_conditions = $params;
		
		switch($relation_type) {
			case 'belongsTo':
				if($this->isNew())
					return null;
					
				if(isset($relationships[$name]['link']))
					$field_id = $relationships[$name]['link'];
				else
					$id_field = strtolower($model).'_id';
			
				if(isset($this->$model) && is_object($this->$model) && get_parent_class($this->$model)=='Model'
					&& isset($this->$model->id) && $this->$model->id == $this->$id_field) //if relationshp is already set and id is the same as the relationship id
					return $this->$model;
				else
					return $model::load($this->$id_field);
			case 'hasMany':
				if($this->isNew())
					return array();
				$model = $relationships[$name]['model'];
					
				if(isset($relationships[$name]['link']))
					$field_id = $relationships[$name]['link'];
				else {				
					#todo recheck and think about this part..
					
					foreach($model::$relationships as $parent_name=>$relation)
						if(strtolower($relation['model']) == strtolower(static::getModelName())) {
							$field_id = strtolower($parent_name).'_id';
							break;
						}
				}
					
				$conditions = array(
					'conditions'	=>	array($field_id." = ?" => array($this->id))
				);
				$conditions = array_merge(array('order_by'	=>	$model::$order_by), $pre_conditions, $conditions);
					
				return $model::find($conditions);
			case 'HMABT':
				if($this->isNew())
					return array();
				$model = $relationships[$name]['model'];
				if(strtolower(static::getModelName()) <= strtolower($model))
					$join_table = strtolower(static::getModelName()).'_'.strtolower($model);
				else
					$join_table = strtolower($model).'_'.strtolower(static::getModelName());
				$model_id_field = strtolower(static::getModelName()).'_id';
				$relation_id_field = strtolower($model).'_id';
				
				$conditions = array(
						'conditions'	=>	array(
							'a.'.$model_id_field.'=?'	=>	array($this->id),
							'a.'.$relation_id_field.'=b.id',
						)
					);
					if($name == 'projects')
				$conditions = array_merge(array('order_by'	=>	'a.'.$model::$order_by), $pre_conditions, $conditions);
				
				return $model::select(
					array(
						'a' => $join_table, 
						'b' => strtolower($model)
					),
					$conditions
				);
			default:	
				throw new Exception('Relation '.$name.' has no correct type.');
		}
    }
	
	public static function select($tables, $conditions, $fields=null) {
		$results = Database::getInstance()->select($tables, $conditions, $fields)->fetchAll();
		
		$models = array();
		foreach($results as $result)
			//~ $models[] = new static($result);
			$models[] = static::create($result);
		
		return $models;
	}
	
	//todo
	public function newCollection($array) {
		return new Collection($this, $array);
	}
	
	public static function getModelName() {
		return strtolower(get_called_class());
	}
	
	public static function findOne($params=array()) {
		$params['limit'] = 1;
		try {
		$results = static::find($params);
		}
		catch(Exception $e) {//todo
			d($e);
		}
		if(!isset($results[0]))
			throw new Exception('no result');
			
		return $results[0];
	}
	
	public static function find($params=array()) {
		$conditions = '';
		$order_by = '';
		$limit = '';
		
		if(isset($params['conditions'])) {
			$conditions = Database::formatConditions('AND', $params['conditions']);
			if($conditions && $conditions != '()')
				$conditions = ' WHERE '.$conditions;
			else
				$conditions = '';
		}
		
		if(!$order_by)
			if(isset($params['order_by']) && $params['order_by'])
				$order_by = ' ORDER BY '.$params['order_by'];
		
		if(!$order_by)
			$order_by = Controller::static_filter('find_model', $order_by, static::getModelName());
		
		if(!$order_by)
				$order_by = ' ORDER BY '.static::$order_by;
		
		if(isset($params['limit'])) {
			if(isset($params['offset']))
				$limit = ' LIMIT '.$params['offset'].', '.$params['limit'];
			else
				$limit = ' LIMIT '.$params['limit'];
		}
			
		$sql = 'SELECT * FROM %table%';
		$sql .= $conditions;
		$sql .= $order_by;
		$sql .= $limit;
		
		return static::query($sql);
	}
	
	public static function count($params) {
		$conditions = '';
		$order_by = '';
		$limit = '';
		
		if(isset($params['conditions'])) {
			$conditions = Database::formatConditions('AND', $params['conditions']);
			if($conditions && $conditions != '()')
				$conditions = ' WHERE '.$conditions;
			else
				$conditions = '';
		}
		
		if(isset($params['order_by']))
			$order_by = ' ORDER BY '.$params['order_by'];
		else
			$order_by = ' ORDER BY '.static::$order_by;
		
		if(isset($params['limit']))
			if(isset($params['offset']))
				$limit = ' LIMIT '.$params['offset'].', '.$params['limit'];
			else
				$limit = ' LIMIT '.$params['limit'];
			
		$sql = 'SELECT count(*) as total FROM `'.Config::get('database', 'prefix').strtolower(static::getModelName()).'`';
		$sql .= $conditions;
		$sql .= $order_by;
		$sql .= $limit;
		
		$result = Database::getInstance()->query($sql)->fetchOne();
		return $result['total'];
	}
	
	public static function paginate($page, $params, $per_page=10) {
		$total = static::count($params);
		
		$params['offset'] = ($page-1)*$per_page;
		$params['limit'] = $per_page;
		
		$models = static::find($params);
		
		$paginator = new Paginator($per_page, $total, $page);
		
		return array($models, $paginator);
	}
	
	public static function query($sql, $args=array()) {
		$db = Database::getInstance();
		$tableName = strtolower(static::getModelName());
		
		$sql = str_replace('%table%', '`'.Config::get('database', 'prefix').$tableName.'`', $sql);
		
		$results = $db->query($sql, $args)->fetchAll();
		
		$models = array();
		foreach($results as $result)
			//~ $models[] = new static($result);
			$models[] = static::create($result);
		
		return $models;
	}
	
	public function isNew() {
		return !isset($this->id);
	}

	public static function getProperty($prop) {
		return access(static::getProperties(), $prop);
	}

	public static function getProperties() {
		return Model::$_properties[static::getModelName()];
	}
	
	public static function getAttributes() {
		return array_keys(Model::$_properties[static::getModelName()]);
	}
	
	public function setFiles($files) {
		$this->_files = $files;
				
		return $this;
	}
	
	public static function load($id) {
		$model = static::create();
		
		try {
			$model->loadFromID($id);
			$model->configure();
			return $model;
		}
		catch(Exception $e) {
			return null;
		}
	
		//~ #todo do not catch mysql connect exception
	}
	
	public static function destroyOne($id) {
		if($model = static::load($id))
			return $model->destroy();
		return false;
	}
	
	public function getTableName() {
		return strtolower(get_class($this));
	}
	
	public function loadFromID($id) {
		$cols = Database::getInstance()->query('SELECT * FROM `'.Config::get('database', 'prefix').$this->getTableName().'` WHERE id=?', array($id))->fetchOne();
		//TODO: move it into Database::
		
		if(!$cols)
			return null;
		else
			return $this->loadFromArray($cols);
	}
	
	public function loadFromArray($cols) {
		$model = static::getModelName();
		foreach($cols as $col=>$value) {
			if(isset(Model::$_properties[$model][$col]['filter'])) {
				$filter = Model::$_properties[$model][$col]['filter']['from'];
				$this->$col = $model::$filter($value);
			}
			elseif(isset(Model::$_properties[$model][$col]['type'])) {
				if(Model::$_properties[$model][$col]['type'] == 'array') {
					try {
						$this->$col = unserialize($value);
					}
					catch(PHPErrorException $e) {
						$this->$col = array($value);
					}
					if(!is_array($this->$col))
						$this->$col = array();
					//~ d($this->$col, $col);
				}
				elseif(Model::$_properties[$model][$col]['type']=='date') {
					$this->$col = Date::fromDatetime($value);//todo with Date class
				}
				else
					$this->$col = $value;
			}
			else
				$this->$col = $value;
		}
		return $this;
	}
	
	public function getVars() {
		$attrs = $this->getAttributes();
		$vars = array();
		
		foreach($attrs as $attr) {
			if(!isset($this->$attr))
				$vars[$attr] = '';
			else
				$vars[$attr] = $this->$attr;
		}
		
		return $vars;
	}
	
	public function getValidator() {
		$modelName = static::getModelName();
		
		$validator = new Validator();
		$constrains = static::$_properties[$modelName];
		//~ d(Choeur::getProperties());
		//~ d($constrains);
		foreach($constrains as $property=>$property_constrains)
			foreach($property_constrains as $k=>$constrain)
				if(strtolower($k)=='validation')
					$constrains[$property][$k] = array($this, $constrain);
		//~ d(1,$constrains);
		$validator->setConstrains($constrains);

		return $validator;
	}
	
	public function getFileValidator() {
		$files = static::$files;
		
		$file_validator = new FileValidator();
		$constrains = $files;
		
		foreach($constrains as $file=>$file_constrains)
			foreach($file_constrains as $k=>$constrain) {
				if($this->getFilePath($file))
					$constrains[$file]['path'] = $this->getFilePath($file);
				else
					$constrains[$file]['path'] = false;
				unset($constrains[$file]['dir']);
			}
			
		$file_validator->setConstrains($constrains);
		
		return $file_validator;
	}
	
	public function slugify($src=null) {	
		if(!$src)
			$src = $this;
		if($this->isNew())
			$this->slug = Tools::slugify($src);
		else {
			$inc = 1;
			do {
				$this->slug = Tools::slugify($src).($inc < 2 ? '':'-'.$inc);
				$inc++;
			}
			while(static::query('SELECT * FROM %table% WHERE id!=? AND slug=?', array($this->id, $this->slug)));
		}
	}
	
	//~ public static function getStatic($model, $attr) {
		//~ return $model::$attr;
	//~ }
	
	public static function loadModel() {
		static::loadBehaviors();
		static::loadRelationships();
		static::loadFiles();
	}
	
	public static function loadBehaviors() {
		Controller::static_trigger('behaviors_pre_load', static::getModelName());
	//~ try {
		//~ d(page::$behaviors);
		//~ }
		//~ catch(Exception $e) {
		//~ }
		
		//~ Controller::static_trigger('behaviors_load');
		//~ d(static::$behaviors);
	
		$model_behaviors = static::$behaviors;
		//~ d(static::getModelName(), $model_behaviors);
		foreach($model_behaviors as $behavior => $params)
			if($params)
				Controller::static_trigger('behaviors_load_'.$behavior, static::getModelName());
	}
	//todo add properties on the fly when saving?
	
	public static function loadRelationships() {
		$model_relationships = static::$relationships;
		
		if(is_array($model_relationships))
			foreach($model_relationships as $relationship => $params)
				if($params['type'] == 'belongsTo' || $params['type'] == 'hasOne')
					static::addProperty($relationship.'_id', array('type' => 'integer', 'required' => (isset($params['required']) && $params['required']), 'editable'=>false));
	}
	
	public static function loadFiles() {
		$model_files = static::$files;
		
		if(is_array($model_files))
			foreach($model_files as $file => $params)
				#multiple
				if(isset($params['multiple']) && $params['multiple']) {
					static::addProperty('filename_'.$file, array('type' => 'array', 'defaultvalue'=>array(), 'editable'=>false));
					
					$modelName = static::getModelName();
					$admin_controller = strtolower(CoxisAdmin::getAdminControllerFor($modelName));
					$index = CoxisAdmin::getIndexFor($modelName);
					
					//~ d('coxis_'.$modelName.'_files_add');

					#todo should handle this in bundles/_admin/ ..
					#todo be sure not to create multiple times the same hook
					Coxis::$controller_hooks[$admin_controller][] = array(
								'route'			=>	$index.'/:id/:file/add',
								'name'			=>	'coxis_'.$modelName.'_files_add',
								'controller'	=>	'Multifile',
								'action'			=>	'add'
							);
					Coxis::$controller_hooks[$admin_controller][] = array(
								'route'			=>	$index.'/:id/:file/delete/:pos',
								'name'			=>	'coxis_'.$modelName.'_files_delete',
								'controller'	=>	'Multifile',
								'action'			=>	'delete'
							);
				}
				#single
				else
					static::addProperty('filename_'.$file, array('type' => 'text', 'editable'=>false));
	}
	
	public static function addProperty($property, $params) {
		$modelName = static::getModelName();
		Model::$_properties[$modelName][$property] = $params;
	}
	
	public function getFiles() {
		$results = array();
		$existing_files = static::$files;
		foreach($existing_files as $name => $file) {
			$path = $this->getFilePath($name);
			if(is_array($path)) {
				foreach($path as $k=>$one_path)
					$path[$k] = _WEB_DIR_.'/'.$one_path;
				$results[$name] = $path;	
			}
			elseif($this->getFilePath($name))
				$results[$name] = _WEB_DIR_.'/'.$this->getFilePath($name);
			else
				$results[$name] = null;
		}
		
		if(isset($this->_files)) {
			$new_files = $this->_files;
			if(isset($new_files))
				foreach($new_files as $name => $file)
					if(isset($file['error']) && $file['error'] == 0)
						if(isset($file['tmp_name']) && !empty($file['tmp_name']))
							$results[$name] = $file['tmp_name'];
		}
		
		return $results;
	}
	
	public function pre_save($params=null) {
		#set $params if any
		if($params)
			$this->set($params);
		
		#handle behaviors	
		$model_behaviors = static::$behaviors;
		foreach($model_behaviors as $behavior => $params)
			if($params)
				Controller::static_trigger('behaviors_presave_'.$behavior, $this);
	}
	
	public function save($params=null, $force=false) {
		$this->pre_save($params);
		$this->_save($params, $force);
	}
	
	public function _save($params=null, $force=false) {
		if(!$force) {
			#validate params and files
			if($errors = $this->errors()) {
				$e = new ModelException();
				$e->errors = $errors;
				throw $e;
			}
		}
			
		$this->move_files();
		
		$vars = $this->getVars();
		
		#apply filters before saving
		foreach($vars as $col => $var) {
			$model = static::getModelName();
			if(isset(Model::$_properties[$model][$col]['filter'])) {
				$filter = Model::$_properties[$model][$col]['filter']['to'];
				$vars[$col] = $model::$filter($var);
			}
			elseif(isset(Model::$_properties[$model][$col]['type'])) {
				if(Model::$_properties[$model][$col]['type']=='array')
					$vars[$col] = serialize($var);
				elseif(Model::$_properties[$model][$col]['type']=='date')
					//~ $vars[$col] = Date::toTimestamp($var);
					$vars[$col] = $var->datetime();
			}
		}
		
		//new
		if(!isset($this->id)) {
			Database::getInstance()->insert($this->getTableName(), $vars);
			$this->id = Database::getInstance()->id();
		}
		//existing
		elseif(sizeof($vars) > 0)
			Database::getInstance()->update($this->getTableName(), array('id' => $this->id), $vars);
	
		//Persist relationships
		$relationships = static::$relationships;
		if(is_array($relationships)) {
			foreach($relationships as $relationship => $params) {
				//~ if($params['type'] == 'belongsTo' || $params['type'] == 'hasOne') {
					//~ $id_field = $relationship.'_id';
					//~ $vars[$id_field] = $this->$id_field;
				//~ }
				if($params['type'] == 'hasOne') {
					$id_field = $relationship.'_id';
					$model_id_field = strtolower($model).'_id';
					Database::getInstance()->update(
						$params['model'],
						array($model_id_field => $this->id),
						array($model_id_field => 0)
					);
					Database::getInstance()->update(
						$params['model'],
						array('id' => $this->$id_field),
						array($model_id_field => $this->id)
					);
				}
				//todo mieux faire HMABT
				elseif($params['type'] == 'HMABT') {
					$id_field = $relationship.'_id';
					//~ d($this->$id_field);
					
					$relation_model = $params['model'];
					if(strtolower($model) <= strtolower($relation_model))
						$join_table = strtolower($model).'_'.strtolower($relation_model);
					else
						$join_table = strtolower($relation_model).'_'.strtolower($model);
					$model_id_field = strtolower($model).'_id';
					$relation_id_field = strtolower($relation_model).'_id';
					
					//~ d($join_table, $model_id_field, $relation_id_field);
						
					if(isset($this->$id_field)) {				
						Database::getInstance()->delete($join_table, array(
							$model_id_field	=>	$this->id
						));
						
						if(!is_array($this->$id_field))
							$this->$id_field = array($this->$id_field);
						foreach($this->$id_field as $relation_id)
							Database::getInstance()->insert($join_table, array($model_id_field => $this->id, $relation_id_field => $relation_id));
					}
				}
			}
		}
	}
	
	public function isValid($file) {
		$file_validator = $this->getFileValidator();
		
		return !$file_validator->validate($this->getFiles());
	}
	
	public function errors() {
		#validator
		$validator = $this->getValidator();
		if(static::$messages)
			//~ d(static::$messages);
			$validator->setMessages(static::$messages);
		$file_validator = $this->getFileValidator();
		
		if(static::$file_messages)
			$file_validator->setMessages(static::$file_messages);
			
		$vars = $this->getVars();
		
		return array_merge(
			$validator->validate($vars), 
			$file_validator->validate($this->getFiles())
		);
	}
	
	public function deleteFile($file) {
		$params = $this->getFile($file);
		if(isset($params['multiple']) && $params['multiple'])
			return;
			
		$path = $this->getFilepath($file);
		if(file_exists(_WEB_DIR_.'/'.$path))
			unlink(_WEB_DIR_.'/'.$path);
		ImageCache::clearFile($path);
		$file_property = 'filename_'.$file;
		$this->$file_property = '';
	}
	
	public function move_files() {
		$model_files = static::$files;
		if(isset($this->_files))
			foreach($this->_files as $file=>$arr)
				if(isset($model_files[$file]) && is_uploaded_file($arr['tmp_name'])) {
					if(!isset($model_files[$file]['format']))
						$model_files[$file]['format'] = IMAGETYPE_JPEG;
						
					if(isset($model_files[$file]['multiple']) && $model_files[$file]['multiple']) {
						if($model_files[$file]['type'] == 'image') {
							$filename = $arr['name'];
							
							$path = _WEB_DIR_.'/upload/'.trim($model_files[$file]['dir'], '/').'/'.$filename;
							$filename = ImageManager::load($arr['tmp_name'])->save($path, $model_files[$file]['format']);
							$file_property = 'filename_'.$file;
							array_push($this->$file_property, $filename);
						}
						else
							#todo change filename if already existing
							#todo add it to model filename_
							FileManager::move_uploaded($_FILES[$file]['tmp_name'], $model_files[$file]['path']);
					}
					else {
						#delete old file
						$old_path = $this->getFilePath($file);
						if($old_path) {
							FileManager::unlink(_WEB_DIR_.'/upload/'.$old_path);
							if($model_files[$file]['type'] == 'image')
								ImageCache::clearFile($old_path);
						}
							
						if($model_files[$file]['type'] == 'image') {
							$filename = $arr['name'];
							
							$path = _WEB_DIR_.'/upload/'.trim($model_files[$file]['dir'], '/').'/'.$filename;
							$filename = ImageManager::load($arr['tmp_name'])->save($path, $model_files[$file]['format']);
							$file_property = 'filename_'.$file;
							$this->$file_property = $filename;
						}
						else {
							#todo change filename if already existing
							#todo add it to model filename_
							$filename = $arr['name'];
							
							$path = _WEB_DIR_.'/upload/'.trim($model_files[$file]['dir'], '/').'/'.$filename;
							$filename = FileManager::move_uploaded($arr['tmp_name'], $path);
							$file_property = 'filename_'.$file;
							$this->$file_property = $filename;
						}
					}
				}
	}
	
	public function destroy() {
		foreach(static::$files as $name=>$v) {
			$path = $this->getFilePath($name);
			if(is_array($path))
				foreach($path as $file)
					FileManager::unlink($file);
			else
				FileManager::unlink($path);
		}
		
		//todo delete all cascade models and files
		return Database::getInstance()->delete($this->getTableName(), array('id' => $this->id))->affected_rows();
	}
	
	public function getFile($file) {
		$files = static::$files;
		return $files[$file];
	}
	
	public function getFilePath($file) {
		$file_infos = $this->getFile($file);
		$dir = 'upload/'.trim($file_infos['dir'], '/').'/';
		$filename_property = 'filename_'.$file;
		if(isset($this->$filename_property)) {
			#multiple files
			if(isset($file_infos['multiple']) && $file_infos['multiple']) {
				$result = array();
				try {
					foreach($this->$filename_property as $filename) {
						$result[] = $dir.$filename;
					}
				}
				catch(Exception $e) {
					d($filename_property, $this->$filename_property);
				}
				return $result;
			}
			#single file
			else {
				$filename = $this->$filename_property;
				
				if($filename)
					return $dir.$filename;
				else
					return null;	
			}
		}
		else
			return null;
	}
	
	public function hasFile($file) {
		$files = static::$files;
		return array_key_exists($file, $files);
	}
	
	public function fileExists($file) {
		$file_infos = $this->getFile($file);
		$filename_property = 'filename_'.$file;
		return isset($this->$filename_property);
	}
	
	public function getRawFilePath($file) {
		$file_infos = $this->getFile($file);
		$filename_property = 'filename_'.$file;
		return $this->$filename_property;
	}
	
	public function setRawFilePath($file, $paths) {
		$file_infos = $this->getFile($file);
		$filename_property = 'filename_'.$file;
		$this->$filename_property = $paths;
		
		return $this;
	}
	
	protected function configure() {}
}