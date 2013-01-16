<?php
namespace App\Admin\Controllers;

class AdminController extends \App\Core\Controller {
	public function layout($content) {
		$this->content = $content;
		\Memory::set('htmlLayout', false);
		$this->setRelativeView('layout.php');
	}
}