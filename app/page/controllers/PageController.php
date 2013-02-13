<?php
class PageController extends Controller {
	/**
	@Route(':url')
	*/
	public function showAction($request) {
		if(!($this->page = Page::loadByURL($request['url'])) || !$this->page->published)
			$this->notfound();

		// $this->page->replaceTags(array(
		// 	'test'	=>	SearchController::widget('searchWidget'),
		// ));
			
		$this->page->showMetas();
		SEO::canonical($this, $this->page->url());
	}
}