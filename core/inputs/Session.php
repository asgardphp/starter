<?php
namespace Coxis\Core\Inputs;

class Session extends InputsBag {
	function __construct() {
		try {
			$this->start();
			$this->inputs = new InputsBag($_SESSION);
		} catch(\ErrorException $e) {
			$this->inputs = new InputsBag();
		}
	}
	  
	public function set($name, $value) {
		if(isset($_SESSION))
			$_SESSION[$name] = $value;
		parent::set($name, $value);
		return $this;
	}

	public function start() {
		if(!headers_sent()) {
			if(\GET::get('PHPSESSID') !== null)
				session_id(\GET::get('PHPSESSID'));
			elseif(\POSt::get('PHPSESSID') !== null)
				session_id(\POST::get('PHPSESSID'));
			session_start();
		}
	}
}