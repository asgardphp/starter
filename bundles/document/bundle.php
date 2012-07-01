<?php
class DocumentBundle extends Bundle {
	public static function configure() {
		AdminMenu::$menu[0]['childs'][] = array('label' => 'Documents', 'link' => 'documents');
	}
}