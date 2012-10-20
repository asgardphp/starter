<?php
namespace Coxis\Core\Tools;

class Browser {
	public static function get($url) {
		// ob_start();

		Context::setDefault(md5($url));
		\URL::setURL($url);
		\URL::setServer('localhost');
		\URL::setRoot('');

		// require_once 'core/load.php';

		/* CONFIG */
		// import('Coxis\Core\Config');
		// \Coxis\Core\Config::loadConfigDir('config');
		// if(\Coxis\Core\Config::get('error_display'))
		// 	\Coxis\Core\Error::display(true);
		
		function url_for($what, $params=array(), $relative=true) {
			return \URL::url_for($what, $params, $relative);
		}

		// import('\Coxis\Core\Tools\Locale');
		// $_GET = 'aaa';
		\BundlesManager::loadBundles();
		//User Session
		\User::start();
		\Router::parseRoutes();

// d(\Coxis\Core\Router::getRoutes());

		\Hook::trigger('start');
		//Dispatch to target controller
		$output = \Router::dispatch();
		\Hook::trigger('filter_output', array(&$output), null);
		//Send the response
		return $output;
	}
}