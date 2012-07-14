<?php
/**
@Prefix('annonces')
*/
class AnnonceController extends Controller {
	/**
	@Route('')
	*/
	public function indexAction($request) {
		$conditions = $this->getAnnonces();
		
		$page = isset($request['page']) ? $request['page']:1;
		list($this->annonces, $this->paginator) = Paginator::paginate('annonce', $page, array('conditions'=>$conditions), 3);
		//~ $this->annonces = Annonce::find();
	}

	/**
	@Route(':id/:slug')
	*/
	public function showAction($request) {
		if(!($this->annonce = Annonce::load($request['id'])))
			$this->forward404();
			
		//~ $this->canonical(url_for(array('page', 'show'), array('id' => $this->page->id, 'slug' => $this->page->slug)));
		//~ Metas::set($this->annonce);
	}

	/**
	@Route('depot')
	*/
	public function depotAction($request) {
		//~ $this->canonical(url_for(array('page', 'show'), array('id' => $this->page->id, 'slug' => $this->page->slug)));
		//~ Metas::set($this->annonce);
		
		$annonce = Annonce::create();
		$this->form = new ModelForm($annonce);
	}

	/**
	@Route('depot/submit')
	*/
	public function depotsubmitAction($request) {
		$annonce = Annonce::create();
		$this->form = new ModelForm($annonce);
		if($this->form->isSent()) {
			try {
				$this->form->save();
				Response::setCode(200)->send();
			}
			catch(ModelException $e) {
				foreach($e->errors as $err)
					echo $err."\n";
				Response::setCode(500)->send();
			}
		}
	}
	
	public function getAnnonces() {
		$conditions = array();
		
		$criteria = false;
		if(isset($_GET['new'])) {
			$_SESSION['recherche_annonce'] = false;
		}
		elseif(count($_POST) > 0) {
			$criteria = $_POST;
			$_SESSION['recherche_annonce'] = $_POST;
		}
		elseif(isset($_SESSION['recherche_annonce']) && $_SESSION['recherche_annonce']) {
			$criteria = $_SESSION['recherche_annonce'];
		}
		
		if($criteria) {
			$categorie = $criteria['categorie'];
			$region = isset($criteria['region']) ? $criteria['region']:false;
			
			if($categorie)
				$conditions['categorie LIKE ?'] = array('%'.$criteria['categorie'].'%');
			if($region)
				$conditions['region LIKE ?'] = array('%'.$criteria['region'].'%');
		}
		
		return $conditions;
	}
}