<?php
class ValueBundle extends Bundle {
	public static function configure() {
		AdminMenu::$menu[8] = array('label' => 'Configuration', 'link' => '#', 'childs' => array(
			array('label' => 'Preferences', 'link' => 'preferences'),
			array('label' => 'Administrators', 'link' => 'administrators'),
		));
	}
}