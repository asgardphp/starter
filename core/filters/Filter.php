<?php
namespace Coxis\Core\Filters;
class Filter {
	protected $params;

	function __construct($params=array()) {
		$this->params = $params;
	}

	public function getBeforePriority() {
		return isset($this->params['beforePriority']) ? $this->params['beforePriority']:0;
	}
	public function getAfterPriority() {
		return isset($this->params['afterPriority']) ? $this->params['afterPriority']:0;
	}
}