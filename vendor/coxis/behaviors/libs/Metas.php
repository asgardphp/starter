<?php
namespace Coxis\Behaviors\Libs;

class Metas {
	public static function set($model) {
		HTML::setTitle($model->meta_title!='' ? html_entity_decode($model->meta_title):html_entity_decode($model));
		HTML::setKeywords($model->meta_keywords);
		HTML::setDescription($model->meta_description);
	}
}