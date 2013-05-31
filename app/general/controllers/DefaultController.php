<?php
namespace Coxis\App\General\Controllers;

class DefaultController extends \Coxis\Core\Controller {
	public function configure($request) {
		// if($request->route['action'] == 'index')
		// 	$this->addFilter(new \Coxis\Core\Filters\PageCaching);
	}

	/**
	@Route('')
	*/
	public function indexAction($request) {
		// SEO::canonical($this, 'as');
		Profiler::checkpoint('In default controller');
	}

	public function _404Action() {
	}
	
	public function layout($content) {
		$this->content = $content;
	}
}