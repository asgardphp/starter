<?php
Asgard\Core\App::get('locale')->importLocales('locales');

// if(!\URL::startsWith('admin')) {
	// Actualite::getDefinition()->hook('getorm', function($chain, $orm) {
	// 	$orm->where(array('published'=>1));
	// });
// }