<?php
namespace General\Controllers;

class DefaultController extends \Asgard\Http\Controller {
	/**
	 * @Route("")
	 */
	public function indexAction(\Asgard\Http\Request $request) {
	}

	public function _404Action() {
	}

	public function maintenanceAction() {
		$this->set('htmlLayout', false);
		$this->set('layout', false);
		$this->response->setCode(503);
	}
	
	public static function layout($controller, $content) {
		return \Asgard\Templating\PHPTemplate::renderFile(dirname(__DIR__).'/html/default/layout.php', ['controller'=>$controller, 'content'=>$content]);
	}
}