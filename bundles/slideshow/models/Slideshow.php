<?php
class Slideshow extends \Coxis\Core\Model {
	public static $properties = array(
					'image1'	=>	array(
								'type'	=>	'file',
								'filetype'	=>	'image',
								'dir'	=>	'images',
							),
					'description1'	=>	array(
							),
					'image2'	=>	array(
								'type'	=>	'file',
								'filetype'	=>	'image',
								'dir'	=>	'images',
							),
					'description2'	=>	array(
							),
					'image3'	=>	array(
								'type'	=>	'file',
								'filetype'	=>	'image',
								'dir'	=>	'images',
								'required'	=>	false,
							),
					'description3'	=>	array(
								'required'	=>	false,
							),
					'image4'	=>	array(
								'type'	=>	'file',
								'filetype'	=>	'image',
								'dir'	=>	'images',
								'required'	=>	false,
							),
					'description4'	=>	array(
								'required'	=>	false,
							),
			);
	
	public static function configure($definition) {
		$validation = function($attribute, $value, $params, $validator) {
			#todo redo with modelfile
			if($value->tmp_file)
				$path = $value->tmp_file['tmp_name'];
			else
				$path = $value->get(null, true);

			if(!$path)
				return;

			list($width, $height) = getimagesize($path);
			if($width < Config::get('slideshow', 'width') || $height < Config::get('slideshow', 'height'))
				return __('This picture is too small.');
		};

		$definition->property('image1')->params['size'] = $validation;
		$definition->property('image2')->params['size'] = $validation;
		$definition->property('image3')->params['size'] = $validation;
		$definition->property('image4')->params['size'] = $validation;
	}
	
	public function __toString() {
		return 'Slideshow';
	}
}