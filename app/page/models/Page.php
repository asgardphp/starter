<?php
class Page extends Model {
	/**
	@Length(255)
	@DefaultValue('Le titre')
	*/
	public $title;
	public $name;
	
	public $content;
	
	#General
	public function __toString() {
		return (string)$this->title;
	}
	
	public static $order_by = 'title ASC';
	
	public static $files = array(	
		//~ 'logo' => array(
			//~ 'dir'	=>	'forum/partner_logos/',
			//~ 'type'	=>	'image',
			//~ 'required'	=>	true,
			//~ 'multiple'	=>	true,
		//~ )
	);
	
	public static $relationships = array(
		//~ 'comment'	=>	array(
			//~ 'model'	=>	'comment',
			//~ 'type'		=>	'hasOne',
			//~ 'required'	=>	true,
		//~ )
	);
	
	public static $behaviors = array(	
		'sortable'		=>	true,
		'page'			=>	true,
	);
}
?>