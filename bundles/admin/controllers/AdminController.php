<?php
class AdminController extends Controller {
	public function layoutAction($content) {
		$this->content = $content;
		$this->view = 'layout.php';
	}
}