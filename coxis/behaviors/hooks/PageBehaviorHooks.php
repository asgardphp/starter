<?php
namespace Coxis\Behaviors\Hooks;

class PageBehaviorHooks extends \Coxis\Core\HooksContainer {
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