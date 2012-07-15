<?php
/**
@Prefix('newsletter')
*/
class InscritController extends Controller {
	public function widgetAction($request) {
		$inscrit = new Inscrit;
		$this->form = new ModelForm($inscrit);
		
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