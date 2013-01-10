<?php
namespace Coxis\ORM\Hooks;

class ORMBehaviorHooks extends \Coxis\Core\HooksContainer {
	/**
	@Hook('behaviors_pre_load')
	**/
	public function behaviors_pre_loadAction($modelDefinition) {
		if(!isset($modelDefinition->behaviors['orm']))
			$modelDefinition->behaviors['orm'] = true;
	}

	/**
	@Hook('behaviors_load_orm')
	**/
	public function behaviors_load_ormAction($modelDefinition) {
		Profiler::checkpoint('Loading ORM : start');
		$modelName = $modelDefinition->getClass();

		#todo rename hook as there is now variable to hook
		$modelDefinition->hookOn('callStatic', function($chain, $name, $args) use($modelName) {
			if($name == 'getTable') {
				$chain->found = true;
				return \Coxis\ORM\Libs\ORMHandler::getTable($modelName);
			}
		});

		$ormHandler = new \Coxis\ORM\Libs\ORMHandler($modelDefinition);

		$modelDefinition->hookOn('constrains', function($chain, &$constrains) use($modelName) {
			foreach($modelName::getDefinition()->relations() as $name=>$relation) {
				if(isset($relation['required']) && $relation['required'])
					$constrains[$name]['required'] = true;
			}
		});

		$modelDefinition->hookOn('callStatic', function($chain, $name, $args) use($ormHandler) {
			$res = null;
			#Article::load(2)
			if($name == 'load') {
				$chain->found = true;
				return $ormHandler->load($args[0]);#id
			}
			#Article::destroyOne(2)
			elseif($name == 'destroyOne') {
				$chain->found = true;
				return $ormHandler->destroyOne($args[0]);#id
			}
			#Article::orm()
			elseif($name == 'orm') {
				$chain->found = true;
				return $ormHandler->getORM();
			}
			#Article::loadByName()
			elseif(strpos($name, 'loadBy') === 0) {
				$chain->found = true;
				preg_match('/^loadBy(.*)/', $name, $matches);
				$property = $matches[1];
				$val = $args[0];
				return $ormHandler->getORM()->where(array($property => $val))->first();
			}
			#Article::where() / ::limit() / ::orderBy() / ..
			elseif(method_exists('Coxis\ORM\Libs\ORM', $name)) {
				$chain->found = true;
				return call_user_func_array(array($ormHandler->getORM(), $name), $args);
			}
		});

		$modelDefinition->hookOn('call', function($chain, $model, $name, $args) use($ormHandler) {
			$res = null;
			#$article->isNew()
			if($name == 'isNew') {
				$chain->found = true;
				$res = $ormHandler->isNew($model);
			}
			#$article->isOld()
			elseif($name == 'isOld') {
				$chain->found = true;
				$res = $ormHandler->isOld($model);
			}
			#Relations
			elseif($name == 'relation') {
				$chain->found = true;
				$res = $ormHandler->relation($model, $args[0]);#relation name
			}
			#Relation properties
			elseif($name == 'getRelationProperty') {
				$chain->found = true;
				$res = $ormHandler->getRelationProperty($model, $args[0]);#relation name
			}
			elseif(array_key_exists($name, $model::$relations)) {
				$chain->found = true;
				$res = $model->relation($name);
			}
			return $res;
		});

		$modelDefinition->hookBefore('validation', function($chain, $model, &$data, &$errors) {
			foreach($model::getDefinition()->relations() as $name=>$relation) {
				if(isset($model->data[$name]))
					$data[$name] = $model->data[$name];
				else
					$data[$name] = $model->$name;#todo only use ids and not models
			}
		});

		$modelDefinition->hookOn('construct', function($chain, $model, $id) use($ormHandler) {
			$ormHandler->construct($chain, $model, $id);
		});

		#$article->destroy()
		$modelDefinition->hookOn('destroy', function($chain, $model) use($ormHandler) {
			//todo delete all cascade models and files
			$ormHandler->destroy($model);
		});

		#$article->save()
		$modelDefinition->hookOn('save', function($chain, $model) use($ormHandler) {
			$ormHandler->save($model);
		});
		
		#$article->title
		$modelDefinition->hookAfter('get', function($chain, $model, $name, $lang) {
			return \Coxis\ORM\Libs\ORMHandler::fetch($model, $name, $lang);
		});

		$modelDefinition->hookBefore('get', function($chain, $model, $name, $lang) {
			#todo hasRelation
			if(array_key_exists($name, $model::getDefinition()->relations())) {
				$rel = $model->relation($name);
				if($rel instanceof \Coxis\Core\Collection)
					return $rel->get();
				else
					return $rel;
			}
		});
		Profiler::checkpoint('Loading ORM : end');
	}
}