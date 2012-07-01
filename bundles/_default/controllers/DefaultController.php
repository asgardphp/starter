<?php
class DefaultController extends Controller {
	/**
	@Route('')
	*/
	public function indexAction($request) {
		$this->canonical('');
	}
}