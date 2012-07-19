<?php
/**
@Prefix('actualites')
*/
class ActualiteController extends Controller {
	/**
	@Route('')
	*/
	public function indexAction($request) {
		$page = isset($request['page']) ? $request['page']:1;
		list($this->actualites, $this->paginator) = Paginator::paginate('\Coxis\App\Actualite\Models\Actualite', $page, array());
		//~ d($this->actualites);
		//~ $this->actualites = Actualite::find();
	}

	/**
	@Route(':id/:slug')
	*/
	public function showAction($request) {
		if(!($this->actualite = Actualite::load($request['id'])))
			$this->forward404();
			
		//~ $this->canonical(url_for(array('page', 'show'), array('id' => $this->page->id, 'slug' => $this->page->slug)));
		
		//~ HTML::setTitle($this->page->meta_title!='' ? $this->page->meta_title:$this->page->title);
		//~ HTML::setKeywords($this->page->meta_keywords);
		//~ HTML::setDescription($this->page->meta_description);
	}
	
	/**
	@Route('widget')
	*/
	public function widgetAction($request) {
		$this->listAction($request);
		$this->view = 'list.php';
		Coxis::set('layout', false);
	}
	
	public function listAction($request) {
		$page = isset($request['page']) ? $request['page']:1;
		list($this->actualites, $this->paginator) = \Coxis\Core\Tools\Paginator::paginate('\Coxis\App\Actualite\Models\Actualite', $page, array(), 3);
	}
}