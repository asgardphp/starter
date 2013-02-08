<?php
namespace Coxis\Behaviors\Hooks;

class PublishBehaviorHooks extends \Coxis\Hook\HooksContainer {
	/**
	@Hook('behaviors_load_publish')
	**/
	public function behaviors_load_publishAction($modelDefinition) {
		$modelDefinition->addProperty('published', array('type'=>'boolean', 'default'=>true));
	}
}