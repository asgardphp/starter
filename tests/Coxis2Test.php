<?php
//~ require_once('../coxis.php');

class Coxis2Test extends PHPUnit_Framework_TestCase
{
  public function setUp(){
	//~ $this->browser = new Browser;
		
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
	$this->assertTrue(false, 'test');
	}
}
?>