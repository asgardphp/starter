<?php
namespace Coxis\Core;

class FrontController extends Controller {
	public function mainAction() {
		if(!defined('_ENV_'))
			if(isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost'))
				define('_ENV_', 'dev');
			else
				define('_ENV_', 'prod');

		\BundlesManager::loadBundles();

		\Router::parseRoutes();

		\Hook::trigger('start');
		//Dispatch to target controller
		$output = \Router::dispatch();
		\Hook::trigger('filter_output', array(&$output), null);
		//Send the response
		\Response::setContent($output)->send();
	}
}