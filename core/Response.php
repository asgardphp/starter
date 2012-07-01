<?php
class Response {
	//~ public static $die = true;
	//~ public static $isSent = false;
	private static $instance;
	private static $content;
	private static $code;
	private static $headers = array();
	private static $codes = array(
		200 => 'OK',
		201 => 'Created',
		204 => 'No Content',
		
		301 => 'Moved Permanently',
		
		400 => 'Bad Request',
		401 => 'Unauthorized',
		404 => 'Not Found',
		
		500 => 'Internal Server Error',
	);
	
	public static function init() {
		static::$content = null;
		static::$headers = array();
		static::$code = null;
	}
	
	public static function isSent() {
		return static::$isSent;
	}

	public static function setCode($code) { 
		static::$code = $code;
		return static::getInstance();
	} 

	public static function getCode() { 
		return static::$code;
	} 

	public static function setHeader($header, $value) {
		static::$headers[$header] = $value;
		return static::getInstance();
	}

	public static function getHeader($header) {
		return static::$headers[$header];
	}

	public static function setContent($content) {
		static::$content = $content;
		
		return static::getInstance();
	}

	public static function getContent() {
		return static::$content;
	}

	public static function sendHeaders($headers=null) {
		if(headers_sent())
			return;
			
		try {
			while(ob_end_clean()){}
		}
		catch(Exception $e) {}
	
		if(!$headers) {
			$headers = array();
			if(array_key_exists(static::$code, static::$codes))
				$headers[] = 'HTTP/1.1 '.static::$code.' '.static::$codes[static::$code];
			else
				$headers[] = 'HTTP/1.1 200 '.static::$codes[200];
			foreach(static::$headers as $k=>$v)
				$headers[] = $k.': '.$v;
		}
			
		foreach($headers as $h)
			header($h);
	}

	public static function send() {
		Controller::static_trigger('output_'.static::$code);
		Controller::static_trigger('output');
		
		$headers = array();
		if(array_key_exists(static::$code, static::$codes))
			$headers[] = 'HTTP/1.1 '.static::$code.' '.static::$codes[static::$code];
		else
			$headers[] = 'HTTP/1.1 200 '.static::$codes[200];
		foreach(static::$headers as $k=>$v)
			$headers[] = $k.': '.$v;
		
		//~ d(static::$content);
		//~ d();
		throw new EndException(new Result($headers, static::$content));
		//~ throw new Exception();
	}
	
	public static function redirect($url='', $relative=true) {
		if($relative)
			static::$headers['Location'] = URL::to($url);
		else
			static::$headers['Location'] = $url;
			
		return static::getInstance();
	}

	public static function getInstance() { 
		if(!static::$instance)
			static::$instance = new self();

		return static::$instance;
	}
}