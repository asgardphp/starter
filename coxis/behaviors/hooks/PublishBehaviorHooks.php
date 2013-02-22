<?php
namespace Coxis\Behaviors\Hooks;

class PublishBehaviorHooks extends \Coxis\Hook\HooksContainer {
	/**
	@Hook('behaviors_load_publish')
	**/
	public function behaviors_load_publishAction($modelDefinition) {
		$modelName = $modelDefinition->getClass();

		$modelDefinition->addProperty('published', array('type'=>'boolean', 'default'=>true));

		#Article::load(2)
		$modelDefinition->addStaticMethod('published', function() use($modelName) {
			return $modelName::orm()->where(array('published'=>1));
		});
		#Article::load(2)
		$modelDefinition->addStaticMethod('loadPublished', function($id) use($modelName) {
			return $modelName::published()->where(array('id'=>$id))->first();
		});
	}
	
	/**
	@Hook('behaviors_coxisadmin_publish')
	*/
	public function behaviors_coxisadmin_publishAction($admin_controller) {
		$admin_controller .= 'Controller';
		$modelName = $admin_controller::getModel();
		
		try {
			$admin_controller::addHook(array(
				'route'			=>	':id/publish',
				'name'			=>	'coxis_'.$modelName.'_publish',
				'controller'	=>	'PublishBehavior',
				'action'			=>	'publish'
			));
			\Coxis\Hook\HooksContainer::addHook('coxis_'.$modelName.'_actions', function($model) use($modelName) {
				return '<a href="'.\URL::url_for('coxis_'.$modelName.'_publish', array('id' => $model->id), false).'">'.($model->published ? __('Unpublish'):__('Publish')).'</a> | ';
			});
			\Coxis\Hook\HooksContainer::addHook('coxis_'.$modelName.'_globalactions', function(&$actions) use($modelName) {
				#publish
				$actions[] = array(
					'text'	=>	__('Publish'),
					'value'	=>	'publish',
					'callback'	=>	function() use($modelName) {
						if(POST::size()>1) {
							foreach(POST::get('id') as $id) {
								$model = $modelName::load($id);
								$model->save(array('published'=>1));
							}
						
							Flash::addSuccess(sprintf(__('%s element(s) published with success!'), sizeof(POST::get('id'))));
						}
					}
				);
				#unpublish
				$actions[] = array(
					'text'	=>	__('Unpublish'),
					'value'	=>	'unpublish',
					'callback'	=>	function() use($modelName) {
						if(POST::size()>1) {
							foreach(POST::get('id') as $id) {
								$model = $modelName::load($id);
								$model->save(array('published'=>0));
							}
						
							Flash::addSuccess(sprintf(__('%s element(s) unpublished with success!'), sizeof(POST::get('id'))));
						}
					}
				);
			});
		} catch(\Exception $e) {}#if the admincontroller does not exist for this model
	}
}