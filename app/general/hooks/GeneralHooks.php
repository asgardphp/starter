<?php
namespace App\General\Hooks;

class GeneralHooks extends \Asgard\Hook\HooksContainer {
	/**
	@Hook('controller_configure')
	*/
	public static function pagelayout($chain, $controller) {
		\Asgard\Core\App::get('html')->setTitle('Asgard');
		$controller->layout = array('\App\General\Controllers\DefaultController', 'layout');

		$controller->addFilter(new \Asgard\Core\Filters\PageLayout);
		$controller->addFilter(new \Asgard\Core\Filters\JSONEntities);
	}

	/**
	@Hook('exception_Asgard\Core\Exceptions\NotFoundException')
	*/
	public static function hook404Exception($chain, $exception) {
		return \Asgard\Core\Controller::run('DefaultController', '_404', \Request::inst())->setCode(404);
	}

	/**
	@Hook('output')
	@Priority(1000)
	*/
	public static function gzip($chain) {
		if(!isset($_SERVER['HTTP_ACCEPT_ENCODING']) || !strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
			return;
		if(!\Asgard\Core\App::get('response')->getContent())
			return;

		$r = \Asgard\Core\App::get('response')->getContent();
		$output = gzencode($r);
		\Asgard\Core\App::get('response')->setContent($output);
		\Asgard\Core\App::get('response')->setHeader('Content-Encoding', 'gzip');
		\Asgard\Core\App::get('response')->setHeader('Content-Length', strlen($output));
		\Asgard\Core\App::get('response')->setHeader('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
		\Asgard\Core\App::get('response')->setHeader('Pragma', 'no-cache');
	}
}