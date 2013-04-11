<?php
define('_ENV_', 'test');
require('../coxis/core/core.php');
\Coxis::load();

class CSVImporter {
	protected $handle;
	protected $separator;
	protected $keys;
	protected $cb;

	function __construct($file, $separator=',') {
		$this->handle = fopen($file, 'r');
		$this->separator = $separator;
	}

	public function process($cb) {
		$this->cb = $cb;
		$this->keys = fgetcsv($this->handle, 0, $this->separator);
		while($line = $this->getLine()) {
			foreach($this->keys as $k=>$v) {
				if(!isset($line[$k]))
					$line[$k] = '';
			}
			$line = array_combine($this->keys, $line);
			call_user_func_array($cb, array($line));
		}
	    fclose($this->handle);
	}

	protected function getLine() {
	    return fgetcsv($this->handle, 0, $this->separator);
	}
}

// Actualite::destroyAll();

$file = '../kritsen/import/files/basexls_produit_visuel/produit3.csv';
$importer = new CSVImporter($file, ',');
$importer->process(function($line) {
	#process line
	// ...
	
	$actualite = new Actualite($line);
	$actualite->save();
});