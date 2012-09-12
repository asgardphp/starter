<?php
$config = array(
	'dev' => array(
		'database' => array(
			'host'			=>	'localhost',
			'user'	=>	'root',
			'password'	=>	'',
			'database'	=>	'recettes',
			'prefix'		=>	'',
		),
		'error_display' => true,
	),
	'test' => array(
		'database' => array(
			'host'			=>	'localhost',
			'user'	=>	'root',
			'password'	=>	'',
			'database'	=>	'coxis3_test',
			'prefix'		=>	'arpa_',
		),
		'error_display' => true,
	),
	'prod' => array(
		'database' => array(
			'host'			=>	'localhost',
			'user'	=>	'root',
			'password'	=>	'a9z8e7',
			'database'	=>	'recettes',
			'prefix'		=>	'',
		),
		'error_display' => false,
	),
);