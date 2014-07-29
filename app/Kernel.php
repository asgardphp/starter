<?php
class Kernel extends \Asgard\Core\Kernel {
	public function __construct() {
		$root = realpath(dirname(__DIR__));
		
		parent::__construct($root);
	}

	public function getBundles() {
		return array_merge(
			[
				new \Asgard\Core\Bundle,
				new \Asgard\Behaviors\Bundle,
				new \Asgard\Jsonentities\Bundle,
				#Composer Bundles - do not remove
			],
			glob(__DIR__.'/*', GLOB_ONLYDIR)
		);
	}
}