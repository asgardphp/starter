<?php
Config::set('slideshow', 'width', 1000);
Config::set('slideshow', 'height', 768);

\Coxis\App\Imagecache\Libs\ImageCache::addPreset('imagecache', array(
	'resize'	=>	array(
		'width'	 =>	Config::get('slideshow', 'width'),
	),
	'crop'	=>	array(
		'height' =>	Config::get('slideshow', 'height'),
	)
));

\Coxis\App\Admin\Libs\AdminMenu::$menu[0]['childs'][] = array('label' => 'Slideshow', 'link' => 'slideshow');

\Coxis\App\Admin\Libs\AdminMenu::$home[] = array('img'=>\URL::to('slideshow/icon.svg'), 'link'=>'slideshow', 'title' => __('Slideshow'), 'description' => __('Slideshow images'));