<?php
namespace Coxis\Bundles\Behaviors\Controllers;

class ORMBehaviorController extends \Coxis\Core\Controller {
	/**
	@Hook('behaviors_pre_load')
	**/
	public function behaviors_pre_loadAction($model) {
		if(!isset($model::$behaviors['orm']))
			$model::$behaviors['orm'] = true;
	}

	/**
	@Hook('behaviors_load_orm')
	**/
	public function behaviors_load_ormAction($modelName) {
		$modelName::hookCallStatic('loadRelationships', function() use($modelName) {
			return ORMHandler::loadRelationships($modelName);
		});

		$modelName::hookCallStatic('getTable', function() use($modelName) {
			return Config::get('database', 'prefix').$modelName::getModelName();
		});

		$ormHandler = new ORMHandler($modelName);

		$modelName::hookCallStatic('orderBy', function($by) use($ormHandler) {
			return $ormHandler->orderBy($by);
		});

		$modelName::hookCallStatic('where', function($conditions) use($ormHandler) {
			return $ormHandler->where($conditions);
		});

		$modelName::hookCallStatic('destroyOne', function($id) use($ormHandler) {
			return $ormHandler->destroyOne($id);
		});

		$modelName::hookCallStatic('getORM', function() use($ormHandler) {
			return $ormHandler->getORM();
		});

		$modelName::hookCallStatic('getTranslationTable', function() use($ormHandler) {
			return $ormHandler->getTranslationTable();
		});

		$modelName::hookCall('myORM', function($model) use($ormHandler) {
			return $ormHandler->myORM($model);
		});

		$modelName::hookCall('getI18N', function($model, $lang) use($ormHandler) {
			return $ormHandler->getI18N($model, $lang);
		});

		$modelName::hookCall('loadFromID', function($model, $id) use($ormHandler) {
			$res = $ormHandler->getORM()->where(array('id' => $id))->dal()->first();
			if($res) {
				$model->set($res);
				return true;
			}
			return false;
		});

		$modelName::hookCall('fetch', function($model, $name, $lang=null) use($ormHandler) {
			return $ormHandler->fetch($model, $name, $lang);
		});

		$modelName::hookCall('getRelation', function($model, $name) use($ormHandler) {
			$rel = ORMHandler::relationData($model, $name);
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
		});

		$modelName::on('destroy', function($model) use($modelName) {
			//todo delete all cascade models and files
			return $model->myORM()->delete();
		});

		$modelName::on('save', function($model) use($modelName) {
			$vars = $model->getVars();
			
			#apply filters before saving
			foreach($vars as $col => $var) {
				if($modelName::property($col)->filter) {
					$filter = $modelName::property($col)->filter['to'];
					$vars[$col] = $modelName::$filter($var);
				}
				else {
					if($modelName::property($col)->i18n)
						foreach($var as $k=>$v)
							$vars[$col][$k] = $modelName::property($col)->serialize($v);
					else
						$vars[$col] = $modelName::property($col)->serialize($var);
				}
			}
			
			//Persist local id field
			foreach($modelName::$relationships as $relationship => $params) {
				if(!isset($model->data[$relationship]))
					continue;
				$rel = $modelName::relationData($model, $relationship);
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
				if($modelName::property($p)->i18n)
					foreach($v as $lang=>$lang_value)
						$i18n[$lang][$p] = $lang_value;
				else
					$values[$p] = $v;
			}
			
			//Persist
			$orm = $modelName::getORM();
			//new
			if(!isset($model->id))
				$model->id = $orm->insert($values);
			//existing
			elseif(sizeof($vars) > 0) {
				if(!$orm->where(array('id'=>$model->id))->update($values))
					$model->id = $orm->insert($values);
			}		
			
			//Persist i18n
			foreach($i18n as $lang=>$values) {
				$dal = new DAL($modelName::getTranslationTable());
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
			foreach($modelName::$relationships as $relationship => $params) {
				if(!isset($model->data[$relationship]))
					continue;
				$rel = $modelName::relationData($model, $relationship);
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
		});
	}
}