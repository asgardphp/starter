<?php
class CkeditorController extends Controller {
	/**
	@Route('bundles/ckeditor/ckeditor/config.js')
	**/
	public function configAction() {
		Response::setHeader('Content-type', 'application/x-javascript');
	}
}