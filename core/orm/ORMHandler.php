<?php
class ORMHandler {
	private $model;

	function __construct($modelName) {
		$this->model = $modelName;
		if(!isset($modelName::$meta['order_by']))
			$modelName::$meta['order_by'] = 'id DESC';
		
		$modelName::addProperty('id', array(
			'type' => 'text', 
			'editable'=>false, 
			'required'=>false,
			'orm'	=>	array(
				'type'	=>	'int(11)',
				'auto_increment'	=>	true,
				'key'	=>	'PRI',
				'nullable'	=>	false,
			),
		));
		static::loadRelationships($modelName);
	}
	
	public function isNew($model) {
		return !(isset($model->data['properties']['id']) && $model->data['properties']['id']);
	}

	public function load($id) {
		$modelName = $this->model;
		$model = new $modelName($id);
		if($model->isNew())
			return null;
		return $model;
	}
	
	public function getORM() {
		return new ORM($this->model);
	}
	
	public static function myORM($model) {
		if($model->isNew())
			return $this->getORM();
		else
			return $this->getORM()->where(array('id' => $model->id));
	}
	
	public static function getTranslationTable($model) {
		// $model =  $this->model;
		return $model::getTable().'_translation';
	}

	public static function getTable($modelName) {
		return Config::get('database', 'prefix').$modelName::getModelName();
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
	
	public static function loadRelationships($model) {
		$model_relationships = $model::$relationships;
		
		if(is_array($model_relationships))
			foreach($model_relationships as $relationship => $params)
				#todo and hasOne ?
				if($params['type'] == 'belongsTo') {
					$rel = ORMHandler::relationData($model::getClassName(), $relationship);
					$model::addProperty($rel['link'], array('type' => 'integer', 'required' => (isset($params['required']) && $params['required']), 'editable'=>false));
				}
	}
	
	public static function getI18N($model, $lang) {
		$dal = new DAL(static::getTranslationTable($model));
		return $dal->where(array('id' => $model->id))->where(array('locale'=>$lang))->first();
	}
	
	public function destroyOne($id) {
		$modelName = $this->model;
		if($model = $modelName::load($id))
			return $model->destroy();
		return false;
	}
	
	public static function fetch($model, $name, $lang=null) {
		if($model::property($name)->i18n) {
			if(!($res = static::getI18N($model, $lang)))
				return null;
			unset($res['id']);
			unset($res['locale']);
			foreach($res as $k=>$v)
				$model->set($k, $v, $lang);
				
			if(isset($model->data['properties'][$name][$lang]))
				return $model->data['properties'][$name][$lang];
		}
	}

	public function relation($model, $name) {
		$rel = static::relationData($model, $name);
		$relation_type = $rel['type'];
		$relmodel = $rel['model'];
		
		switch($relation_type) {
			case 'hasOne':
				if($model->isNew())
					return null;
					
				$link = $rel['link'];
				return $relmodel::where(array($link => $model->id))->first();
			case 'belongsTo':
				if($model->isNew())
					return null;
					
				$link = $rel['link'];
				return $relmodel::where(array('id' => $model->$link))->first();
			case 'hasMany':
			case 'HMABT':
				if($model->isNew())
					return array();
					
				$collection = new CollectionORM($model, $name);
				return $collection;
			default:	
				throw new \Exception('Relation '.$relation_type.' does not exist.');
		}
	}

	public function callStatic($name, $args) {	
		if(strpos($name, 'loadBy') === 0) {
			preg_match('/^loadBy(.*)/', $name, $matches);
			$property = $matches[1];
			$val = $args[0];
			return $this->getORM()->where(array($property => $val))->first();
		}
		else {
			if(method_exists('Coxis\Core\ORM\ORM', $name)) {
				$orm = $this->getORM();
				return call_user_func_array(array($orm, $name), $args);
			}
		}
	}

	public function construct($model, $id) {
		if(!is_int($id))
			return;

		$res = $this->getORM()->where(array('id' => $id))->dal()->first();
		if($res) {
			$model->set($res);
			$model->_is_loaded = true;
		}
	}

	public function destroy($model) {
		return static::myORM($model)->delete();
	}

	public function save($model) {
		$vars = $model->toArray();
		
		#apply filters before saving
		foreach($vars as $col => $var) {
			if($model::property($col)->filter) {
				$filter = $model::property($col)->filter['to'];
				$vars[$col] = $model::$filter($var);
			}
			else {
				if($model::property($col)->i18n)
					foreach($var as $k=>$v)
						$vars[$col][$k] = $model::property($col)->serialize($v);
				else
					$vars[$col] = $model::property($col)->serialize($var);
			}
		}
		
		//Persist local id field
		foreach($model::$relationships as $relationship => $params) {
			if(!isset($model->data[$relationship]))
				continue;
			$rel = $model::relationData($model, $relationship);
			$type = $rel['type'];
			if($type == 'belongsTo') {
				$link = $rel['link'];
				$vars[$link] = $model->data[$relationship];
			}
		}
		
		//Persist i18n
		$values = array();
		$i18n = array();
		foreach($vars as $p => $v) {
			if($model::property($p)->i18n)
				foreach($v as $lang=>$lang_value)
					$i18n[$lang][$p] = $lang_value;
			else
				$values[$p] = $v;
		}

		//Persist
		$orm = $this->getORM();
		//new
		if(!isset($model->id) || !$model->id)
			$model->id = $orm->insert($values);
		//existing
		elseif(sizeof($vars) > 0) {
			if(!$orm->where(array('id'=>$model->id))->update($values))
				$model->id = $orm->insert($values);
		}		
		
		//Persist i18n
		foreach($i18n as $lang=>$values) {
			$dal = new DAL(static::getTranslationTable($model));
			if(!$dal->where(array('id'=>$model->id, 'locale'=>$lang))->update($values))
				$dal->insert(
					array_merge(
						$values, 
						array(
							'locale'=>$lang,
							'id'=>$model->id,
						)
					)
				);
		}
	
		//Persist relationships
		foreach($model::$relationships as $relationship => $params) {
			if(!isset($model->data[$relationship]))
				continue;
			$rel = $model::relationData($model, $relationship);
			$type = $rel['type'];
				
			if($type == 'hasOne') {
				$relation_model = $rel['model'];
				$link = $rel['link'];
				$relation_model::where(array($link => $model->id))->update(array($link => 0));
				$relation_model::where(array('id' => $model->data[$relationship]))->update(array($link => $model->id));
			}
			elseif($type == 'hasMany' || $type == 'HMABT')
				$model->$relationship()->sync($model->data[$relationship]);
		}
	}
}