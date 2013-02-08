<?php
namespace Coxis\JS\JQuery;

class JQuery {
	public static function load() {
		HTML::include_js('http://code.jquery.com/jquery-latest.min.js');
	}
}