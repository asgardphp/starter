<?php
namespace Coxis\Behaviors\Hooks;

class TimestampsBehaviorHooks extends \Coxis\Hook\HooksContainer {
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
		$ModelDefinition->addProperty('created_at', array('type' => 'datetime', 'required' => false, 'editable' => false));
		$ModelDefinition->addProperty('updated_at', array('type' => 'datetime', 'required' => false, 'editable' => false));
	}
	
	/**
	@Hook('behaviors_presave_timestamps')
	**/
	public function behaviors_presave_timestampsAction($model) {
		if(!$model->created_at)
			$model->created_at = new \Coxis\Utils\Datetime(time());
		$model->updated_at = new \Coxis\Utils\Datetime(time());
	}
}