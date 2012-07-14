<?php
class PageBundle extends Bundle {
	public static function configure() {
		AdminMenu::$menu[0]['childs'][] = array('label' => 'Pages', 'link' => 'pages');
	}
}