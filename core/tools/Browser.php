<?php
namespace Coxis\Core\Tools;

class Browser {
	public $session = array();
	public $cookies = array();
	public $last;

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
		\Coxis\Core\Context::get('autoloader')->preloadDir(_DIR_.'core');
		// d(\Coxis\Core\Context::get('autoloader')->preloaded);

		#build request
		$get = array();
		$infos = parse_url($url);
		if(isset($infos['query'])) {
			parse_str($infos['query'], $get);
			$url = preg_replace('/(\?.*)$/', '', $url);
		}
		$request = new Request;
		$request->buildServer();
		$request->setMethod($method);
		$request->get = $get;
		$request->post = $post;
		$request->file = $file;
		$request->cookie = $this->cookies;
		$request->session = $this->session;
		if(sizeof($post)) {
			// $request->body = urlencode($post);
			$request->body = '';
			#todo
		}
		else
			$request->body = $body;

		Context::instance()->request = $request;
		// if(\POST::has('send'))

		#todo redo this with request
		\URL::setURL($url);
		\URL::setServer('localhost');
		\URL::setRoot('');

		$res = FrontController::getResponse();

		$this->last = $res;
		$this->cookies = $request->cookie;
		$this->session = $request->session;

		return $res;
	}

	public function submit($item=0, $to=null, $override=array()) {
		libxml_use_internal_errors(true);
		$orig = new \DOMDocument();
		$orig->loadHTML($this->last->content);
		$node = $orig->getElementsByTagName('form')->item($item);

		$parser = new FormParser;
		$parser->parse($node);
		$parser->clickOn('send');
		$res = $parser->values();
		$this->merge($res, $override);

		return $this->post($to, $res);
	}

	protected function merge(&$arr1, &$arr2) {
		foreach($arr2 as $k=>$v) {
			if(is_array($arr1[$k]) && is_array($arr2[$k])) {
				$this->merge($arr1[$k], $arr2[$k]);
			}
			elseif($arr1[$k] !== $arr2[$k]) {
				$arr1[$k] = $arr2[$k];
			}
		}
	}
}