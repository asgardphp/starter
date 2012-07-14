<?php
class CkeditorBundle extends Bundle {
	public static function configure() {
		AdminMenu::$menu[] = array('label' => 'Media', 'link' => '../bundles/ckeditor/kcfinder/browse.php?type=images&CKEditor=editor_article&CKEditorFuncNum=2&langCode=fr');
	}
}