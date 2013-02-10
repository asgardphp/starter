<?php
/**
@Prefix('actualites')
*/
class ActualiteController extends Controller {
	/**
	@Route('rss')
	*/
	public function rssAction($request) {
		$actualites = Actualite::all();
		
		Response::setHeader('Content-Type', 'application/xml;charset=utf-8');

		$rss = new RSS('utf-8');
		
		$rss->channel(Value::val('name').' RSS', \URL::to(''), Value::val('name'));
		$rss->language('fr-FR');
		$rss->webMaster(Value::val('email'));

		$rss->startRSS();

		foreach($actualites as $actualite) {
			$rss->itemTitle($actualite->title);
			$rss->itemLink(\URL::to('news/'.$actualite->slug.'/'.$actualite->slug));
			$rss->itemPubDate($actualite->created_at->format('r'));

			$rss->addItem();
		}
		
		return $rss->RSSdone();
	}
}