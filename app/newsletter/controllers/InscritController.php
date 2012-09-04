<?php
/**
@Prefix('newsletter')
*/
class InscritController extends Controller {
	public function widgetAction($request) {
		$this->form = new ModelForm(new Inscrit);
		
		if($this->form->isSent()) {
			try {
				$this->form->save();
				Response::setCode(200)->send();
			}
			catch(FormException $e) {
				Response::setCode(500)->sendHeaders();
				echo 'Mail incorrect !';
				Response::send();
			}
		}
	}
}