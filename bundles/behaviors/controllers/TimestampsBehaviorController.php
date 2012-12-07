<?php
namespace Coxis\Bundles\Behaviors\Controllers;

class TimestampsBehaviorController extends \Coxis\Core\Controller {
	/**
	@Hook('behaviors_pre_load')
	**/
	public function behaviors_pre_loadAction($ModelDefinition) {
		if(!isset($ModelDefinition->behaviors['timestamps']))
			$ModelDefinition->behaviors['timestamps'] = true;
	}
	
	/**
	@Hook('behaviors_load_timestamps')
	**/
	public function behaviors_load_timestampsAction($ModelDefinition) {
		$ModelDefinition->addProperty('created_at', array('type' => 'date', 'required' => false, 'editable' => false, 'default'=>date('d/m/Y')));
		$ModelDefinition->addProperty('updated_at', array('type' => 'date', 'required' => false, 'editable' => false));
	}
	
	/**
	@Hook('behaviors_presave_timestamps')
	**/
	public function behaviors_presave_timestampsAction($model) {
		if(!$model->created_at)
			$model->created_at = new \Coxis\Core\Tools\Datetime();
		$model->updated_at = new \Coxis\Core\Tools\Datetime();
	}
}