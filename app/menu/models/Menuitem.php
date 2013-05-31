<?php
class Menuitem extends \Coxis\Core\Model {
	public static $properties = array(
		'title'	=>	array(
			'required'	=>	false,
		),
		'fixed_url' => array(
			'required'	=>	false,
		),
		'type'	=>	array(
			'in'	=>	array('none', 'fixed', 'custom', 'item'),
			'required'	=>	false,
			// none
			// fixed url
			// custom object
			// db item
		),
		'custom_id' => array(
			'required'	=>	false,
		),
	);
	
	public static $relations = array(	
		'menu' => array(
			'has'	=>	'one',
			'model'	=>	'Menu',
		),
		'parent' => array(
			'has'	=>	'one',
			'model'	=>	'MenuItem',
		),
		'childs' => array(
			'has'	=>	'many',
			'model'	=>	'MenuItem',
		),
		'item' => array(
			'type'	=>	'belongsTo',
			'polymorphic'	=>	true,
			'model'	=>	'menuitemable',
		),
	);
	
	public static $behaviors = array(
		'sortable'
	);
		
	public static $meta = array(
	);
	
	public function __toString() {
		return (string)$this->title;
	}

	public function url() {
		switch($this->type) {
			case 'none':
				return $this->childs()->first()->url();
				// return null;
			case 'fixed':
				return $this->fixed_url;
			case 'custom':
				return MenuLib::get($this->custom_id);
			case 'item':
				return $this->item->url();
		}
	}
}