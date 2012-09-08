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
		$modelName::hookCallStatic('getTable', function() use($modelName) {
			return ORMHandler::getTable($modelName);
		});

		$ormHandler = new ORMHandler($modelName);

		$modelName::on('construct', function($model, $id) use($ormHandler) {
			$ormHandler->construct($model, $id);
		});

		#Article::load(2)
		$modelName::hookCallStatic('load', function($id) use($ormHandler) {
			return $ormHandler->load($id);
		});

		#Article::destroyOne(2)
		$modelName::hookCallStatic('destroyOne', function($id) use($ormHandler) {
			return $ormHandler->destroyOne($id);
		});

		#$article->destroy()
		$modelName::on('destroy', function($model) use($ormHandler) {
			//todo delete all cascade models and files
			$ormHandler->destroy($model);
		});

		#$article->save()
		$modelName::on('save', function($model) use($ormHandler) {
			$ormHandler->save($model);
		});

		#$article->isNew()
		$modelName::hookCall('isNew', function($model) use($ormHandler) {
			return $ormHandler->isNew($model);
		});
		
		#$article->title
		$modelName::on('get', function($model, $name, $lang, $res) use($ormHandler) {
			if($res === null)
				return ORMHandler::fetch($model, $name, $lang);
			return $res;
		});

		#Article::where() / ::limit() / ::orderBy() / ..
		$modelName::on('__callStatic', function($name, $args) use($ormHandler) {
			return $ormHandler->callStatic($name, $args);
		});

		#Relations
		$modelName::hookCall('relation', function($model, $name) use($ormHandler) {
			return $ormHandler->relation($model, $name);
		});

		$modelName::on('__get', function($model, $name) use($ormHandler) {
			if(array_key_exists($name, $model::$relationships)) {
				$res = $model->relation($name);
				if($res instanceof \Coxis\Core\Collection)
					return $res->get();
				else
					return $res;
			}
		});

		$modelName::on('__call', function($model, $name) use($ormHandler) {
			if(array_key_exists($name, $model::$relationships))
				return $model->relation($name);
		});
	}
}