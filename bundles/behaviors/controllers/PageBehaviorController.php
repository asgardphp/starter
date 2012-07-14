<?php
class PageBehaviorController extends Controller {
	/**
	@Hook('behaviors_pre_load')
	**/
	public function behaviors_pre_loadAction($model) {
	//~ if($model == 'page')
		//~ d(123, $model::$behaviors, $model::$behaviors['page'], isset($model::$behaviors['page']));
		if(isset($model::$behaviors['page'])) {
		//~ d('asdfg');
			$model::$behaviors['metas'] = true;
			$model::$behaviors['slugify'] = true;
			unset($model::$behaviors['page']);
		}
	}
}