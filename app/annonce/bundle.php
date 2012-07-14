<?php
class AnnonceBundle extends Bundle {
	public static function configure() {
		AdminMenu::$menu[0]['childs'][] = array('label' => 'Annonces', 'link' => 'annonces');
	}
}