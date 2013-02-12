<?php
/**
@Prefix('actualites')
*/
class ActualiteController extends Controller {
	/**
	@Route('')
	*/
	public function indexAction($request) {
		$this->actualites = Actualite::published()->get();
	}

	/**
	@Route(':id')
	*/
	public function showAction($request) {
		if(!($this->actualite = Actualite::loadPublished($request['id'])))
			$this->notfound();

		SEO::canonical($this, $this->actualite->url());
	}
}