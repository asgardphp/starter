<?php
namespace Coxis\Bundles\Behaviors\Libs;

class Metas {
	public static function set($model) {
		HTML::setTitle($model->meta_title!='' ? $model->meta_title:$model);
		HTML::setKeywords($model->meta_keywords);
		HTML::setDescription($model->meta_description);
	}
}