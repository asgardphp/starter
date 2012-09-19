<?php
namespace Coxis\Bundles\Files\Controllers;

class FilesBehaviorController extends \Coxis\Core\Controller {
	public static function _autoload() {
		Validator::register('filerequired', function($attribute, $value, $params, $validator) {
			if(!$params[0])
				return;
			$msg = false;
			if(!$value)
				$msg = $validator->getMessage('filerequired', $attribute, __('The file ":attribute" is required.'));
			elseif(!$value->exists())
				$msg = $validator->getMessage('fileexists', $attribute, __('The file ":attribute" does not exist.'));
			if($msg)
				return Validator::format($msg, array(
					'attribute'	=>	$attribute,
				));
		});
		
		Validator::register('image', function($attribute, $value, $params, $validator) {
			try {
				$mime = mime_content_type($value['tmp_name']);
				if(!in_array($mime, array('image/jpeg', 'image/png', 'image/gif'))) {
					$msg = $validator->getMessage('image', $attribute, __('The file ":attribute" must be an image.'));
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
	@Hook('behaviors_load_files')
	**/
	public function behaviors_load_filesAction($modelName) {
		$modelName::hookOn('call', function($chain, $model, $name, $file) {
			if($name == 'hasFile')
				return $model::hasProperty($file[0]) && $model::property($file[0])->type == 'file';
		});

		$modelName::hookBefore('save', function($chain, $model) {
			foreach($model::properties() as $name=>$property)
				if($property->type == 'file')
					$model->$name->save();
		});

		$modelName::hookOn('destroy', function($chain, $model) {
			foreach($model::properties() as $name=>$property)
				if($property->type == 'file')
					$model->$name->delete();
		});
	}
}