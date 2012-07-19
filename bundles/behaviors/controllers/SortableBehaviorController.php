<?php
namespace Coxis\Bundles\Behaviors\Controllers;

class SortableBehaviorController extends Controller {
	/**
	@Hook('behaviors_load_sortable')
	**/
	public function behaviors_load_sortableAction($modelName) {
		$modelName::addProperty('position', array('type' => 'integer', 'required' => true, 'editable' => false));
	}
	
	/**
	@Hook('behaviors_coxisadmin_sortable')
	*/
	public function behaviors_coxisadmin_sortableAction($admin_controller) {
		$admin_controller .= 'Controller';
		$modelName = strtolower(basename($admin_controller::getModel()));
		
		try {
			$admin_controller::addHook(array(
				'route'			=>	':id/promote',
				'name'			=>	'coxis_'.$modelName.'_promote',
				'controller'	=>	'SortableBehavior',
				'action'			=>	'promote'
			));
			$admin_controller::addHook(array(
				'route'			=>	':id/demote',
				'name'			=>	'coxis_'.$modelName.'_demote',
				'controller'	=>	'SortableBehavior',
				'action'			=>	'demote'
			));
		
			Event::$hooks_table['coxis_'.$modelName.'_actions'][] = array('controller' => 'SortableBehavior', 'action' => 'sortableactions');
		} catch(\Exception $e) {}#if the admincontroller does not exist for this model
	}
	
	/**
	@Hook('behaviors_presave_sortable')
	**/
	public function behaviors_presave_sortableAction($model) {
		if($model->isNew()) {
			try {
				$last = Database::getInstance()->query('SELECT position FROM `'.Config::get('database', 'prefix').$model::getModelName().'` ORDER BY position ASC LIMIT 1')->fetchOne();
				$model->position = $last['position']+1;
			} catch(\Exception $e) {
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
		return '<a href="'.url_for('coxis_'.$model->getClassName().'_promote', array('id' => $model->id), false).'">Monter</a> | <a href="'.url_for('coxis_'.$model->getClassName().'_demote', array('id' => $model->id), false).'">Descendre</a> | ';
	}

	public function promoteAction($request) {
		$controller = $request['_controller'].'Controller';
		$modelName = $controller::getModel();
		$model = $modelName::load($request['id']);
		static::reset($modelName);
		
		try {
			$over_model = $modelName::findOne(array(
				'conditions'	=>	array(
					'position < ?'	=>	array($model->position),
				),
				'order_by'	=>	'position DESC'
			));
			
			$old = $model->position;
			$model->position = $over_model->position;
			$over_model->position = $old;
			$model->save(null, true);
			$over_model->save(null, true);
			Messenger::addSuccess('Ordre modifié avec succès.');
		} catch(\Exception $e) {}
		
		Response::redirect(url_for(array($request['_controller'], 'index')))->send();
	}
	
	public function demoteAction($request) {
		$controller = $request['_controller'].'Controller';
		$modelName = $controller::getModel();
		$model = $modelName::load($request['id']);
		static::reset($modelName);
		
		try {
			$below_model = $modelName::findOne(array(
				'conditions'	=>	array(
					'position > ?'	=>	array($model->position),
				),
				'order_by'	=>	'position ASC'
			));
			
			$old = $model->position;
			$model->position = $below_model->position;
			$below_model->position = $old;
			$model->save(null, true);
			$below_model->save(null, true);
			Messenger::addSuccess('Ordre modifié avec succès.');
		} catch(\Exception $e) {}
		
		Response::redirect(url_for(array($request['_controller'], 'index')))->send();
	}
	
	public static function reset($modelName) {
		$all = $modelName::find(array(
			'order_by'	=>	'position ASC'
		));
		
		#reset positions
		foreach($all as $i=>$one_model) {
			$one_model->position = $i;
			$one_model->save(null, true);
		}
	}
}