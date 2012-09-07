<?php
namespace Coxis\Bundles\Behaviors\Controllers;

class FilesBehaviorController extends \Coxis\Core\Controller {
	public static function _autoload() {
		Validator::register('filerequired', function($attribute, $value, $params, $validator) {
			$msg = false;
			if(!$value)
				$msg = $validator->getMessage('filerequired', $attribute, 'The file ":attribute" is required.');
			elseif(!$value->exists())
				$msg = $validator->getMessage('fileexists', $attribute, 'The file ":attribute" does not exist.');
			if($msg)
				return Validator::format($msg, array(
					'attribute'	=>	$attribute,
				));
		});
		
		Validator::register('image', function($attribute, $value, $params, $validator) {
			try {
				$mime = mime_content_type($value['tmp_name']);
				if(!in_array($mime, array('image/jpeg', 'image/png', 'image/gif'))) {
					$msg = $validator->getMessage('image', $attribute, 'The file ":attribute" must be an image.');
					return Validator::format($msg, array(
						'attribute'	=>	$attribute,
					));
				}
			} catch(\ErrorException $e) {}
		});
	}

	/**
	@Hook('behaviors_pre_load')
	**/
	public function behaviors_pre_loadAction($model) {
		if(!isset($model::$behaviors['files']))
			$model::$behaviors['files'] = true;
	}

	/**
	@Hook('behaviors_load_sortable')
	**/
	public function behaviors_load_filesAction($modelName) {
		$modelName::hookCall('hasFile', function($model, $file) {
			return $model::hasProperty($file) && $model::property($file)->type == 'file';
		});

		$modelName::on('save', function($model) {
			foreach($model::properties() as $property)
				if($property->type == 'file')
					$model->{$property->getName()}->save();
		});

		$modelName::on('destroy', function($model) {
			foreach($model::$files as $name=>$v)
				$this->$name->delete();
		});
	}
}