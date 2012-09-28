<?php
namespace Coxis\Bundles\Behaviors\Controllers;

class PageBehaviorController extends \Coxis\Core\Controller {
	/**
	@Hook('behaviors_pre_load')
	**/
	public function behaviors_pre_loadAction($model) {
		if(isset($model::$behaviors['page'])) {
			$model::$behaviors['metas'] = true;
			$model::$behaviors['slugify'] = true;
			unset($model::$behaviors['page']);
		}
	}
}