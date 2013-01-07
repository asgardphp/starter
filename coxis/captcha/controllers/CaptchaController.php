<?php
class CaptchaController extends Controller {
	/**
	@Route('captcha')
	*/
	public function captchaAction($request) {
		$width = isset($_GET['width']) ? $_GET['width'] : '151';
		$height = isset($_GET['height']) ? $_GET['height'] : '61';
		$characters = isset($_GET['characters']) && $_GET['characters'] > 1 ? $_GET['characters'] : '6';
		 
		$captcha = new Captcha($width,$height,$characters);
		$image = $captcha->image();
		imagejpeg($image);
		imagedestroy($image);
		\Response::setHeader('Content-Type', 'image/jpeg');
	}
}