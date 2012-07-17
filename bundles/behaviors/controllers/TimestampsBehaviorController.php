<?php
namespace Coxis\Bundles\Behaviors\Controllers;

class TimestampsBehaviorController extends Controller {
	/**
	@Hook('behaviors_pre_load')
	**/
	public function behaviors_pre_loadAction($model) {
		if(!isset($model::$behaviors['timestamps']))
			$model::$behaviors['timestamps'] = true;
	}
	
	/**
	@Hook('behaviors_load_timestamps')
	**/
	public function behaviors_load_timestampsAction($model) {
		$model::addProperty('created_at', array('type' => 'date', 'required' => true, 'editable' => false));
		$model::addProperty('updated_at', array('type' => 'date', 'required' => true, 'editable' => false));
	}
	
	/**
	@Hook('behaviors_presave_timestamps')
	**/
	public function behaviors_presave_timestampsAction($model) {
		if($model->isNew()) {
			$model->created_at = new Date();
			$model->updated_at = new Date();
		}
		elseif(!$model->isNew())
			$model->updated_at = new Date();
	}
}