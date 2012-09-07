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
		$modelName::loadRelationships();
	}

	public function __call($name, $args) {
		// if(strpos($name, 'loadBy') === 0) {
		// 	preg_match('/^loadBy(.*)/', $name, $matches);
		// 	$property = $matches[1];
		// 	$val = $arguments[0];
		// 	return $this->getORM()->where(array($property => $val))->first();
		// }
		// else {
			$orm = $this->getORM();
			// if(method_exists($orm, $name))
				return call_user_func_array(array($orm, $name), $args);
		// }
	}

	public function loadFromID($id) {
		$res = $this->getORM()->where(array('id' => $id))->dal()->first();
		if($res) {
			$this->set($res);
			return true;
		}
		return false;
	}
	
	public function getORM() {
		return new ORM($this->model);
	}
	
	public function getTranslationTable() {
		$model =  $this->model;
		return $model::getTable().'_translation';
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
	
	public static function myORM($model) {
		if($model->isNew())
			return static::getORM();
		else
			return static::getORM()->where(array('id' => $model->id));
	}
	
	public static function getI18N($model, $lang) {
		$dal = new DAL($model->getTranslationTable());
		return $dal->where(array('id' => $model->id))->where(array('locale'=>$lang))->first();
	}
	
	// public static function destroy($model) {
		// parent::destroy();
		
	// 	//todo delete all cascade models and files
	// 	return $model->myORM()->delete();
	// }
	
	public function destroyOne($id) {
		$modelName = $this->model;
		if($model = $modelName::load($id))
			return $model->destroy();
		return false;
	}
	
	public static function fetch($model, $name, $lang=null) {
		// d();
		if($model::property($name)->i18n) {
			if(!($res = $model->getI18N($lang)))
				return null;
			unset($res['id']);
			unset($res['locale']);
			foreach($res as $k=>$v)
				$model->{'set'.$k}($v, $lang);
				
			if(isset($model->data['properties'][$name][$lang]))
				return $model->data['properties'][$name][$lang];
		}
	}
}