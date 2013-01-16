<?php
/**
@Prefix('admin/slideshow')
*/
class SlideshowAdminController extends \App\Admin\Libs\Controller\AdminParentController {
	static $_model = 'Slideshow';
	
	public function formConfigure($model) {
		$form = new \App\Admin\Libs\Form\AdminModelForm($model, $this);
		
		return $form;
	}

	/**
	@Route('')
	*/
	public function indexAction($request) {
		$this->slideshow = Slideshow::first();
		if(!$this->slideshow)
			$this->slideshow = new Slideshow;
		$this->form = $this->formConfigure($this->slideshow);

		if($this->form->isSent()) {
			try {
				$this->form->save();
				Flash::addSuccess('The slideshow was saved successfully.');
			} catch(\App\Form\FormException $e) {
				\Flash::addError($this->form->getGeneralErrors());
				\Response::setCode(400);
			}
		}
		elseif(!$this->form->uploadSuccess()) {
			\Flash::addError(__('Data exceeds upload size limit. Maybe your file is too heavy.'));
			\Response::setCode(400);
		}
		$this->setRelativeView('form.php');
	}
	
	/**
	@Route(':id/deletefile/:file')
	*/
	public function deleteSingleFileAction($request) {
		$_model = 'Slideshow';
		
		if(!($this->$_model = $_model::load($request['id'])))
			$this->forward404();
			
		$file = $request['file'];
		$this->$_model->$file->delete();
		\Flash::addSuccess(__('File deleted with success.'));
		return \Response::back();
	}
}