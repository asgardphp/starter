<?php
class DefaultController extends Controller {	
	public function _404Action() {
		Coxis::set('layout', false);
		return 'Jeu introuvable.';
	}
}