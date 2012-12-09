<?php
class SlideshowService {
	public static function show() {
		$slideshow = Slideshow::first();
		if(!$slideshow)
			$slideshow = new Slideshow;
		echo View::render(dirname(__FILE__).'/../views/slideshow.php', array('slideshow'=>$slideshow));
	}
}