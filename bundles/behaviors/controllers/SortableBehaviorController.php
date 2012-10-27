<?php
namespace Coxis\Bundles\Behaviors\Controllers;

class SortableBehaviorController extends \Coxis\Core\Controller {
	/**
	@Hook('behaviors_load_sortable')
	**/
	public function behaviors_load_sortableAction($modelDefinition) {
		$modelDefinition->meta['order_by'] = 'position ASC';
		$modelDefinition->addProperty('position', array('type' => 'integer', 'required' => true, 'editable' => false));
	}
	
	/**
	@Hook('behaviors_coxisadmin_sortable')
	*/
	public function behaviors_coxisadmin_sortableAction($admin_controller) {
		$admin_controller .= 'Controller';
		$modelName = $admin_controller::getModel();
		
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
			\Coxis\Core\Controller::hookOn('coxis_'.$modelName.'_actions', array('controller' => '\Coxis\Bundles\Behaviors\Controllers\SortableBehavior', 'action' => 'sortableactions'));
		} catch(\Exception $e) {}#if the admincontroller does not exist for this model
	}
	
	/**
	@Hook('behaviors_presave_sortable')
	**/
	public function behaviors_presave_sortableAction($model) {
		if($model->isNew()) {
			try {
				$last = $model::orderBy('position ASC')->first();
				$model->position = $last->position+1;
			} catch(\Exception $e) {
				$model->position = 0;
			}
		}
	}
	
	public function sortableactionsAction($model) {
		return '<a href="'.\URL::url_for('coxis_'.get_class($model).'_promote', array('id' => $model->id), false).'">'.__('Promote').'</a> | <a href="'.\URL::url_for('coxis_'.get_class($model).'_demote', array('id' => $model->id), false).'">'.__('Demote').'</a> | ';
	}

	public function promoteAction($request) {
		$controller = $request['_controller'].'Controller';
		$modelName = $controller::getModel();
		$model = $modelName::load($request['id']);
		static::reset($modelName);
		
		try {
			$over_model = $modelName::where(array('position < ?'=>$model->position))->orderBy('position DESC')->first();
			
			$old = $model->position;
			$model->position = $over_model->position;
			$over_model->position = $old;
			$model->save(null, true);
			$over_model->save(null, true);
			\Flash::addSuccess('Ordre modifié avec succès.');
		} catch(\Exception $e) {}
		
		return \Response::redirect(\URL::url_for(array($request['_controller'], 'index')));
	}
	
	public function demoteAction($request) {
		$controller = $request['_controller'].'Controller';
		$modelName = $controller::getModel();
		$model = $modelName::load($request['id']);
		static::reset($modelName);
		
		try {
			$below_model = $modelName::where(array('position > ?'=>$model->position))->orderBy('position ASC')->first();
			
			$old = $model->position;
			$model->position = $below_model->position;
			$below_model->position = $old;
			$model->save(null, true);
			$below_model->save(null, true);
			\Flash::addSuccess('Ordre modifié avec succès.');
		} catch(\Exception $e) {}
		
		return \Response::redirect(\URL::url_for(array($request['_controller'], 'index')));
	}
	
	public static function reset($modelName) {
		$all = $modelName::orderBy('position ASC')->all();
		
		#reset positions
		foreach($all as $i=>$one_model) {
			$one_model->position = $i;
			$one_model->save(null, true);
		}
	}
}