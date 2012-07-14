<?php
class MetasBehaviorController extends Controller {
	/**
	@Hook('behaviors_load_metas')
	**/
	public function behaviors_load_metasAction($model) {
	//~ d($model);
		$model::addProperty('meta_title', array('type' => 'text', 'required' => false));
		$model::addProperty('meta_description', array('type' => 'text', 'required' => false));
		$model::addProperty('meta_keywords', array('type' => 'text', 'required' => false));
	}
}