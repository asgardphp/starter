<?php
namespace Coxis\Utils;

/*$csv = new CSV($header);
$csv->header($header);
$csv->add($data);
$csv->get();
$csv->output();
$csv->separator(';');

$csv = new CSV($model::propertyNames());
foreach($models as $model)
	$csv->add($model->toArray());
$csv->get();
*/

class CSV {
	protected $header = array();
	protected $data = array();
	protected $separator = ';';

	function __construct($header = array()) {
		$this->header($header);
	}

	public function header($header) {
		$this->header = $header;
		return $this;
	}

	public function add($data) {
		$this->data[] = $data;
		return $this;
	}

	public function separator($sep) {
		$this->separator = $sep;
		return $this;
	}

	function toCSV($data) {
		$outstream = fopen("php://temp", 'r+');
		fputcsv($outstream, $data, $this->separator, '"');
		rewind($outstream);
		$csv = fgets($outstream);
		fclose($outstream);
		return $csv;
	}

	public function get() {
		$csv = '';

		if($this->header) {
			$header = $this->header;
			$data = $this->data;	
		}
		else {
			$header = $this->data[0];
			$data = array_slice($this->data, 1);
		}
		foreach($data as $k=>$row) {
			ksort($row, function($a, $b) use ($header) {
				return array_search($a, array_keys($header)) < array_search($b, array_keys($header));
			});
			$data[$k] = $row;
		}

		$data = array_merge(array($header), $data);

		foreach($data as $row)
			$csv .= $this->toCSV($row);

		return $csv;
	}

	public function output() {
		echo $this->get();
		return $this;
	}
}