<?php
namespace App\General\Controllers;

class DefaultController extends \Asgard\Core\Controller {
	public function configure($request) {
		// if($request->route['action'] == 'index')
		// 	$this->addFilter(new \Asgard\Core\Filters\PageCaching);
	}

	/**
	@Route('')
	*/
	public function indexAction($request) {
		// SEO::canonical($this, 'as');
		\Asgard\Utils\Profiler::checkpoint('In default controller');
	}

	public function _404Action() {
	}
	
	public function layout($content) {
		$this->content = $content;
	}
}