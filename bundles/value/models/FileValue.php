<?php
namespace Coxis\Bundles\Value\Models;

class FileValue extends \Coxis\Bundles\Value\SingleValue {
	public static $properties = array(
		'key',
		'value'    => array(
			'type'	=>	'file',
			'filetype'	=>	'file',
			'dir'	=>	'files',
			'required'    =>    false,
		),
	);
}