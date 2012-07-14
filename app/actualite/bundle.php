<?php
class ActualiteBundle extends Bundle {
	public static function configure() {
		AdminMenu::$menu[0]['childs'][] = array('label' => 'Actualites', 'link' => 'actualites');
	}
}