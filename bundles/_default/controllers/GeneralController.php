<?php
class GeneralController extends Controller {
	static $called404 = false;

	/**
	@Hook('output')
	@Priority(10000)
	*/
	public function gzipAction() {
		if(!isset($_SERVER['HTTP_ACCEPT_ENCODING']) || !strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
			return;
		if(!Response::getContent())
			return;
		
		//~ ini_set('zlib.output_compression','Off');
		//~ d(Response::getContent());
		$output = gzencode(Response::getContent());
		Response::setContent($output);
		Response::setHeader('Content-Encoding', 'gzip');
		Response::setHeader('Content-Length', strlen($output));
		Response::setHeader('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
		Response::setHeader('Pragma', 'no-cache');
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
			$output = Router::run('General', '_404');
			$output = $this->filter('output', $output);
			Response::setContent($output);
		}
		
		Response::send();
	}
	
	public function _404Action() {
	}

	/**
	@Hook('start')
	@Priority(-10)
	*/
	public function startAction() {
		Coxis::set('layout', array('General', 'layout'));
	}
	
	public function layoutAction($content) {
		if(access(Router::getRequest(), 'format') != 'html')
			return $content;
		$this->content = $content;
		$this->view = 'layout.php';
	}
	
	/**
	@Filter('output')
	*/
	public function preSendingAction($args) {
		$content = $args[0];
		try {
			$type = Response::getHeader('Content-Type');
			
			if($type != 'text/html')
				return $content;
		}
		catch(Exception $e) {
		}
			
		if(is_array(Coxis::get('layout'))
			&& sizeof(Coxis::get('layout'))>=2 && $content!==null)
			return Router::run(Coxis::get('layout', 0), Coxis::get('layout', 1), $content, $this);
		else
			return $content;
	}
	
	/**
	@Hook('start')
	*/
	public function initAction($params) {
		HTML::setTitle(MySettings::get('name'));
		//~ HTML::setDescription('');
		//~ HTML::setKeywords('');
	}
}