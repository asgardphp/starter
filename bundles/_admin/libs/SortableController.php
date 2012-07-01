<?php
class SortableController extends Controller {
	public function sortableactionsAction($model) {
	#todo replace model name below..
	#actualites/'.$model->id.'/promote
	#actualites/'.$model->id.'/demote
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
		
		#todo url_for to get relative url..?
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