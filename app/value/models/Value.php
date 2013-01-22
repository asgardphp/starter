<?php
namespace App\Value\Models;

class Value extends \App\Value\SingleValue {
	public static $properties = array(
		'key',
		'value'    => array(
			'required'    =>    false,
		),
	);
}