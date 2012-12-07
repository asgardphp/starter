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
	@Hook('filter_response')
	**/
	public function hook404Action($response) {
		if($response->getCode() != 404)
			return;
		
		$request = \Router::getRequest();
		
		if($request['format']=='html') {
			$output = \Coxis\Core\Router::run('default', '_404');
			$response->setContent($output);
		}
	}
	
	/**
	@Hook('filter_response')
	*/
	public function preSendingAction($response) {
		if($response->getCode() == 500)
			return;

		if(get(getallheaders(), 'X-Requested-With') == 'XMLHttpRequest')
			return;

		try {
			if(\Response::getHeader('Content-Type') && \Response::getHeader('Content-Type')!='text/html')
				return;
		} catch(\Exception $e) {}
			
		if(is_array(\Memory::get('layout')) && sizeof(\Memory::get('layout')) >= 2 && $response->getContent() !== null)
			$response->setContent(\Router::run(\Memory::get('layout', 0), \Memory::get('layout', 1), $response->getContent()));
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