<?php
namespace Coxis\Bundles\General\Controllers;

class GeneralController extends \Coxis\Core\Controller {
	static $called404 = false;

	/**
	@Hook('output')
	@Priority(10000)
	*/
	public function gzipAction() {
		if(!isset($_SERVER['HTTP_ACCEPT_ENCODING']) || !strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
			return;
		if(!\Response::getContent())
			return;
		
		$output = gzencode(\Response::getContent());
		\Response::setContent($output);
		\Response::setHeader('Content-Encoding', 'gzip');
		\Response::setHeader('Content-Length', strlen($output));
		\Response::setHeader('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
		\Response::setHeader('Pragma', 'no-cache');
	}
	
	/**
	@Hook('output_404')
	**/
	public function hook404Action() {
		if(static::$called404)
			return;
		static::$called404 = true;
		
		$request = \Router::getRequest();
		
		if($request['format']=='html') {
			$output = \Coxis\Core\Router::run('default', '_404');
			\Hook::trigger('output', array(&$output));
			\Response::setContent($output);
		}
		
		\Response::send();
	}

	/**
	@Hook('start')
	@Priority(-10)
	*/
	public function startAction() {
		\Memory::set('layout', array('\Coxis\App\Standard\Controllers\Default', 'layout'));
	}
	
	/**
	@Hook('filter_output')
	*/
	public function preSendingAction(&$content) {
		try {
			if(get(\Router::getRequest(), 'format') != 'html')
				return;
		} catch(\Exception $e) {}
			
		if(is_array(\Memory::get('layout'))
			&& sizeof(\Memory::get('layout')) >= 2 && $content !== null)
			$content = \Router::run(\Memory::get('layout', 0), \Memory::get('layout', 1), $content, $this);
	}
}