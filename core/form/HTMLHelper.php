<?php
class HTMLHelper {
	public static function tag($tag, $attrs) {
		$attrs_str = '';
		
		foreach($attrs as $k=>$v)
			$attrs_str .= $k.'="'.$v.'" ';
		
		return '<'.$tag.' '.trim($attrs_str).'>';
	}
	
	public static function endTag($tag) {
		return '</'.$tag.'>';
	}
}