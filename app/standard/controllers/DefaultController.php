<?php
namespace App\Standard\Controllers;

class DefaultController extends \Coxis\Core\Controller {
	public function configure($request) {
		if($request->route['action'] == 'index')
			$this->addFilter(new \Coxis\Core\Filters\PageCaching);
	}

	/**
	@Route('')
	*/
	public function indexAction($request) {
		d();
		// SEO::canonical($this, 'as');
		Profiler::checkpoint('In default controller');
	}

	public function _404Action() {
	}
	
	public function layout($content) {
		$this->content = $content;
	}
}