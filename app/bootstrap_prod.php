<?php
\Asgard\Core\App::get('hook')->hook('behaviors_pre_load', function($chain, $entityDefinition) {
	if(!isset($entityDefinition->behaviors['Asgard\Behaviors\TimestampsBehavior']))
		$entityDefinition->behaviors['Asgard\Behaviors\TimestampsBehavior'] = true;

	if(!isset($entityDefinition->behaviors['orm']))
		$entityDefinition->behaviors['Asgard\Orm\ORMBehavior'] = true;
});