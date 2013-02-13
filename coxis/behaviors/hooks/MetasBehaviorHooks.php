<?php
namespace Coxis\Behaviors\Hooks;

class MetasBehaviorHooks extends \Coxis\Hook\HooksContainer {
	/**
	@Hook('behaviors_load_metas')
	**/
	public function behaviors_load_metasAction($modelDefinition) {
		$modelDefinition->addProperty('meta_title', array('type' => 'text', 'required' => false));
		$modelDefinition->addProperty('meta_description', array('type' => 'text', 'required' => false));
		$modelDefinition->addProperty('meta_keywords', array('type' => 'text', 'required' => false));

		$modelName = $modelDefinition->getClass();
		$modelDefinition->hookOn('call', function($chain, $model, $name, $args) {
			#$article->showMetas()
			if($name == 'showMetas') {
				$chain->found = true;
				HTML::setTitle($model->meta_title!='' ? html_entity_decode($model->meta_title):html_entity_decode($model));
				HTML::setKeywords($model->meta_keywords);
				HTML::setDescription($model->meta_description);
			}
		});
	}
}