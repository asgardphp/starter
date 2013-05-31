<?php
/**
@Prefix('admin/newsletter')
*/
class NewsletterAdminController extends \Coxis\App\Admin\Libs\Controller\ModelAdminController {
	static $_model = 'mailing';
	static $_models = 'mailings';
	static $_index = 'newsletter';
	
	function __construct() {
		$this->_messages = array(
			'modified'			=>	__('Mailing modified with success.'),
			'created'			=>	__('Mailing sent with success.'),
			'many_deleted'			=>	__('Mailings modified with success.'),
			'deleted'			=>	__('Mailing deleted with success.'),
			'unexisting'			=>	__('This mailing does not exist.'),
		);
		parent::__construct();
	}
	
	public function formConfigure($model) {
		$form = new \Coxis\App\Admin\Libs\Form\AdminModelForm($model, $this);
		return $form;
	}
	
	public function test($subject) {
		$email = \POST::get('testmail');
		$html = $this->form->content->getValue();
		$html = str_replace('src="/', 'src="'.\URL::base(), $html);
		$html = View::render('app/newsletter/views/newsletteradmin/newsletter.php', array('content'=>$html, 'key'=>'', 'subscriber_id'=>0, 'id'=>0));
		$html = str_replace('src="/web/', 'src="'.URL::base(), $html);
		Email::create($email, \Value::val('email'), $subject, $this->form->plaintext->getValue(), $html)->send();
	}
	
	public function envoi($subject) {
		$subscribers = Subscriber::all();
		foreach($subscribers as $subscriber) {
			$key = sha1(Config::get('salt').$subscriber->id);
			$html = $this->form->content->getValue();
			$html = str_replace('src="/', 'src="'.\URL::base(), $html);
			$html = $this->render('app/newsletter/views/newsletteradmin/newsletter.php', array('content'=>$html, 'subscriber_id'=>$subscriber->id, 'key'=>$key, 'id'=>$this->mailing->id));
			$html = str_replace('src="/web/', 'src="'.URL::base(), $html);
			Email::create($subscriber->email, \Value::val('email'), $subject, $this->form->plaintext->getValue(), $html)->send();
		}
	}
	
	/**
	@Route('new')
	*/
	public function newAction($request) {
		$_model = static::$_model;
		
		$this->$_model = new $_model;
	
		$this->form = $this->formConfigure($this->$_model);

		if($this->form->isSent())
			try {
				$this->form->save();

				$subject = $this->form->title->getValue();
				
				if(\POST::has('tous')) {
					$this->envoi($subject);
					Flash::addSuccess(__('Mailing sent to all subscribers.'));
				}
				elseif(\POST::has('test')) {
					$this->test($subject);
					Flash::addSuccess(__('Mailing sent to the given email address.'));
				}
				else
					Flash::addSuccess($this->_messages['created']);
				
				if(\POST::has('send'))
					return \Response::redirect($this->url_for('index'));
				else
					return \Response::redirect($this->url_for('edit', array('id'=>$this->$_model->id)));
			}
			catch(\Coxis\Form\FormException $e) {
				Flash::addError($e->errors);
			}
		
		$this->setRelativeView('form.php');
	}
	
	/**
	@Route(':id/edit')
	*/
	public function editAction($request) {
		$_model = static::$_model;
		
		if(!($this->$_model = $_model::load($request['id'])))
			$this->forward404();
	
		$this->form = $this->formConfigure($this->$_model);
	
		if($this->form->isSent())
			try {
				$this->form->save();
				
				$subject = $this->form->title->getValue();
				if(\POST::has('tous')) {
					$this->envoi($subject);
					Flash::addSuccess(__('Mailing sent to all subscribers.'));
				}
				elseif(\POST::has('test')) {
					$this->test($subject);
					Flash::addSuccess(__('Mailing sent to the given email address.'));
				}
				else
					Flash::addSuccess($this->_messages['modified']);

				if(\POST::has('send'))
					return Response::redirect($this->url_for('index'));
			}
			catch(\Coxis\Core\Form\FormException $e) {
				Flash::addError($e->errors);
			}
		
		$this->setRelativeView('form.php');
	}
}