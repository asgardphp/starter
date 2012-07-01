<?php
/**
@Prefix('formations')
*/
class FormationController extends Controller {
	/**
	@Route('')
	*/
	public function indexAction($request) {
		//~ $this->formations = Formation::find();
	}
	
	/**
	@Route('liste')
	*/
	public function listeAction($request) {
		$page = isset($request['page']) ? $request['page']:1;
		list($this->formations, $this->paginator) = Paginator::paginate('formation', $page, array());
	}
	
	/**
	@Route('presentation')
	*/
	public function presentationAction($request) {
		//~ $this->formations = Formation::find();
	}

	/**
	@Route(':id/:slug')
	*/
	public function showAction($request) {
		if(!($this->formation = Formation::load($request['id'])))
			$this->forward404();
			
		//~ $this->canonical(url_for(array('page', 'show'), array('id' => $this->page->id, 'slug' => $this->page->slug)));
		
		//~ HTML::setTitle($this->page->meta_title!='' ? $this->page->meta_title:$this->page->title);
		//~ HTML::setKeywords($this->page->meta_keywords);
		//~ HTML::setDescription($this->page->meta_description);
	}
}