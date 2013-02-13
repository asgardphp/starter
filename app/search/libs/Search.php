<?php
namespace App\Search\Controllers;

class Search {
	public static function searchModels($modelName, $term) {
		$orm = $modelName::orm();
		$conditions = array();
		foreach($modelName::propertyNames() as $prop)
			$conditions[] = "$prop LIKE '%$term%'";
		return $orm->where(array('or'=>$conditions))->get();
	}
}