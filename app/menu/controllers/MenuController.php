<?php
/**
@Prefix('menus')
*/
class MenuController extends Controller {
	/**
	@Route('')
	*/
	public function indexAction($request) {
		$this->menu = Menu::loadByName('Principal');
	}
}