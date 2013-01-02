<?php
namespace Coxis\Bundles\General\Controllers;

class GeneralController extends \Coxis\Core\Controller {
	/**
	@Hook('start')
	@Priority(-10)
	*/
	public function startAction() {
		HTML::setTitle('LiliChantilly');
		\Memory::set('layout', array('\Coxis\App\Standard\Controllers\Default', 'layout'));
	}

	/**
	@Hook('exception_Coxis\Core\Exceptions\NotFoundException')
	*/
	public function hook404ExceptionAction($exception) {
		$request = \Router::getRequest();
		$response = \Response::inst();
		$response->setCode(404);
		if($request['format']=='html') {
			$output = \Coxis\Core\Router::run('default', '_404');
			$response->setContent($output);
		}
		return $response;
	}
	
	/**
	@Hook('filter_response')
	*/
	public function preSendingAction($response) {
		if($response->getCode() == 500)
			return;

		if(function_exists('getallheaders'))
			if(\get(\getallheaders(), 'X-Requested-With') == 'XMLHttpRequest')
				return;

		try {
			if(\Response::getHeader('Content-Type') && \Response::getHeader('Content-Type')!='text/html')
				return;
		} catch(\Exception $e) {}
			
		if(is_array(\Memory::get('layout')) && sizeof(\Memory::get('layout')) >= 2 && $response->getContent() !== null) {
			$res = \Router::run(\Memory::get('layout', 0), \Memory::get('layout', 1), $response->getContent());
			if(\Memory::get('htmlLayout') !== false)
				$res = View::render('app/standard/views/default/html.php', array('content'=>$res));
			$response->setContent($res);
		}
	}

	/**
	@Hook('output')
	@Priority(10000)
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