<?php
/**
@Prefix('newsletter')
*/
class SubscriberController extends Controller {
	public function newsletter() {
		$subscriber = new Subscriber;
		$this->form = new ModelForm($subscriber);
	}

	/**
	@Route('submit')
	*/
	public function submitAction($request) {
		$subscriber = new Subscriber;
		$this->form = new ModelForm($subscriber);
		if($this->form->isSent()) {
			try {
				$this->form->save();
			}
			catch(\Coxis\Form\FormException $e) {
				echo 'Mail incorrect !';
				return \Response::setCode(500);
			}
		}
	}
	/**
	@Route('unsubscribe/:key')
	*/
	public function unsubAction($request) {
		$subscriber = Membre::where(array("SHA1(CONCAT('".Config::get('salt')."', id)) = '$request[key]'"))->first();
		if(!$membre)
			$this->notfound();
		$subscriber->destroy();
		return '<p>'.__('You have been unsubscribed.').'</p>';
	}
}