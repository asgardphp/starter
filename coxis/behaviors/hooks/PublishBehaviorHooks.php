<?php
namespace Coxis\Behaviors\Hooks;

class PublishBehaviorHooks extends \Coxis\Hook\HooksContainer {
	/**
	@Hook('behaviors_load_publish')
	**/
	public function behaviors_load_publishAction($modelDefinition) {
		$modelDefinition->addProperty('published', array('type'=>'boolean', 'default'=>true));

		$modelName = $modelDefinition->getClass();
		$modelDefinition->hookOn('callStatic', function($chain, $name, $args) use ($modelName) {
			if($name == 'published') {
				$chain->found = true;
				return $modelName::orm()->where(array('published'=>1));
			}
		});
	}
}