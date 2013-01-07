<?php
namespace Coxis\Value\Models;

class Value extends \Coxis\Value\SingleValue {
	public static $properties = array(
		'key',
		'value'    => array(
			'required'    =>    false,
		),
	);
}