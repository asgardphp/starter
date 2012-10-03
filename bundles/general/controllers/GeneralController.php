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
		if(!\Coxis\Core\Response::getContent())
			return;
		
		//~ ini_set('zlib.output_compression','Off');
		//~ d(Response::getContent());
		$output = gzencode(\Coxis\Core\Response::getContent());
		\Coxis\Core\Response::setContent($output);
		\Coxis\Core\Response::setHeader('Content-Encoding', 'gzip');
		\Coxis\Core\Response::setHeader('Content-Length', strlen($output));
		\Coxis\Core\Response::setHeader('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
		\Coxis\Core\Response::setHeader('Pragma', 'no-cache');
	}
	
	/**
	@Hook('output_404')
	**/
	public function hook404Action() {
		if(static::$called404)
			return;
		static::$called404 = true;
		
		$request = Router::getRequest();
		
		if($request['format']=='html') {
			$output = Router::run('default', '_404');
			$output = Hook::hook('output', $output);
			Response::setContent($output);
		}
		
		Response::send();
	}

	/**
	@Hook('start')
	@Priority(-10)
	*/
	public function startAction() {
		\Coxis\Core\Coxis::set('layout', array('\Coxis\App\Standard\Controllers\Default', 'layout'));
	}
	
	/**
	@Hook('filter_output')
	*/
	public function preSendingAction(&$content) {
		try {
			if(get(Router::getRequest(), 'format') != 'html')
			if(get(\Coxis\Core\Router::getRequest(), 'format') != 'html')
				return;
		} catch(\Exception $e) {}
			
		if(is_array(\Coxis\Core\Coxis::get('layout'))
			&& sizeof(\Coxis\Core\Coxis::get('layout')) >= 2 && $content !== null)
			$content = \Coxis\Core\Router::run(\Coxis\Core\Coxis::get('layout', 0), \Coxis\Core\Coxis::get('layout', 1), $content, $this);
	}
}