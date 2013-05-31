<?php
namespace Coxis\App\Sitemapxml\Controllers;

class SitemapController extends \Coxis\Core\Controller {
	/**
	@Route('sitemap.xml')
	*/
	public function indexAction() {
		\Response::setHeader('Content-Type', 'text/xml');
	}
}