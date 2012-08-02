<?php
function url_for($what, $params=array(), $relative=true) {
	return \Coxis\Core\URL::url_for($what, $params, $relative);
}
BundlesManager::loadBundles();
//User Session
User::start();
Router::parseRoutes(BundlesManager::$routes);