<?php
class MultifileController extends Controller {
	public function addAction($request) {
		$modelName = CoxisAdmin::getModelNameFor($request['_controller']);
		if(!($model = $modelName::load($request['id'])))
			$this->forward404();
		if(!$model->fileExists($request['file']))
			$this->forward404();
			
		try {
			if(isset($_FILES['Filedata']))
				$files = array($request['file'] => $_FILES['Filedata']);
			else
				Response::setCode(500)->setContent('Erreur lors de l\'envoi.')->send();
				
			$model->setFiles($files)->save();
			$final_paths = $model->getFilePath($request['file']);
			$response = array(
				'url' => array_pop($final_paths),
				'deleteurl' => url_for('coxis_'.$model->getModelName().'_files_delete', array('id' => $model->id, 'pos' => sizeof($final_paths)+1, 'file' => $request['file'])),
			);
			Response::setCode(200)->setContent(json_encode($response))->send();
		} catch(Exception $e) {
			Response::setCode(500)->setContent('Erreur lors de l\'envoi.')->send();
		}
	}
	
	public function deleteAction($request) {
		$modelName = CoxisAdmin::getModelNameFor($request['_controller']);
		if(!($model = $modelName::load($request['id'])))
			$this->forward404();
		if(!$model->fileExists($request['file']))
			$this->forward404();
			
		$paths = $model->getFilePath($request['file']);

		if(!isset($paths[$request['pos']-1]))
			Response::redirect(url_for(array($request['_controller'], 'edit'), array('id' => $model->id)))->setCode(404)->send();

		$path = $paths[$request['pos']-1];
		
		$rawpaths = $model->getRawFilePath($request['file']);
		unset($rawpaths[$request['pos']-1]);
		$rawpaths = array_values($rawpaths);
		
		try {
			$model->setRawFilePath($request['file'], $rawpaths)->save(null, true);
			Messenger::addSuccess('Fichier supprimé avec succès.');
			FileManager::unlink(_WEB_DIR_.'/'.$path);
		} catch(Exception $e) {
			Messenger::addError('Il y a eu une erreur avec l\'élément');
		}
		
		try {
			Response::redirect(url_for(array($request['_controller'], 'edit'), array('id' => $model->id)))->send();
		} catch(Exception $e) {
			Response::redirect(url_for(array($request['_controller'], 'index')))->send();
		}
	}
}