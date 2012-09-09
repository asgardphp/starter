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
		#todo rename filter as there is now variable to filter
		$modelName::filterOn('callStatic', function($chain, $name, $args) use($modelName) {
			if($name == 'getTable') {
				$chain->found = true;
				return ORMHandler::getTable($modelName);
			}
		});

		$ormHandler = new ORMHandler($modelName);

		$modelName::filterOn('callStatic', function($chain, $name, $args) use($ormHandler) {
			$res = null;
			#Article::load(2)
			if($name == 'load')
				$res = $ormHandler->load($args[0]);#id
			#Article::destroyOne(2)
			elseif($name == 'destroyOne')
				return $ormHandler->destroyOne($args[0]);#id
			#Article::where() / ::limit() / ::orderBy() / ..
			else
				$res = $ormHandler->callStatic($name, $args);
			if($res !== null) {
				$chain->found = true;
				return $res;
			}
		});

		$modelName::filterOn('call', function($chain, $model, $name, $args) use($ormHandler) {
			$res = null;
			#$article->isNew()
			if($name == 'isNew')
				$res = $ormHandler->isNew($model);
			#Relations
			elseif($name == 'relation')
				$res = $ormHandler->relation($model, $args[0]);#relation name
			elseif(array_key_exists($name, $model::$relationships))
				return $model->relation($name);
			if($res !== null) {
				$chain->found = true;
				return $res;
			}
		});


		$modelName::filterOn('construct', function($chain, $model, $id) use($ormHandler) {
			$ormHandler->construct($model, $id);
		});

		#$article->destroy()
		$modelName::filterOn('destroy', function($chain, $model) use($ormHandler) {
			//todo delete all cascade models and files
			$ormHandler->destroy($model);
		});

		#$article->save()
		$modelName::filterOn('save', function($chain, $model) use($ormHandler) {
			$ormHandler->save($model);
		});
		
		#$article->title
		$modelName::filterAfter('get', function($chain, $model, $name, $lang, &$res) {
			$res = ORMHandler::fetch($model, $name, $lang);
			if($res !== null)
				$chain->stop();
		});

		$modelName::filterBefore('get', function($chain, $model, $name, $lang, &$res) {
			if(array_key_exists($name, $model::$relationships)) {
				$rel = $model->relation($name);
				if($rel instanceof \Coxis\Core\Collection)
					$res = $rel->get();
				else
					$res = $rel;
				$chain->stop();
			}
		});
	}
}