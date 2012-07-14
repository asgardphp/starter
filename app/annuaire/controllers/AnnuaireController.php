<?php
/**
@Prefix('annuaire')
*/
class AnnuaireController extends Controller {
	/**
	@Route('')
	*/
	public function indexAction($request) {
		$this->annonces = Annonce::find();
	}

	/**
	@Route('depot-choeur')
	*/
	public function depot_choeurAction($request) {
		//~ $this->canonical(url_for(array('page', 'show'), array('id' => $this->page->id, 'slug' => $this->page->slug)));
		//~ Metas::set($this->annonce);
		
		$choeur = Choeur::create();
		$this->form = new ModelForm($choeur);
		
		
		if($this->form->isSent()) {
			try {
				$this->form->save();
				Response::setCode(200)->send();
			}
			catch(FormException $e) {
				Response::setCode(500)->sendHeaders();
				foreach($e->errors as $err)
					echo $err."\n";
				Response::send();
			}
		}
	}

	/**
	@Route('depot-professeur')
	*/
	public function depot_professeurAction($request) {
		//~ $this->canonical(url_for(array('page', 'show'), array('id' => $this->page->id, 'slug' => $this->page->slug)));
		//~ Metas::set($this->annonce);
		
		$professeur = Professeur::create();
		$this->form = new ModelForm($professeur);
	}

	/**
	@Route('depot-professeur/submit')
	*/
	public function depot_professeursubmitAction($request) {
		$professeur = Professeur::create();
		$this->form = new ModelForm($professeur);
		if($this->form->isSent()) {
			try {
				$this->form->save();
				Response::setCode(200)->send();
			}
			catch(FormException $e) {
				Response::setCode(500)->sendHeaders();
				foreach($e->errors as $err)
					echo $err."\n";
				Response::send();
			}
		}
	}

	/**
	@Route('choeurs/:id/:slug')
	*/
	public function fiche_choeurAction($request) {
		if(!($this->choeur = Choeur::load($request['id'])))
			$this->forward404();
			
		$choeurs = Choeur::find($this->getChoeurs());
		$this->prec = false;
		$this->suiv = false;
		foreach($choeurs as $k=>$choeur)
			if($choeur->id == $this->choeur->id) {
				try {
					$this->prec = 'annuaire/choeurs/'.$choeurs[$k-1]->id.'/'.$choeurs[$k-1]->slug;
				} catch(Exception $e){}
				try {
					$this->suiv = 'annuaire/choeurs/'.$choeurs[$k+1]->id.'/'.$choeurs[$k+1]->slug;
				} catch(Exception $e){}
				return;
			}
		//~ $this->canonical(url_for(array('page', 'show'), array('id' => $this->page->id, 'slug' => $this->page->slug)));
		//~ Metas::set($this->annonce);
	}

	/**
	@Route('professeurs/:id/:slug')
	*/
	public function fiche_professeurAction($request) {
		if(!($this->professeur = Professeur::load($request['id'])))
			$this->forward404();
			
		$professeurs = Professeur::find($this->getProfesseurs());
		$this->prec = false;
		$this->suiv = false;
		foreach($professeurs as $k=>$professeur)
			if($professeur->id == $this->professeur->id) {
				try {
					$this->prec = 'annuaire/professeurs/'.$professeurs[$k-1]->id.'/'.$professeurs[$k-1]->slug;
				} catch(Exception $e){}
				try {
					$this->suiv = 'annuaire/professeurs/'.$professeurs[$k+1]->id.'/'.$professeurs[$k+1]->slug;
				} catch(Exception $e){}
				return;
			}
		//~ $this->canonical(url_for(array('page', 'show'), array('id' => $this->page->id, 'slug' => $this->page->slug)));
		//~ Metas::set($this->annonce);
	}
	
	public function getChoeurs() {
		$conditions = array();
		
		$criteria = false;
		if(isset($_GET['new'])) {
			$_SESSION['recherche_choeur'] = false;
		}
		elseif(count($_POST) > 0) {
			$criteria = $_POST;
			$_SESSION['recherche_choeur'] = $_POST;
		}
		elseif(isset($_SESSION['recherche_choeur']) && $_SESSION['recherche_choeur']) {
			$criteria = $_SESSION['recherche_choeur'];
		}
		
		if($criteria) {
			$nom = $criteria['nom'];
			$ville = $criteria['ville'];
			$repertoire = isset($criteria['repertoire']) ? $criteria['repertoire']:false;
			$type = isset($criteria['type']) ? $criteria['type']:false;
			$region = isset($criteria['region']) ? $criteria['region']:false;
			
			if($nom)
				$conditions['nom LIKE ?'] = array('%'.$criteria['nom'].'%');
			if($ville)
				$conditions['ville LIKE ?'] = array('%'.$criteria['ville'].'%');
			if($repertoire)
				$conditions['style_musical LIKE ?'] = array('%'.$criteria['repertoire'].'%');
			if($type)
				$conditions['type_choeurs LIKE ?'] = array('%'.$criteria['type'].'%');
			if($region)
				$conditions['region LIKE ?'] = array('%'.$criteria['region'].'%');
		}
		
		return $conditions;
	}
	
	public function getProfesseurs() {
		$conditions = array();
		
		$criteria = false;
		if(isset($_GET['new'])) {
			$_SESSION['recherche_choeur'] = false;
		}
		elseif(count($_POST) > 0) {
			$criteria = $_POST;
			$_SESSION['recherche_professeur'] = $_POST;
		}
		elseif(isset($_SESSION['recherche_professeur']) && $_SESSION['recherche_choeur']) {
			$criteria = $_SESSION['recherche_professeur'];
		}
		
		if($criteria) {
			$nom = $criteria['nom'];
			$ville = $criteria['ville'];
			$repertoire = isset($criteria['repertoire']) ? $criteria['repertoire']:false;
			$region = isset($criteria['region']) ? $criteria['region']:false;
			
			if($nom)
				$conditions['nom LIKE ?'] = array('%'.$criteria['nom'].'%');
			if($ville)
				$conditions['ville LIKE ?'] = array('%'.$criteria['ville'].'%');
			if($repertoire)
				$conditions['style_musical LIKE ?'] = array('%'.$criteria['repertoire'].'%');
			if($region)
				$conditions['region LIKE ?'] = array('%'.$criteria['region'].'%');
		}
	}

	/**
	@Route('recherche-choeurs')
	*/
	public function recherche_choeurAction($request) {
		$conditions = $this->getChoeurs();
		
		$page = isset($request['page']) ? $request['page']:1;
		list($this->choeurs, $this->paginator) = Paginator::paginate('choeur', $page, array('conditions'=>$conditions), 3);
		
		//~ if(!($this->annonce = Annonce::load($request['id'])))
			//~ $this->forward404();
			
		//~ $this->canonical(url_for(array('page', 'show'), array('id' => $this->page->id, 'slug' => $this->page->slug)));
		//~ Metas::set($this->annonce);
	}

	/**
	@Route('recherche-professeurs')
	*/
	public function recherche_professeurAction($request) {
		$conditions = $this->getProfesseurs();
		
		$page = isset($request['page']) ? $request['page']:1;
		list($this->professeurs, $this->paginator) = Paginator::paginate('professeur', $page, array('conditions'=>$conditions), 3);
	
		//~ if(!($this->annonce = Annonce::load($request['id'])))
			//~ $this->forward404();
			
		//~ $this->canonical(url_for(array('page', 'show'), array('id' => $this->page->id, 'slug' => $this->page->slug)));
		//~ Metas::set($this->annonce);
	}
}