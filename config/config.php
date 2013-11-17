<?php
$config = array(
	'all' => array(
		'key'	=>	'coxis',
		'admin'	=>	array(
			'footer'	=>	''
		),
		'salt'	=>	'FCT7f6ew%^',
		'bundles' => array(
			'app',
			_COXIS_DIR_.'core',
			_COXIS_DIR_.'hooks',
			_COXIS_DIR_.'orm',
			_COXIS_DIR_.'auth',
			_COXIS_DIR_.'behaviors',
			_COXIS_DIR_.'captcha',
			_COXIS_DIR_.'ckeditor',
			_COXIS_DIR_.'cli',
			_COXIS_DIR_.'db',
			_COXIS_DIR_.'files',
			_COXIS_DIR_.'form',
			_COXIS_DIR_.'hook',
			_COXIS_DIR_.'js',
			_COXIS_DIR_.'minify',
			_COXIS_DIR_.'orm',
			_COXIS_DIR_.'seo',
			_COXIS_DIR_.'utils',
			_COXIS_DIR_.'validation',
		),
		'imagecache'	=>	false,
		'locale'	=>	'fr',
		'locales'	=>	array(
			'fr', 'en'
		),
		'cache'	=>	array(
			'method'	=>	'apc',
			// 'method'	=>	'file',
		),
		'profiler'	=>	false,
		'error_display' => true,
	),
	'dev'	=>	array(
		'phpcache'	=>	false,
		// 'phpcache'	=>	true,
		'profiler'	=>	false,
		// 'profiler'	=>	true,
	),
	'test'	=>	array(
		'phpcache'	=>	false,
	),
	'prod'	=>	array(
		'error_display' => false,
	),
);