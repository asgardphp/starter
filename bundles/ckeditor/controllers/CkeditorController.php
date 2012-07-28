<?php
namespace Coxis\Bundles\Ckeditor\Controllers;

class CkeditorController extends \Coxis\Core\Controller {
	/**
	@Route('bundles/ckeditor/ckeditor/config.js')
	**/
	public function configAction() {
		Response::setHeader('Content-type', 'application/x-javascript');
	}
}