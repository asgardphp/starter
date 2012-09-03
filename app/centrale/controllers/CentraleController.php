<?php
/**
@Prefix('centrales')
*/
class CentraleController extends Controller {
	/**
	@Route('')
	*/
	public function indexAction($request) {
		$this->centrales = Centrale::find();
	}

	/**
	@Route(':id')
	*/
	public function showAction($request) {
		if(!($this->centrale = Centrale::load($request['id'])))
			$this->forward404();
			
		//~ $this->canonical(url_for(array('page', 'show'), array('id' => $this->page->id, 'slug' => $this->page->slug)));
		
		//~ HTML::setTitle($this->page->meta_title!='' ? $this->page->meta_title:$this->page->title);
		//~ HTML::setKeywords($this->page->meta_keywords);
		//~ HTML::setDescription($this->page->meta_description);
	}
}