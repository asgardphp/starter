<?php
/**
@Prefix('actualites')
*/
class ActualiteController extends Controller {
	/**
	@Route('')
	*/
	public function indexAction($request) {
		//~ $actu = new Actualite(2);
		//~ $actu->commentaires = array(2);
		//~ $actu->save();
		//~ d($actu->commentaires);
		/*
		
		$actu = new Commentaire(2);
		$actu->actualite = 2;
		$actu->save();
		d($actu->actualite);
		
		//~ d(Commentaire::with('actualite')->get());
		$actu = new Actualite(2);
		//~ d($actu->commentaires()->sync(array(2)));
		//~ $actu->commentaires()->remove(array(2));
		//~ d($actu->commentaires()->add(2));
		//~ d($actu->commentaires()->delete(2));
		//~ d($actu->commentaires);
		//~ d(Actualite::with('commentaires')->get());
		//~ d(Commentaire::with('actualite')->get());
		
		//~ $actus = Actualite::with('commentaires')->get();
		//~ d($actus[0]->commentaires);
		d($actu->commentaires);
		
		$com = new Commentaire(2);
		d($com->actualite);
		*/
	
		$page = isset($request['page']) ? $request['page']:1;
		list($this->actualites, $this->paginator) = Coxis\App\Actualite\Models\Actualite::paginate($page, 10);
		//~ d($this->actualites);
		//~ $this->actualites = Actualite::find();
		
		#$model->articles	#->articles()->all()	#->articles()->get()
	}

	/**
	@Route(':id/:slug')
	*/
	public function showAction($request) {
	//~ d(Actualite::load($request['id']));
		if(!($this->actualite = Actualite::load($request['id'])))
			$this->forward404();
		
//~ Commentaire::$order_by;
		//~ d('here');

		//~ d(Commentaire::$order_by);
		//~ $com = new Commentaire(2);
		//~ $com = Commentaire::load(2);
		//~ d($com);
		//~ d($com->actualite);
		//~ d($this->actualite->commentaires);
		//~ d($this->actualite->commentaires()->get());
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
		list($this->actualites, $this->paginator) = Coxis\App\Actualite\Models\Actualite::paginate($page, 3);
	}
}