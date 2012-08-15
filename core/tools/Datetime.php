<?php
namespace Coxis\Core\Tools;

class Datetime extends Time {
	public function __toString() {
		return $this->format('d/m/Y H:i:s');
	}
	
	public static function fromDatetime($v) {
		if(strtotime($v))
			$timestamp = strtotime($v);
		else
			$timestamp = 0;
		return new static($timestamp);
	}
	
	public static function fromDate($v) {
		if(!$v)
			return 0;
		if($v instanceof Time)
			return $v;
		list($d, $m, $y) = explode('/', $v);
		$timestamp = mktime(0, 0, 0, $m, $d, $y);
		return new static($timestamp);
	}
}