<?php
class PageController extends Controller {
	/**
	@Route(':name')
	*/
	public function showAction($request) {
		if(!($this->page = Page::loadByName($request['name'])))
			$this->forward404();
		
		$this->canonical(url_for(array('page', 'show'), array('name'=>$this->page->name)));
		Metas::set($this->page);
	}
}