<?php
namespace App\General\Hooks;

class GeneralHooks extends \Coxis\Hook\HooksContainer {
	/**
	@Hook('controller_configure')
	*/
	public static function pagelayout($chain, $controller) {
		\Coxis\Core\App::get('html')->setTitle('Coxis');
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
		if(!\Coxis\Core\App::get('response')->getContent())
			return;

		$r = \Coxis\Core\App::get('response')->getContent();
		$output = gzencode($r);
		\Coxis\Core\App::get('response')->setContent($output);
		\Coxis\Core\App::get('response')->setHeader('Content-Encoding', 'gzip');
		\Coxis\Core\App::get('response')->setHeader('Content-Length', strlen($output));
		\Coxis\Core\App::get('response')->setHeader('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
		\Coxis\Core\App::get('response')->setHeader('Pragma', 'no-cache');
	}
}