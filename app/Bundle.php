<?php
namespace App;

class Bundle extends \Coxis\Core\BundleLoader {
	public function load($queue) {
		$queue->addBundles(glob(dirname(__FILE__).'/*', GLOB_ONLYDIR));
		parent::load($queue);
	}

	public function run() {
	}
}