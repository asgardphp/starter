<?php
class CkeditorController extends Controller {
	/**
	@Route('bundles/_ckeditor/ckeditor/config.js')
	**/
	public function configAction() {
		Response::setHeader('Content-type', 'application/x-javascript');
	}
}