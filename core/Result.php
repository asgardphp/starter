<?php
namespace Coxis\Core;

class Result {
	public $headers = array();
	public $content = '';
	
	public function __construct($headers, $content) {
		$this->headers = $headers;
		$this->content = $content;
	}
}