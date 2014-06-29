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
	}
	
	public static function layout($content) {
		return \Asgard\Http\PHPTemplate::renderFile(dirname(__DIR__).'/html/default/layout.php', ['content'=>$content]);
	}
}