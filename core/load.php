<?php
/* CONFIG */
import('Coxis\Core\Config');
\Coxis\Core\Config::loadConfigDir('config');
if(\Coxis\Core\Config::get('error_display'))
	\Coxis\Core\Error::display(true);
			
function url_for($what, $params=array(), $relative=true) {
	return \Coxis\Core\URL::url_for($what, $params, $relative);
}
BundlesManager::loadBundles();
//User Session
User::start();
Router::parseRoutes(BundlesManager::$routes);