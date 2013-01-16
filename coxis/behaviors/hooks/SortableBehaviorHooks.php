<?php
namespace Coxis\Behaviors\Hooks;

class SortableBehaviorHooks extends \Coxis\Hook\HooksContainer {
	/**
	@Hook('behaviors_load_sortable')
	**/
	public function behaviors_load_sortableAction($modelDefinition) {
		$modelDefinition->meta['order_by'] = 'position ASC';
		$modelDefinition->addProperty('position', array('type' => 'integer', 'default'=>0, 'required' => false, 'editable' => false));
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
			\Coxis\Hook\HooksContainer::addHook('coxis_'.$modelName.'_actions', array('\Coxis\Behaviors\Controllers\SortableBehaviorHooks', 'sortable'));
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

	public function sortable($model) {
		return '<a href="'.\URL::url_for('coxis_'.get_class($model).'_promote', array('id' => $model->id), false).'">'.__('Promote').'</a> | <a href="'.\URL::url_for('coxis_'.get_class($model).'_demote', array('id' => $model->id), false).'">'.__('Demote').'</a> | ';
	}
}