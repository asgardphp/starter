<?php
namespace App\Standard\Controllers;

class DefaultController extends \Coxis\Core\Controller {
	public function configure() {
		if($this->action == 'indexAction')
			$this->addFilter(new \Coxis\Core\Filters\PageCaching());
	}

	/**
	@Route('')
	*/
	public function indexAction($request) {
		Profiler::checkpoint('In default controller');
	}

	public function _404Action() {
	}
	
	public function layout($content) {
		$this->content = $content;
	}
}