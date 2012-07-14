<?php
class Document extends Model {
			public $titre;
	
			public $description;
	
		#General
	public function __toString() {
		return $this->titre;
	}	
		public static $files = array(	
			'document' => array(
			'dir'	=>	'document/',
						'type'	=>	'file',
						'required'	=>	true,
					),
		);
	
	public static $relationships = array(	
		);
	
	public static $behaviors = array(	
			'sortable' => true,
		);
}