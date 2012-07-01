<?php
class ProfesseurBundle extends Bundle {
	public static function configure() {
		AdminMenu::$menu[0]['childs'][] = array('label' => 'Professeurs', 'link' => 'professeurs');
	}
}