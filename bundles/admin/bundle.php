<?php
class AdminBundle extends Bundle {
	public static function configure() {
		ImageCache::addPreset('admin_thumb', array(
			'resize'	=>	array(
				'height'	=>	100,
				'force'	=>	false
			)
		));
	}
}