<?php
namespace Coxis\Behaviors\Controllers;

class SortableBehaviorController extends \Coxis\Core\Controller {
	public function promoteAction($request) {
		$controller = $request->parentController.'Controller';
		$modelName = $controller::getModel();
		$model = $modelName::load($request['id']);

		static::reset($modelName);
		
		try {
			$separate_by = $model->getDefinition()->meta('separate_by');
			if($separate_by)
				$over_model = $modelName::where(array('position < ?'=>$model->position, $separate_by=>$model->$separate_by))->orderBy('position DESC')->first();
			else
				$over_model = $modelName::where(array('position < ?'=>$model->position))->orderBy('position DESC')->first();
			
			$old = $model->position;
			$model->position = $over_model->position;
			$over_model->position = $old;
			$model->save(null, true);
			$over_model->save(null, true);
			\Flash::addSuccess(__('Ordre modifié avec succès.'));
		} catch(\Exception $e) {}
		
		return \Response::back();
	}
	
	public function demoteAction($request) {
		$controller = $request->parentController.'Controller';
		$modelName = $controller::getModel();
		$model = $modelName::load($request['id']);
		static::reset($modelName);
		
		try {
			$separate_by = $model->getDefinition()->meta('separate_by');
			if($separate_by)
				$below_model = $modelName::where(array('position > ?'=>$model->position, $separate_by=>$model->$separate_by))->orderBy('position ASC')->first();
			else
				$below_model = $modelName::where(array('position > ?'=>$model->position))->orderBy('position ASC')->first();
			
			$old = $model->position;
			$model->position = $below_model->position;
			$below_model->position = $old;
			$model->save(null, true);
			$below_model->save(null, true);
			\Flash::addSuccess(__('Ordre modifié avec succès.'));
		} catch(\Exception $e) {}
		
		return \Response::back();
	}
	
	public static function reset($modelName) {
		$all = $modelName::orderBy('position ASC')->get();
		
		#reset positions
		foreach($all as $i=>$one_model) {
			$one_model->position = $i;
			$one_model->save(null, true);
		}
	}
}