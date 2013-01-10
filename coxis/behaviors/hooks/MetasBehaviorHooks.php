<?php
namespace Coxis\Behaviors\Hooks;

class MetasBehaviorHooks extends \Coxis\Core\HooksContainer {
	/**
	@Hook('behaviors_load_metas')
	**/
	public function behaviors_load_metasAction($modelDefinition) {
		$modelDefinition->addProperty('meta_title', array('type' => 'text', 'required' => false));
		$modelDefinition->addProperty('meta_description', array('type' => 'text', 'required' => false));
		$modelDefinition->addProperty('meta_keywords', array('type' => 'text', 'required' => false));
	}
}