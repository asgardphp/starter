<?php
namespace Coxis\Bundles\Behaviors\Controllers;

class PageBehaviorController extends \Coxis\Core\Controller {
	/**
	@Hook('behaviors_pre_load')
	**/
	public function behaviors_pre_loadAction($modelDefinition) {
		if(isset($modelDefinition->behaviors['page'])) {
			$modelDefinition->behaviors['metas'] = true;
			$modelDefinition->behaviors['slugify'] = true;
			unset($modelDefinition->behaviors['page']);
		}
	}
}