<?php
namespace Coxis\Bundles\Files\Controllers;

class FilesBehaviorController extends \Coxis\Core\Controller {
	public static function _autoload() {
		\Validation::register('filerequired', function($attribute, $value, $params, $validator) {
			if(!$params[0])
				return;
			$msg = false;
			if(!$value)
				$msg = $validator->getMessage('filerequired', $attribute, __('The file ":attribute" is required.'));
			elseif(!$value->exists())
				$msg = $validator->getMessage('fileexists', $attribute, __('The file ":attribute" does not exist.'));
			if($msg)
				return \Validation::format($msg, array(
					'attribute'	=>	$attribute,
				));
		});
		
		\Validation::register('image', function($attribute, $value, $params, $validator) {
			try {
				$mime = mime_content_type($value['tmp_name']);
				if(!in_array($mime, array('image/jpeg', 'image/png', 'image/gif'))) {
					$msg = $validator->getMessage('image', $attribute, __('The file ":attribute" must be an image.'));
					return \Validation::format($msg, array(
						'attribute'	=>	$attribute,
					));
				}
			} catch(\ErrorException $e) {}
		});
		

		\Validation::register('allowed', function($attribute, $value, $params, $validator) {
			if(!$value->exists())
				return;
			if(!in_array($value->extension(), $params[0])) {
				$msg = $validator->getMessage('image', $attribute, __('This type of file is not allowed ":ext".'));
				return \Validation::format($msg, array(
					'attribute'	=>	$attribute,
					'ext'	=>	$value->extension(),
				));
			}
		});
	}

	/**
	@Hook('behaviors_pre_load')
	**/
	public function behaviors_pre_loadAction($model) {
		if(!isset($model::$behaviors['files']))
			$model::$behaviors['files'] = true;
		if($model::$behaviors['files']) {
			$model::hookBefore('propertyClass', function($chain, $type) {
				if($type == 'file')
					return '\Coxis\Bundles\Files\Libs\FileProperty';
			});
		}
	}

	/**
	@Hook('behaviors_load_files')
	**/
	public function behaviors_load_filesAction($modelName) {
		$modelName::hookOn('call', function($chain, $model, $name, $file) {
			if($name == 'hasFile') {
				$chain->found = true;
				return $model::hasProperty($file[0]) && $model::property($file[0])->type == 'file';
			}
		});

		$modelName::hookOn('callStatic', function($chain, $name, $args) use($modelName) {
			$res = null;
			#Article::files()
			if($name == 'files') {
				$res = array();
				foreach($modelName::properties() as $name=>$property)
					if($property->type == 'file')
						$res[$name] = $property;
			}
			if($res !== null) {
				$chain->found = true;
				return $res;
			}
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