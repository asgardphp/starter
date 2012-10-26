<?php
namespace Coxis\Core;

class Request {
	public $data;

	public $get = array();
	public $post = array();
	public $file = array();
	public $json = array();
	public $server = array();
	public $cookie = array();
	public $session = array();
	public $argv = array();

	function __construct($default=true) {
		global $argv;

		if($default) {
			$this->get = $_GET;
			$this->post = $_POST;
			$this->file = $_FILES;
			$this->cookie = $_COOKIE;
			$this->server = $_SERVER;
			$this->argv = $argv;
			try {
				$this->start();
				$this->session = $_SESSION;
			} catch(\ErrorException $e) {}
			$this->data = file_get_contents('php://input');
			try {
				$this->json = json_decode($this->data);
			} catch(\ErrorException $e) {}
		}
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

	public function data() {
		return $this->data;
	}

	public function setData($value) {
		$this->data = $value;
		return $this;
	}
}