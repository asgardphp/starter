<?php
class Page extends \Coxis\Core\Model {
	public static $properties = array(
		'title',
		'content',
		'name',
		'url',
	);

	public static $relations = array(	
	);
	
	public static $behaviors = array(	
		'metas' => true,
		'publish' => true,
	);
		
	public static $meta = array(
	);
	
	public function __toString() {
		return (string)$this->title;
	}

	public function url() {
		return \URL::url_for(array('page', 'show'), array('url'=>$this->url));
	}

	public function replaceTags($tags) {
		foreach($tags as $name=>$replace) {
			$this->content = str_replace('{{'.$name.'}}', $replace, $this->content);
		}
	}
}