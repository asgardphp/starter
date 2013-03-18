<?php
namespace App\Admin\Controllers;

class AdminController extends \App\Core\Controller {
	public function layout($content) {
		$this->content = $content;
		$this->setRelativeView('layout.php');
	}
}