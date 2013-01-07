<?php
namespace Tests\App\Actualite\Controllers;
/**
@Prefix('actualites')
*/
class ActualiteController extends \Coxis\Core\Controller {
	/**
	@Route('')
	*/
	public function indexAction($request) {
		$page = isset($request['page']) ? $request['page']:1;
		list($this->actualites, $this->paginator) = Coxis\App\Actualite\Models\Actualite::paginate($page, 10);
	}

	/**
	@Route(':id/:slug')
	*/
	public function showAction($request) {
		if(!($this->actualite = Actualite::load($request['id'])))
			$this->forward404();
	}
}