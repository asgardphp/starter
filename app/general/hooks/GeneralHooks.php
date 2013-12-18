<?php
namespace App\General\Hooks;

class GeneralHooks extends \Coxis\Hook\HooksContainer {
	/**
	@Hook('controller_configure')
	*/
	public static function pagelayout($chain, $controller) {
		\HTML::setTitle('Coxis');
		$controller->layout = array('\App\General\Controllers\DefaultController', 'layout');

		$controller->addFilter(new \Coxis\Core\Filters\PageLayout);
		$controller->addFilter(new \Coxis\Core\Filters\JSONEntities);
	}

	/**
	@Hook('exception_Coxis\Core\Exceptions\NotFoundException')
	*/
	public static function hook404Exception($chain, $exception) {
		return \Coxis\Core\Controller::run('DefaultController', '_404', \Request::inst())->setCode(404);
	}

	/**
	@Hook('output')
	@Priority(1000)
	*/
	public static function gzip($chain) {
		if(!isset($_SERVER['HTTP_ACCEPT_ENCODING']) || !strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
			return;
		if(!\Response::getContent())
			return;

		$r = \Response::getContent();
		$output = gzencode($r);
		\Response::setContent($output);
		\Response::setHeader('Content-Encoding', 'gzip');
		\Response::setHeader('Content-Length', strlen($output));
		\Response::setHeader('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
		\Response::setHeader('Pragma', 'no-cache');
	}
}