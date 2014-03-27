<?php
namespace App;

class Bundle extends \Asgard\Core\BundleLoader {
	public function load($queue) {
		$queue->addBundles(glob(__DIR__.'/*', GLOB_ONLYDIR));
		parent::load($queue);
	}

	public function run() {
	}
}