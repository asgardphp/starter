<?php
class ChoeurBundle extends Bundle {
	public static function configure() {
		AdminMenu::$menu[0]['childs'][] = array('label' => 'Choeurs', 'link' => 'choeurs');
	}
}