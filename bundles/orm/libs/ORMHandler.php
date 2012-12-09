<?php
namespace Coxis\Bundles\ORM\Libs;

class ORMHandler {
	private $model;

	function __construct($modelDefinition) {
		$this->model = $modelDefinition;
		if(!isset($modelDefinition->meta['order_by']))
			$modelDefinition->meta['order_by'] = 'id DESC';
		
		$modelDefinition->addProperty('id', array(
			'type' => 'text', 
			'editable'=>false, 
			'required'=>false,
			'position'	=>	1,
			'orm'	=>	array(
				'type'	=>	'int(11)',
				'auto_increment'	=>	true,
				'key'	=>	'PRI',
				'nullable'	=>	false,
			),
		));	
		static::loadrelations($modelDefinition);
	}

	public function isNew($model) {
		return !(isset($model->data['properties']['id']) && $model->data['properties']['id']);
	}

	public function isOld($model) {
		return !static::isNew($model);
	}

	public function load($id) {
		$modelName = $this->model->getClass();
		$model = new $modelName($id);
		if($model->isNew())
			return null;
		return $model;
	}
	
	public function getORM() {
		return new ORM($this->model->getClass());
	}
	
	public function myORM($model) {
		if($model->isNew())
			return $this->getORM();
		else
			return $this->getORM()->where(array('id' => $model->id));
	}
	
	public static function getTranslationTable($model) {
		return $model::getTable().'_translation';
	}

	public static function getTable($modelName) {
		if(isset($modelName::getDefinition()->meta['table']) && $modelName::getDefinition()->meta['table'])
			return \Config::get('database', 'prefix').$modelName::getDefinition()->meta['table'];
		else
			return \Config::get('database', 'prefix').$modelName::getModelName();
	}
	
	public static function relationData($model, $name) {
		if(is_string($model) || !$model instanceof \Coxis\Core\ModelDefinition)
			$model = $model::getDefinition();
		$relations = $model->relations;
		$relation = $relations[$name];
		
		$res = array();
		$res['type'] = $relation['type'];
		$res['model'] = $relation_model = $relation['model'];
		if($res['type'] == 'hasMany') {
			if(isset($relation['link']))
				$res['link'] = $relation['link'];
			else {
				#todo pouvoir creer une correspondance entre deux relations
				// if(is_string($model))
				// 	$modelName = strtolower($model);
				// else
				// 	$modelName = strtolower(get_class($model));
				$modelName = strtolower($model->getClass());
				$modelName = preg_replace('/^\\\/', '', $modelName);
				// d($relation_model::getDefinition()->relations);
				foreach($relation_model::getDefinition()->relations as $name=>$relation) {
					$rel = static::relationData($relation_model::getDefinition(), $name);
					$relModelClass = preg_replace('/^\\\/', '', strtolower($rel['model']));
					if($rel['type'] == 'belongsTo' && $relModelClass == $modelName) {
						$res['link'] = $rel['link'];
						break;
					}
				}
				if(!isset($res['link']))
					throw new \Exception('Could not find the opposite relation.');
			}
		}
		elseif($res['type'] == 'HMABT') {
			$modelClass = $model->getClass();
			$res['link_a'] = $modelClass::getModelName().'_id';
			$res['link_b'] = $relation_model::getModelName().'_id';
			if(isset($relation['sortable']) && $relation['sortable'])
				$res['sortable'] = $modelClass::getModelName().'_position';
			else
				$res['sortable'] = false;
			if($modelClass::getModelName() < $relation_model::getModelName())
				$res['join_table'] = \Config::get('database', 'prefix').$modelClass::getModelName().'_'.$relation_model::getModelName();
			else
				$res['join_table'] = \Config::get('database', 'prefix').$relation_model::getModelName().'_'.$modelClass::getModelName();
		}
		elseif($res['type'] == 'hasOne')
			$res['link'] = $name.'_id';
		elseif($res['type'] == 'belongsTo')
			$res['link'] = $name.'_id';
		
		return $res;
	}
	
	public static function loadrelations($model) {
		$model_relations = $model->relations();
		
		if(is_array($model_relations))
			foreach($model_relations as $relation => $params)
				#todo and hasOne ?
				if($params['type'] == 'belongsTo' || $params['type'] == 'hasOne') {
					$rel = ORMHandler::relationData($model, $relation);
					$model->addProperty($rel['link'], array('type' => 'integer', 'required' => (isset($params['required']) && $params['required']), 'editable'=>false));
				}
	}
	
	public static function getI18N($model, $lang) {
		$dal = new \Coxis\Core\DB\DAL(static::getTranslationTable($model));
		return $dal->where(array('id' => $model->id))->where(array('locale'=>$lang))->first();
	}
	
	public function destroyOne($id) {
		$modelName = $this->model->getClass();
		if($model = $modelName::load($id)) {
			$model->destroy();
			return true;
		}
		return false;
	}
	
	public static function fetch($model, $name, $lang=null) {
		if(!$model::hasProperty($name))
			return;
		if($model::property($name)->i18n) {
			if(!($res = static::getI18N($model, $lang)))
				return null;
			unset($res['id']);
			unset($res['locale']);

			static::unserializeSet($model, $res, $lang);
				
			if(isset($model->data['properties'][$name][$lang]))
				return $model->data['properties'][$name][$lang];
		}
	}

	public function relation($model, $name) {
		$rel = static::relationData($model->getDefinition(), $name);
		$relation_type = $rel['type'];
		$relmodel = $rel['model'];
		
		switch($relation_type) {
			case 'hasOne':
				if($model->isNew())
					return null;
					
				#todo bug?
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

				$collection = new \Coxis\Bundles\ORM\Libs\CollectionORM($model, $name);
				return $collection;
			default:	
				throw new \Exception('Relation '.$relation_type.' does not exist.');
		}
	}

	public function construct($chain, $model, $id) {
		if(!ctype_digit($id) && !is_int($id))
			return;

		$res = $this->getORM()->where(array('id' => $id))->dal()->first();
		if($res) {
			static::unserializeSet($model, $res);
			$chain->found = true;
		}
	}

	public static function unserializeSet($model, $data, $lang=null) {
		foreach($data as $k=>$v)
			if($model->hasProperty($k))
				$data[$k] = $model->property($k)->unserialize($v);
			else
				unset($data[$k]);
		return $model->set($data, $lang, true);
	}

	public function destroy($model) {
		$orms = array();
		foreach($model->getDefinition()->relations() as $name=>$relation)
			if(isset($relation['cascade']['delete'])) {
				$orm = $model->$name();
				if(!is_object($orm))
					continue;
				$orm->dal()->rsc();
				$orms[] = $orm;
			}

		$r = static::myORM($model)->dal()->delete();

		foreach($orms as $orm)
			$orm->delete();

		return $r;
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
		foreach($model::getDefinition()->relations as $relation => $params) {
			if(!isset($model->data[$relation]))
				continue;
			$rel = ORMHandler::relationData($model, $relation);
			$type = $rel['type'];
			if($type == 'belongsTo') {
				$link = $rel['link'];
				if(is_object($model->data[$relation]))
					$vars[$link] = $model->data[$relation]->id;
				else
					$vars[$link] = $model->data[$relation];
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
			$dal = new \Coxis\Core\DB\DAL(static::getTranslationTable($model));
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
	
		//Persist relations
		foreach($model::getDefinition()->relations as $relation => $params) {
			if(!isset($model->data[$relation]))
				continue;
			$rel = static::relationData($model, $relation);
			$type = $rel['type'];

			if($type == 'hasOne') {
				$relation_model = $rel['model'];
				$link = $rel['link'];
				$relation_model::where(array($link => $model->id))->update(array($link => 0));
				$relation_model::where(array('id' => $model->data[$relation]))->update(array($link => $model->id));
			}
			elseif($type == 'hasMany' || $type == 'HMABT')
				$model->$relation()->sync($model->data[$relation]);
		}
	}
}