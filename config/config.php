<?php
$config = array(
	'all' => array(
		//~ 'routes' => array(
			//~ #admin app
			//~ 'admin/:controller/:id/:action/:obj_id'		=>	array(),
			//~ 'admin/:controller/:id/:action'		=>	array(),
			//~ 'admin/:controller/:action'			=>	array(),
			//~ 'admin/:controller'					=>	array('action' => 'index'),
			//~ 'admin'									=>	array('controller' => 'default', 'action' => 'index'),
			
			//~ #default app
			//~ 'appartements/:id'					=>	array('controller' => 'appartement', 'action' => 'show'),
			//~ 'appartements'							=>	array('controller' => 'appartement', 'action' => 'index'),
			//~ ':controller/:action/:id'				=>	array(),
			//~ ':controller/:action'					=>	array(),
			//~ ':controller'								=>	array('action' => 'index'),
			//~ '											=>	array('controller' => 'default', 'action' => 'index'),
		//~ ),
	
		'admin'	=>	array(
			'footer'	=>	'Si vous rencontrez une difficulté contactez-nous au ...'
		),
		'salt'	=>	'FCT7f6ew%^',
		'imagecache'	=>	false,
	),
);