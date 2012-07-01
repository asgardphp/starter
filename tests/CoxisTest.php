<?php
require_once(dirname(__FILE__).'/../coxis.php');

class CoxisTest extends PHPUnit_Framework_TestCase
{
  public function setUp(){
	$this->browser = new Browser;
		
	//~ function myexceptionhandler($e) {
		//~ d();
		//~ if(is_a($e, 'EndException')) {
			//~ return $e->result;
		//~ }
		//~ else {
			//~ return null;
		//~ }
	//~ }
	//~ set_exception_handler('myexceptionhandler');
	
	//~ throw new Exception;
  }
  public function tearDown(){ }
  
    /*
	front
		-ouvrir toutes les pages sans se taper une exception
	admin
		-ouvrir toutes les pages sans se taper une exception
		-sauvegarde des formulaires avec entites existantes sans se taper une exception ou une erreur
    */
    
	public function test1Sample() {
		//test all pages
		//type them manually?
		//generate them?
		/*
		
		*/
	
		$this->get('');
		$this->get('admin');
		$this->get('formations');
		$this->get('annuaire');
		$this->get('annonces');
		$this->get('actualites');
		$this->get('arpa');
		$this->get('contact');
		$this->get('formations/liste');
		$this->get('formations/presentation');
		$this->get('annuaire/recherche-choeurs');
		$this->get('annuaire/recherche-professeurs');
		$this->get('annuaire/depot-choeur');
		$this->get('annuaire/depot-professeur');
		$this->get('actualites/1/reseau-voix-midi-pyrenees');
		$this->get('etudes');
		$this->get('partenaires');
		$this->get('actualites/widget?page=2');
	}
  
  public function get($url) {
	$result = $this->browser->get($url) ? true:false;
	$this->assertTrue($result, 'GET: '.$url);
  }
}
?>