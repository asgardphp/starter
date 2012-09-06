<?php
namespace Coxis\Bundles\Behaviors\Controllers;

class FilesBehaviorController extends \Coxis\Core\Controller {
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
		// $model_files = $modelName::$files;
		// if(is_array($model_files))
		// 	foreach($model_files as $file => $params) {
		// 		if(isset($params['multiple']) && $params['multiple']) #multiple
		// 			$modelName::addProperty('filename_'.$file, array('type' => 'array', 'editable'=>false, 'required'=>false));
		// 		else #single
		// 			$modelName::addProperty('filename_'.$file, array('type' => 'text', 'editable'=>false, 'required'=>false));
		// 	}

		$getfile = function($model, $file) {
			return new ModelFile($model, $file);
		};
		$setfile = function($model, $file, $value) {
			$model->data['_files'][$file] = $value;
		};

		foreach($modelName::$files as $file=>$params) {
			$modelName::hookGet($file, function($model) use($getfile, $file) {
				return $getfile($model, $file);
			});

			$modelName::hookSet($file, function($model, $value) use($setfile, $file) {
				return $setfile($model, $file, $value);
			});
		}

		$modelName::hookCall('hasFile', function($model, $file) {
			return array_key_exists($file, $model::$files);
		});

		$modelName::on('save', function($model) {
						// d($model->data['_files']);
			foreach($model::properties() as $property)
				if($property->type == 'file')
					// d($model->{$property->getName()}, $property->getName(), $property);
					$model->{$property->getName()}->save();

			/*if(isset($model->data['_files']) && is_array($model->data['_files']))
				foreach($model->data['_files'] as $file=>$arr)
					if($model->hasFile($file)
						// && is_uploaded_file($arr['tmp_name'])
						) {
						#todo should not use the name of the uploaded file, file injection
						$path = _WEB_DIR_.'/'.$model->$file->dir().'/'.$arr['name'];
						$model->$file->add($arr['tmp_name'], $path);
					}*/
		});

		$modelName::on('destroy', function($model) {
			foreach($model::$files as $name=>$v)
				$this->$name->delete();
		});
	}
}