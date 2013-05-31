<?php
namespace Coxis\App\Admin\Libs;

#todo not static
class AdminMenu {
	public static $menu = null;
	public static $home = array();

	public static function _autoload() {
		static::$menu = array(array(
			'label'	=>	__('Content'),
			'link'	=>	'#',
			'childs'	=>	array()
		));
	}
}