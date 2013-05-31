<?php
namespace Coxis\App\Search\Controllers;

class SearchController extends \Coxis\Core\Controller {
	/**
	@Route('search')
	*/
	public function indexAction($request) {
		$this->searchWidget();
		$this->results = array();

		if($this->form->isSent()) {
			$term = $this->form->term->getValue();
			
			foreach(Search::searchModels('Actualite', $term) as $actualite)
				$this->results[] = array('title'=>$actualite, 'description'=>$actualite->content, 'link'=>$actualite->url());
		}
	}

	public function searchWidget() {
		$this->form = new Form(array('action'=>\URL::url_for(array('App\Search\Controllers\search', 'index'))));
		$this->form->term = new TextField;
	}
}