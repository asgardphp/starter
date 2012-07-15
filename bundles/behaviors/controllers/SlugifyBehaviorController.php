<?php
class SlugifyBehaviorController extends Controller {
	/**
	@Hook('behaviors_load_slugify')
	**/
	public function behaviors_load_slugifyAction($model) {
		$model::addProperty('slug', array('type' => 'text', 'required' => false));
	}
	
	/**
	@Hook('behaviors_presave_slugify')
	**/
	public function behaviors_presave_slugifyAction($model) {
		//~ $model->slugify();
		//~ if(!$src)
			$src = $model;
		if($model->isNew())
			$model->slug = Tools::slugify($src);
		else {
			$inc = 1;
			do {
				$model->slug = Tools::slugify($src).($inc < 2 ? '':'-'.$inc);
				$inc++;
			}
			while($model::query('SELECT * FROM %table% WHERE id!=? AND slug=?', array($model->id, $model->slug)));
		}
	}
}