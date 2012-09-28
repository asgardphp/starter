<?php
namespace Coxis\Core;

class FrontController extends Controller {
	public function mainAction() {
		if(!defined('_ENV_'))
			if(isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost'))
				define('_ENV_', 'dev');
			else
				define('_ENV_', 'prod');
	
		/* CONFIG */
		import('Coxis\Core\Config');
		\Coxis\Core\Config::loadConfigDir('config');
		if(\Coxis\Core\Config::get('error_display'))
			\Coxis\Core\Error::display(true);

		/* WEB RESOURCES */
		// $this->getResource();
		
		require_once('core/load.php');

		\Coxis\Core\Hook::trigger('start');
			
		//Dispatch to target controller
		$output = Router::dispatch($this);

		\Coxis\Core\Hook::trigger('filter_output', array(), null, $output);

		//Send the response
		Response::setContent($output)->send();
	}
}