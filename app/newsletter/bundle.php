<?php
class newsletterBundle extends Bundle {
	public static function configure() {
		AdminMenu::$menu[0]['childs'][] = array('label' => 'Inscrits', 'link' => 'inscrits');
		AdminMenu::$menu[0]['childs'][] = array('label' => 'Newsletter', 'link' => 'newsletter');
	}
}