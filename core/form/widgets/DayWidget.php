<?php
namespace Coxis\Core\Form\Widgets;

class DayWidget extends Widget {
	function __construct($params=array()) {
		$params['validation']['type'] = 'integer';
		$params['choices'] = array('Day');
		foreach(range(1, 31) as $i)
			$params['choices'][$i] = $i;
		parent::__construct($params);
	}
}