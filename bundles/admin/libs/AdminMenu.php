<?php
namespace Coxis\Bundles\Admin\Libs;

class AdminMenu {
	public static $menu = null;

	public static function _autoload() {
		static::$menu = array(array(
			'label'	=>	__('Content'),
			'link'	=>	'#',
			'childs'	=>	array()
		));
	}
}