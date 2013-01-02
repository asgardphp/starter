<?php
namespace Coxis\Bundles\Admin\Controllers;

class AdminController extends \Coxis\Core\Controller {
	public function layoutAction($content) {
		$this->content = $content;
		\Memory::set('htmlLayout', false);
		$this->setRelativeView('layout.php');
	}
}