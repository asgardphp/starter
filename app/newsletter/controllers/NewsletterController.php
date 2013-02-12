<?php
/**
@Prefix('newsletter')
*/
class NewsletterController extends Controller {
	/**
	@Route('archives/:id/:subscriber_id')
	*/
	public function showAction($request) {
		Memory::set('layout', false);
		$mailing = Mailing::load($request['id']);
		$subscriber = Subscriber::load($request['subscriber_id']);
		if($subscriber) {
			$sub_id = $subscriber->id;
			$key = sha1(Config::get('salt').$subscriber->id);
		}
		else {
			$sub_id = 0;
			$key = '';
		}
		return $this->render('app/newsletter/views/newsletteradmin/newsletter.php', array('content'=>$mailing->content, 'key'=>$key, 'id'=>$mailing->id, 'subscriber_id'=>$sub_id));
	}
}