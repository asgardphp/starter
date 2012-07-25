<?php
namespace Coxis\Core\Form\Widgets;

class YearWidget extends Widget {
	function __construct($params=array()) {
		$params['validation']['type'] = 'integer';
		$params['choices'] = array('Year');
		foreach(array_reverse(range(date('Y')-100, date('Y'))) as $i)
			$params['choices'][$i] = $i;
		parent::__construct($params);
	}
}