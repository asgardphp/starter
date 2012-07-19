<?php
class AdminBundle extends \Coxis\Core\Bundle {
	public static function configure() {
		\Coxis\Bundles\Imagecache\Libs\ImageCache::addPreset('admin_thumb', array(
			'resize'	=>	array(
				'height'	=>	100,
				'force'	=>	false
			)
		));
	}
}