<?php
/**
@Prefix('newsletter')
*/
class InscritController extends Controller {
	public function widgetAction($request) {
		$inscrit = Inscrit::create();
		$this->form = new ModelForm($inscrit);
	}

	/**
	@Route('submit')
	*/
	public function submitAction($request) {
		$inscrit = Inscrit::create();
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