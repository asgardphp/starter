<?php
namespace Coxis\Bundles\Admin\Controllers;

class AdminController extends \Coxis\Core\Controller {
	public function layoutAction($content) {
		$this->content = $content;
		$this->setRelativeView('layout.php');
	}
}