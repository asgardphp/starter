<?php
namespace Coxis\Core\Tools;

class Browser {
	public static function get($url='') {
		$rand = Tools::randstr(10);
		Context::setDefault($rand);
		\Coxis\Core\Context::get('autoloader')->preloadDir('core');
		#todo redo this with request
		\URL::setURL($url);
		\URL::setServer('localhost');
		\URL::setRoot('');

		\BundlesManager::loadBundles();
		\Router::parseRoutes();

		\Hook::trigger('start');
		//Dispatch to target controller
		$output = \Router::dispatch();
		\Hook::trigger('filter_output', array(&$output));
		//Send the response
		return $output;
	}
}