<?php
class Kernel extends \Asgard\Core\Kernel {
	public function __construct() {
		$root = realpath(dirname(__DIR__));
		$this['webdir'] = dirname(__DIR__).'/web';
		
		parent::__construct($root);
	}

	public function getBundles() {
		return array_merge(
			array(
				new \Asgard\Core\Bundle,
				new \Asgard\Orm\Bundle,
				new \Asgard\Behaviors\Bundle,
				new \Asgard\Migration\Bundle,
				new \Asgard\Db\Bundle,
				new \Asgard\Entity\Bundle,
				new \Asgard\Files\Bundle,
				new \Asgard\Form\Bundle,
				new \Asgard\Hook\Bundle,
				new \Asgard\Http\Bundle,
				new \Asgard\Utils\Bundle,
				new \Asgard\Validation\Bundle,
				new \Asgard\Data\Bundle
			),
			glob(__DIR__.'/*', GLOB_ONLYDIR)
		);
	}
}