<?php
namespace Coxis\Core;

class Request {
	public $data;

	function __construct() {
		$this->data = file_get_contents('php://input');
	}

	public function gets() {
		return Context::get('get');
	}

	public function posts() {
		return Context::get('post');
	}

	public function files() {
		return Context::get('file');
	}

	public function server() {
		return Context::get('server');
	}

	public function cookies() {
		return Context::get('cookie');
	}

	public function session() {
		return Context::get('session');
	}

	public function json() {
		return Context::get('json');
	}

	public function argv() {
		return Context::get('argv');
	}

	public function method() {
		return $this->server()->get('REQUEST_METHOD') ? $this->server()->get('REQUEST_METHOD'):'GET';
	}

	public function setMethod($value) {
		$this->server()->set('REQUEST_METHOD');
		return $this;
	}

	public function ip() {
		return $this->server()->get('REMOTE_ADDR');
	}

	public function setIP($value) {
		$this->server()->set('REMOTE_ADDR');
		return $this;
	}

	public function referer() {
		return $this->server()->get('HTTP_REFERER');
	}

	public function setReferer($value) {
		$this->server()->set('HTTP_REFERER');
		return $this;
	}

	public function data() {
		return $this->data;
	}

	public function setData($value) {
		$this->data = $value;
		return $this;
	}
}