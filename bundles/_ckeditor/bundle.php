<?php
class _ckeditorBundle extends Bundle {
	public static function configure() {
		AdminMenu::$menu[] = array('label' => 'Media', 'link' => '../bundles/_ckeditor/kcfinder/browse.php?type=images&CKEditor=editor_article&CKEditorFuncNum=2&langCode=fr');
	}
}