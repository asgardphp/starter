<?php
namespace Coxis\Core\ORM;

abstract class ModelORM extends \Coxis\Core\Model {
	public static function _autoload() {
		if(static::getClassName() == 'coxis\core\orm\modelorm')
			return;
		static::loadModel();
		parent::post_configure();
	}
	
	public static function loadModel() {
		if(!isset(static::$meta['order_by']))
			static::$meta['order_by'] = 'id DESC';
		
		static::$properties = array_merge(
			array('id' => 
				array(
					'type' => 'text', 
					'editable'=>false, 
					'required'=>false,
					'orm'	=>	array(
						'type'	=>	'int(11)',
						'auto_increment'	=>	true,
						'key'	=>	'PRI',
						'nullable'	=>	false,
					),
				)), 
			static::$properties
		);
		
		parent::loadModel();
		static::loadRelationships();
	}
	
	public static function loadRelationships() {
		$model_relationships = static::$relationships;
		
		if(is_array($model_relationships))
			foreach($model_relationships as $relationship => $params)
				#todo and hasOne ?
				if($params['type'] == 'belongsTo') {
					$rel = static::relationData(static::getClassName(), $relationship);
					static::addProperty($rel['link'], array('type' => 'integer', 'required' => (isset($params['required']) && $params['required']), 'editable'=>false));
				}
	}
	
	public static function getORM() {
		return new ORM(static::getClassName());
	}
	
	public function myORM() {
		if($this->isNew())
			return static::getORM();
		else
			return static::getORM()->where(array('id' => $this->id));
	}
	
	public function getI18N($lang) {
		$dal = new DAL($this->getTranslationTable());
		return $dal->where(array('id' => $this->id))->where(array('locale'=>$lang))->first();
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
	
	public static function __callStatic($name, $arguments) {
		if(strpos($name, 'loadBy') === 0) {
			preg_match('/^loadBy(.*)/', $name, $matches);
			$property = $matches[1];
			$val = $arguments[0];
			return static::getORM()->where(array($property => $val))->first();
		}
		else {
			$orm = static::getORM();
			if(method_exists($orm, $name))
				return call_user_func_array(array($orm, $name), $arguments);
		}
		throw new \Exception('The method "'.$name.'" does not exist for model "'.static::getClassName().'"');
	}
	
	public static function getTable() {
		return Config::get('database', 'prefix').static::getModelName();
	}
	
	public static function getTranslationTable() {
		return static::getTable().'_translation';
	}
	
	/* IMPLEMENTS */
	public function loadFromID($id) {
		//~ d(static::getORM()->where(array('id' => $id))->dal()->buildSQL());
		$res = static::getORM()->where(array('id' => $id))->dal()->first();
		if($res) {
			$this->loadFromArray($res);
			return true;
		}
		return false;
	}
	
	public function loadFromArray($cols) {
		//~ d($cols);
		foreach($cols as $col=>$value) {
			if(isset(static::$properties[$col]['filter'])) {
				$filter = static::$properties[$col]['filter']['from'];
				$this->data['properties'][$col] = $model::$filter($value);
			}
			elseif(isset(static::$properties[$col]['type'])) {
				if(static::$properties[$col]['type'] === 'array') {#php, seriously.. == 'array'
					try {
						$this->data['properties'][$col] = unserialize($value);
					} catch(\ErrorException $e) {
						$this->data['properties'][$col] = array($value);
					}
					if(!is_array($this->$col))
						$this->data['properties'][$col] = array();
				}
				elseif(static::$properties[$col]['type'] === 'date')
					$this->data['properties'][$col] = \Coxis\Core\Tools\Date::fromDatetime($value);
				elseif(static::$properties[$col]['type'] === 'datetime')
					$this->data['properties'][$col] = \Coxis\Core\Tools\Datetime::fromDatetime($value);
				else
					$this->data['properties'][$col] = $value;
			}
			else
				$this->data['properties'][$col] = $value;
		}
		
		return $this;
	}
	
	public function dosave() {
		parent::dosave();
	
		$vars = $this->getVars();
		
		#apply filters before saving
		foreach($vars as $col => $var) {
			if(isset(static::$properties[$col]['filter'])) {
				$filter = static::$properties[$col]['filter']['to'];
				$vars[$col] = static::$filter($var);
			}
			elseif(isset(static::$properties[$col]['type'])) {
				if(static::$properties[$col]['type']=='array')
					$vars[$col] = serialize($var);
				elseif(static::$properties[$col]['type']=='date' || static::$properties[$col]['type']=='datetime')
					$vars[$col] = $var->datetime();
			}
		}
		
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
		
		//Persist i18n
		$values = array();
		$i18n = array();
		foreach($vars as $p => $v) {
			if(isset(static::$properties[$p]['i18n']) && static::$properties[$p]['i18n'])
				foreach($v as $lang=>$lang_value)
					$i18n[$lang][$p] = $lang_value;
			else
				$values[$p] = $v;
		}
		
		//Persist
		$orm = static::getORM();
		//new
		if(!isset($this->id))
			$this->id = $orm->insert($values);
		//existing
		elseif(sizeof($vars) > 0) {
			//~ d($values, $this->data);
			if(!$orm->where(array('id'=>$this->id))->update($values))
				$this->id = $orm->insert($values);
		}		
		
		//Persist i18n
		foreach($i18n as $lang=>$values) {
			$dal = new DAL(static::getTranslationTable());
			if(!$dal->where(array('id'=>$this->id, 'locale'=>$lang))->update($values))
				$dal->insert(
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
		parent::destroy();
		
		//todo delete all cascade models and files
		return $this->myORM()->delete();
	}
	
	public static function destroyOne($id) {
		if($model = static::load($id))
			return $model->destroy();
		return false;
	}
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
					
				$collection = new CollectionORM($this, $name);
				return $collection;
			default:	
				throw new \Exception('Relation '.$relation_type.' does not exist.');
		}
	}
	
	public function fetch($name, $lang=null) {
		if(isset(static::$properties[$name]['i18n']) && static::$properties[$name]['i18n']) {
			if(!($res = $this->getI18N($lang)))
				return null;
			unset($res['id']);
			unset($res['locale']);
			foreach($res as $k=>$v)
				$this->{'set'.$k}($v, $lang);
				
			if(isset($this->data['properties'][$name][$lang]))
				return $this->data['properties'][$name][$lang];
		}
	}
}