<?php
class AdminBundle extends \Coxis\Core\Bundle {
	public static function configure() {
		ImageCache::addPreset('admin_thumb', array(
			'resize'	=>	array(
				'height'	=>	100,
				'force'	=>	false
			)
		));
	}
}