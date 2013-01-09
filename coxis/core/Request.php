<?php
namespace Coxis\Core;

class Request implements \ArrayAccess {
	public $get = array();
	public $post = array();
	public $file = array();
	public $json = array();
	public $server = array();
	public $cookie = array();
	public $session = array();
	public $argv = array();
	public $data = '';

	public $params = array(
		'format'	=>	'html',
	);

	public function getParam($name) {
		return $this->params[$name];
	}
	public function setParam($name, $value) {
		$this->params[$name] = $value;
		return $this;
	}

	public function offsetSet($offset, $value) {
		if (is_null($offset))
			$this->get[] = $value;
		else
			$this->get[$offset] = $value;
	}
	public function offsetExists($offset) {
		return isset($this->get[$offset]);
	}
	public function offsetUnset($offset) {
		unset($this->get[$offset]);
	}
	public function offsetGet($offset) {
		return isset($this->get[$offset]) ? $this->get[$offset] : null;
	}

	public static function createFromGlobals() {
		global $argv;

		$request = new static;
		$request->get = $_GET;
		$request->post = $_POST;
		$request->file = $_FILES;
		$request->cookie = $_COOKIE;
		$request->server = $_SERVER;
		$request->argv = $argv;
		try {
			$request->start();
			$request->session = $_SESSION;
		} catch(\ErrorException $e) {}
		$request->body = file_get_contents('php://input');
		try {
			$request->json = json_decode($request->body);
		} catch(\ErrorException $e) {}

		if(isset($matches[1]))
			\Request::setParam('format', $matches[1]);

		return $request;
	}

	public function buildServer() {
		$this->server = $_SERVER;
		return $this;
	}

	public function start() {
		if(!headers_sent()) {
			if(isset($this->get['PHPSESSID']))
				session_id($this->get['PHPSESSID']);
			elseif(isset($this->post['PHPSESSID']))
				session_id($this->post['PHPSESSID']);
			session_start();
		}
	}

	public function method() {
		return isset($this->server['REQUEST_METHOD']) ? $this->server['REQUEST_METHOD']:'GET';
	}

	public function setMethod($value) {
		$this->server['REQUEST_METHOD'] = $value;
		return $this;
	}

	public function ip() {
		return $this->server['REMOTE_ADDR'];
	}

	public function setIP($value) {
		$this->server['REMOTE_ADDR'] = $value;
		return $this;
	}

	public function referer() {
		return $this->server['HTTP_REFERER'];
	}

	public function setReferer($value) {
		$this->server['HTTP_REFERER'] = $value;
		return $this;
	}

	public function body() {
		return $this->body;
	}

	public function setbody($value) {
		$this->body = $value;
		return $this;
	}
}