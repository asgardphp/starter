<?php
namespace Coxis\Behaviors\Hooks;

class SlugifyBehaviorHooks extends \Coxis\Hook\HooksContainer {
	/**
	@Hook('behaviors_load_slugify')
	**/
	public function behaviors_load_slugifyAction($modelDefinition) {
		$modelDefinition->addProperty('slug', array('type' => 'text', 'required' => false, 'editable'=>false));
	}
	
	/**
	@Hook('behaviors_presave_slugify')
	**/
	public function behaviors_presave_slugifyAction($model) {
		if($model->isNew())
			$model->slug = \Coxis\Utils\Tools::slugify($model);
		else {
			$inc = 1;
			do {
				$model->slug = \Coxis\Utils\Tools::slugify($model).($inc < 2 ? '':'-'.$inc);
				$inc++;
			}
			while($model::where(array(
				'id != ?' => $model->id,
				'slug' => $model->slug,
			))->count());
		}
	}
}