<?php
namespace Coxis\Core;

#todo handle priorities
class HookChain {
	public $calls;
	private $continue = true;
	public $executed = 0;

	function __construct($calls=array()) {
		$this->calls = $calls;
	}

	public function run($args, $filters) {
		foreach($this->calls as $call) {
			$res = call_user_func_array($call, array_merge(array($this), $args, $filters));
			$this->executed++;
			if($res !== null)
				return $res;
			if(!$this->continue)
				return;
		}
	}

	public function stop() {
		$this->continue = false;
	}
}