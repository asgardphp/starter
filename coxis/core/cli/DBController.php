<?php
namespace Coxis\Core\Cli;

class DBController extends CLIController {
	public function dumpAction($request) {
		$output = $request[0];
		echo 'Dumping data into '.$output."\n";

		FileManager::mkdir(dirname($output));
		$config = \Config::get('database');
		$cmd = 'mysqldump -u '.$config['user'].' '.($config['password'] ? '-p'.$config['password']:'').' '.$config['database'].' > '.$output;
		exec($cmd);

		/*
		require_once('vendors/yaml/sfYamlDumper.php');
		$yaml = new sfYamlDumper();

		$all = array();
		$tables = DB::query('SHOW TABLES')->all();
		foreach($tables as $table) {
			$table_name = array_shift($table);
			
			$data = DB::query('SELECT * FROM '.$table_name)->all();
			if(sizeof($data) > 0)
				$all[$table_name] = $data;
		}
		$yml = $yaml->dump($all, 2);
		FileManager::mkdir(dirname($output));
		file_put_contents($output, $yml);
		*/
	}
	
	public function backupAction($request) {
		$request[] = 'backup/data/'.time().'.sql';
		// $request[] = 'backup/data/'.time().'.yml';
		$this->dumpAction($request);
	}
	
	public function loadAllAction($request) {
		die('TODO');
	}
	
	public function loadAction($request) {
		die('TODO');
	}
}