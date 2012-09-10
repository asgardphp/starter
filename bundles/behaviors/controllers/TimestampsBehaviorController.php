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
		$model::addProperty('created_at', array('type' => 'date', 'required' => false, 'editable' => false));
		$model::addProperty('updated_at', array('type' => 'date', 'required' => false, 'editable' => false));
	}
	
	/**
	@Hook('behaviors_presave_timestamps')
	**/
	public function behaviors_presave_timestampsAction($model) {
		if($model->isNew()) {
			$model->created_at = new \Coxis\Core\Tools\Datetime();
			$model->updated_at = new \Coxis\Core\Tools\Datetime();
		}
		elseif(!$model->isNew())
			$model->updated_at = new \Coxis\Core\Tools\Datetime();
	}
}