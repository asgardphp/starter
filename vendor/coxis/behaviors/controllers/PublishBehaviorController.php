<?php
namespace Coxis\Behaviors\Controllers;

class PublishBehaviorController extends \Coxis\Core\Controller {
	public function publishAction($request) {
		$controller = $request->parentController.'Controller';
		$modelName = $controller::getModel();
		$model = $modelName::load($request['id']);
		$model->save(array('published'=>!$model->published));
		return \Response::back();
	}
}