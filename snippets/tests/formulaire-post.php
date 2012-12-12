<?php
$browser = new Browser;

$url = 'membres/inscription';
$post = array();
$files = array();
$body = '';
$response = $browser->post($url, $post, $files, $body);
if($response->getCode() > 299)
	die('gnark');
else
	die('good');
if(!Membre::loadByPseudo('test'))
	die('gnark');
$this->assertEquals(_pq($browser->get('')->content, 'h1')->html(), 'Coxis');