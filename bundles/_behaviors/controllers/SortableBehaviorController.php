<?php
class SortableBehaviorController extends Controller {
	/**
	@Hook('behaviors_load_sortable')
	**/
	public function behaviors_load_sortableAction($modelName) {
		$modelName::addProperty('position', array('type' => 'integer', 'required' => true, 'editable' => false));
		#todo controller name
		//~ $modelNameName = static::getModelName();
		$admin_controller = strtolower(CoxisAdmin::getAdminControllerFor($modelName));
		try {
			$index = CoxisAdmin::getIndexFor($modelName);
			Coxis::$controller_hooks[$admin_controller][] = array(
						'route'			=>	$index.'/:id/promote',
						'name'			=>	'coxis_'.$modelName.'_promote',
						'controller'	=>	'SortableBehavior',
						'action'			=>	'promote'
					);
			Coxis::$controller_hooks[$admin_controller][] = array(
						'route'			=>	$index.'/:id/demote',
						'name'			=>	'coxis_'.$modelName.'_demote',
						'controller'	=>	'SortableBehavior',
						'action'			=>	'demote'
					);
			Coxis::$hooks_table['coxis_'.$modelName.'_actions'][] = array('controller' => 'SortableBehavior', 'action' => 'sortableactions');
		}
		catch(Exception $e) {
		}
	}
	
	/**
	@Hook('behaviors_presave_sortable')
	**/
	public function behaviors_presave_sortableAction($model) {
		if($model->isNew()) {
			try {
				$last = Database::getInstance()->query('SELECT position FROM `'.Config::get('database', 'prefix').$model::getModelName().'` ORDER BY position ASC LIMIT 1')->fetchOne();
				$model->position = $last['position']+1;
			}
			catch(Exception $e) {
				$model->position = 0;
			}
		}
	}
	
	/**
	@Filter('find_model')
	**/
	public function find_modelAction($args) {
		list($order_by, $modelName) = $args;
		
		if(in_array('sortable', array_keys($modelName::$behaviors)))
			return ' ORDER BY position ASC';
		else
			return '';
	}
	
	public function sortableactionsAction($model) {
		return '<a href="'.url_for('coxis_'.$model->getModelName().'_promote', array('id' => $model->id), false).'">Promote</a> | <a href="'.url_for('coxis_'.$model->getModelName().'_demote', array('id' => $model->id), false).'">Demote</a> | ';
	}

	public function promoteAction($request) {
		$modelName = CoxisAdmin::getModelNameFor($request['_controller']);
		
		$model = $modelName::load($request['id']);
		
		static::reset($modelName);
		
		try {
			$over_model = $modelName::findOne(array(
				'conditions'	=>	array(
					'position < ?'	=>	array($model->position),
				),
				'order_by'	=>	'position DESC'
			));
			//~ d($over_models);
			
			$old = $model->position;
			$model->position = $over_model->position;
			$over_model->position = $old;
			$model->save(null, true);
			$over_model->save(null, true);
			Messenger::addSuccess('Ordre modifié avec succès.');
		}
		catch(Exception $e) {
		}
		
		Response::redirect(url_for(array($request['_controller'], 'index')))->send();
	}
	
	public function demoteAction($request) {
		$modelName = CoxisAdmin::getModelNameFor($request['_controller']);
		
		$model = $modelName::load($request['id']);
		
		static::reset($modelName);
		
		try {
			$below_model = $modelName::findOne(array(
				'conditions'	=>	array(
					'position > ?'	=>	array($model->position),
				),
				'order_by'	=>	'position ASC'
			));
			//~ d($over_models);
			
			$old = $model->position;
			$model->position = $below_model->position;
			$below_model->position = $old;
			$model->save(null, true);
			$below_model->save(null, true);
			Messenger::addSuccess('Ordre modifié avec succès.');
		}
		catch(Exception $e) {
		}
		
		Response::redirect(url_for(array($request['_controller'], 'index')))->send();
	}
	
	public static function reset($modelName) {
		$all = $modelName::find(array(
			//~ 'order_by'	=>	'position ASC'
		));
		
		#reset positions
		foreach($all as $i=>$one_model) {
			$one_model->position = $i;
			$one_model->save(null, true);
		}
	}
}