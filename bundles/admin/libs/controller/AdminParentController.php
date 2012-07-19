<?php
namespace Coxis\Bundles\Admin\Libs\Controller;

abstract class AdminParentController extends Controller {
	
	//~ function __construct() {
		//~ if(static::$_models == null)
			//~ static::$_models = static::$_model.'s';
		//~ if(isset(static::$_messages))
			//~ static::$_messages = array_merge(static::$__messages, static::$_messages);
		//~ else
			//~ static::$_messages = static::$__messages;
		//~ static::$_index = static::$_index ? static::$_index:static::$_models;
	//~ }

	public function configure($request) {
		Coxis::set('layout', array('Admin', 'layout'));
		if(!User::getId() || User::getRole()!='admin') {
			$_SESSION['redirect_to'] = URL::full();
			Response::setCode(401)->redirect('admin/login', true)->send();
		}
	}
}