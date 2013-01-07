<?php
namespace Coxis\General\Controllers;

class GeneralController extends \Coxis\Core\Controller {
	/**
	@Hook('controller_configure')
	*/
	public function pagelayoutAction($controller) {
		HTML::setTitle('Coxis');
		\Memory::set('layout', array('\Coxis\App\Standard\Controllers\DefaultController', 'layout'));

		$controller->addFilter(new \Coxis\Core\Filters\PageLayout);
	}

	/**
	@Hook('exception_Coxis\Core\Exceptions\NotFoundException')
	*/
	public function hook404ExceptionAction($exception) {
		$request = \Router::getRequest();
		$response = \Response::inst();
		if($request['format']=='html')
			$output = \Coxis\Core\Router::run('default', '_404');
		return $response->setCode(404);
	}

	/**
	@Hook('output')
	@Priority(1000)
	*/
	public function gzipAction() {
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