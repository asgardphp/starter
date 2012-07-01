<?php
class EndException extends Exception {
	public $result = null;
	
	public function __construct($result) {
		$this->result = $result;
	}
}