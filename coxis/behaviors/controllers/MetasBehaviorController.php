<?php
namespace Coxis\Behaviors\Controllers;

class MetasBehaviorController extends \Coxis\Core\Controller {
	/**
	@Hook('behaviors_load_metas')
	**/
	public function behaviors_load_metasAction($modelDefinition) {
		$modelDefinition->addProperty('meta_title', array('type' => 'text', 'required' => false));
		$modelDefinition->addProperty('meta_description', array('type' => 'text', 'required' => false));
		$modelDefinition->addProperty('meta_keywords', array('type' => 'text', 'required' => false));
	}
}