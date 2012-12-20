<?php
namespace Coxis\Core;

class FrontController extends Controller {
	public function mainAction() {
		$response = require('core/getresponse.php');
		$response->send();
	}
}