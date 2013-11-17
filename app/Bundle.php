<?php
namespace App;

class Bundle extends \Coxis\Core\BundleLoader {
	public function load($queue) {
		foreach(glob(dirname(__FILE__).'/*', GLOB_ONLYDIR) as $d)
			$queue->addBundle($d);
		parent::load($queue);
	}

	public function run() {
	}
}
return new Bundle;