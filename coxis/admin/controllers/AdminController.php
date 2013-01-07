<?php
namespace Coxis\Admin\Controllers;

class AdminController extends \Coxis\Core\Controller {
	public function layout($content) {
		$this->content = $content;
		\Memory::set('htmlLayout', false);
		$this->setRelativeView('layout.php');
	}
}