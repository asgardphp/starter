<?php
$config = array(
	'all' => array(
		'admin'	=>	array(
			'footer'	=>	'Si vous rencontrez une difficulté contactez-nous au ...'
		),
		'salt'	=>	'FCT7f6ew%^',
		'imagecache'	=>	false,
		'locale'	=>	'fr',
		'locales'	=>	array(
			'fr', 'en'
		),
		'cache'	=>	array(
			'method'	=>	'apc',
			//~ 'method'	=>	'file',
		)
	),
	'dev'	=>	array(
		'phpcache'	=>	false,
		// 'phpcache'	=>	true,
	),
);