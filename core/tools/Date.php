<?php
class Date {
	public $timestamp = 0;

	public function __construct($t=null) {
		if(!$t)
			$t = time();
		$this->timestamp = $t;
	}
	
	public static function fromDatetime($v) {
		if(strtotime($v))
			$timestamp = strtotime($v);
		else
			$timestamp = 0;
		return new static($timestamp);
	}

	public function iso() {
		return date('r', $this->timestamp);
	}
	
	public function __toString() {
		return $this->format('d/m/Y H:i:s');
	}
	
	public function datetime() {
		return $this->format('Y-m-s H:i:s');
	}
	
	public function date($format) {
		return $this->format('d/m/Y');
	}
	
	public function format($format) {
	//~ try {
		return date($format, $this->timestamp);
		//~ }catch(Exception $e){d($this->timestamp, $e);}
	}

	public static function dateToSQLFormat($date) {
		if($date=='')
			return '';
		list($d, $m, $y) = explode('/', $date);
		return $y.'-'.$m.'-'.$d;
	}
	
	public static function SQLFormatToDate($date) {
		if($date=='')
			return '';
		list($y, $m, $d) = explode('-', $date);
		return $d.'/'.$m.'/'.$y;
	}
	
	public static function toTimestamp($value) {
		try {
			list($d, $m, $y) = explode('/', $value);
			return mktime(0, 0, 0, $m, $d, $y);
		} catch(Exception $e) {
			return 0;
		}
	}
	
	public static function toDate($value) {
		return date('d/m/Y', $value);
	}
}