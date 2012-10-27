<?php
namespace Coxis\Core\Tools;

class Browser {
	public $session = array();
	public $cookies = array();

	public function get($url='', $body='') {
		return $this->req($url, 'GET', array(), array(), $body);
	}

	public function post($url='', $post=array(), $files=array(), $body='') {
		return $this->req($url, 'POST', $post, $files, $body);
	}

	public function put($url='', $post=array(), $files=array(), $body='') {
		return $this->req($url, 'PUT', $post, $files, $body);
	}

	public function delete($url='', $body='') {
		return $this->req($url, 'DELETE', array(), array(), $body);
	}

	public function req(
			$url='',
			$method='GET',
			$post=array(),
			$file=array(),
			$body=''
		) {
		#new context
		$rand = Tools::randstr(10);
		Context::setDefault($rand);
		\Coxis\Core\Context::get('autoloader')->preloadDir('core');

		#build request
		$get = array();
		$infos = parse_url($url);
		if(isset($infos['query'])) {
			parse_str($infos['query'], $get);
			$url = preg_replace('/(\?.*)$/', '', $url);
		}
		$request = new Request;
		$request->setMethod('POST');
		$request->get = $get;
		$request->post = $post;
		$request->file = $file;
		$request->cookie = $this->cookies;
		$request->session = $this->session;
		if(sizeof($post))
			$request->body = urlencode($post);
		else
			$request->body = $body;
		$request->buildServer();

		Context::instance()->request = $request;

		#todo redo this with request
		\URL::setURL($url);
		\URL::setServer('localhost');
		\URL::setRoot('');

		$res = require('core/getresponse.php');

		$this->cookies = $request->cookie;
		$this->session = $request->session;

		return $res;
	}
}