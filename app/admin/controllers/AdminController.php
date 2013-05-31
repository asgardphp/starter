<?php
namespace Coxis\App\Admin\Controllers;

class AdminController extends \Coxis\App\Core\Controller {
	public function layout($content) {
		$this->content = $content;
		$this->setRelativeView('layout.php');
	}
}