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
		$model->slugify();
	}
}