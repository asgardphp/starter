<?php
class Slide extends \Coxis\App\Core\Model {
	public static $properties = array(
		'image'	=>	array(
			'type'	=>	'file',
			'filetype'	=>	'image',
			'dir'	=>	'images',
		),
		'description'	=>	array(
		),
	);

	public static $meta = array(
		'order_by' => 'id ASC',
	);
	
	public static function configure($definition) {
		$validation = function($attribute, $value, $params, $validator) {
			if(!$path = $value->get(null, true))
				return;

			list($width, $height) = getimagesize($path);
			if($width === null)
				return;
			if($width < Config::get('slideshow', 'width') || $height < Config::get('slideshow', 'height'))
				return __('This picture is too small.');
		};
		$definition->property('image')->params['size'] = $validation;
	}
	
	public function __toString() {
		return $this->description;
	}
}