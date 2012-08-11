<?php
class Mailing extends \Coxis\Core\ORM\ModelORM {
	public static $properties = array(
					'titre'	=>	array(
							),
					'contenu'	=>	array(
							),
			);
	
	public static $files = array(	
		);
	
	public static $relationships = array(	
		);
	
	public static $behaviors = array(	
		);
		
	public static $meta = array(
			);
	
	public function __toString() {
		return $this->titre;
	}
}