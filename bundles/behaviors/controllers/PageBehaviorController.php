<?php
namespace Coxis\Bundles\Behaviors\Controllers;

class PageBehaviorController extends \Coxis\Core\Controller {
	/**
	@Hook('behaviors_pre_load')
	**/
	public function behaviors_pre_loadAction($modelDefinition) {
		// if(isset($model::$behaviors['page'])) {
		// 	$model::$behaviors['metas'] = true;
		// 	$model::$behaviors['slugify'] = true;
		// 	unset($model::$behaviors['page']);
		// }
		// if(isset($model::getDefinition()->behaviors['page'])) {
		// 	$model::getDefinition()->behaviors['metas'] = true;
		// 	$model::getDefinition()->behaviors['slugify'] = true;
		// 	unset($model::getDefinition()->behaviors['page']);
		// }
		if(isset($modelDefinition->behaviors['page'])) {
			$modelDefinition->behaviors['metas'] = true;
			$modelDefinition->behaviors['slugify'] = true;
			unset($modelDefinition->behaviors['page']);
		}
	}
}