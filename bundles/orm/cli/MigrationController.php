<?php
#todo delete old model tables
namespace Coxis\Bundles\ORM\CLI;

class MigrationController extends \Coxis\Core\CLI\CLIController {
	/**
	@Shortcut('automigrate')
	*/
	public function automigrateAction($request) {
		$this->diffAction($request);
		$this->migrateAction($request);
	}

	/**
	@Shortcut('diff')
	*/
	public function diffAction($request) {
		#todo check migration version
		if(!static::uptodate())
			die('You must run all migrations before using diff.');
			
		FileManager::mkdir('migrations');
		echo 'Running diff..'."\n";
	
		echo 'New migration: '.ORMManager::diff();
	}

	/**
	@Shortcut('migrate')
	*/
	public function migrateAction($request) {
		Router::run('Coxis\Core\Cli\DB', 'backup', $request);
		echo 'Migrating...'."\n";

		ORMManager::migrate(true);
	}
}
