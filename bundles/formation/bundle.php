<?php
class FormationBundle extends Bundle {
	public static function configure() {
		AdminMenu::$menu[0]['childs'][] = array('label' => 'Formations', 'link' => 'formations');
	}
}