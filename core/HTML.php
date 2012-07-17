<?php
namespace Coxis\Core;

class HTML {
	private static $include_js = array();
	private static $include_css = array();
	private static $code_js = array();
	public static $code_css = array();
	private static $code = array();
	
	private static $title = '';
	private static $description = '';
	private static $keywords = '';
	
	static public function getTitle() {
		return static::$title;
	}
	static public function getDescription() {
		return static::$description;
	}
	static public function getKeywords() {
		return static::$keywords;
	}
	
	static public function setTitle($title) {
		static::$title = $title;
	}
	
	static public function setDescription($description) {
		static::$description = $description;
	}
	
	static public function setKeywords($keywords) {
		static::$keywords = $keywords;
	}
	
	static public function show_title() {
		echo '<title>'.htmlentities(static::$title, ENT_QUOTES, "UTF-8").'</title>';
	}
	
	static public function show_description() {
		if(static::$description)
			echo '<meta name="description" content="'.str_replace('"', '\"', static::$description).'">';
	}
	
	static public function show_keywords() {
		if(static::$keywords)
			echo '<meta name="keywords" content="'.str_replace('"', '\"', static::$keywords).'">';
	}
	
	static public function include_js($js) {
		if(!in_array($js, static::$include_js))
			static::$include_js[] = $js;
	}
	
	static public function include_css($css) {
		if(!in_array($css, static::$include_css))
			static::$include_css[] = $css;
	}
	
	static public function code_js($js) {
		static::$code_js[] = $js;
	}
	
	static public function code_css($css) {
		static::$code_css[] = $css;
	}
	
	static public function code($code) {
		static::$code[] = $code;
	}
	
	static public function show_include_js() {
		foreach(static::$include_js as $js)
			echo '<script type="text/javascript" src="'.URL::to($js).'"></script>'."\n";
	}
	
	static public function show_include_css() {
		foreach(static::$include_css as $css)
			echo '<link rel="stylesheet" href="'.URL::to($css).'"/>'."\n";
	}
	
	static public function show_code_js() {
		if(sizeof(static::$code_js)>0) {
			echo '<script type="text/javascript">
			//<![CDATA[
			';
			foreach(static::$code_js as $code)
				echo $code."\n";
			echo '//]]>
			</script>';
		}
	}
	
	static public function show_code_css() {
		if(sizeof(static::$code_css)>0) {
			echo '<style type="text/css">';
			foreach(static::$code_css as $code)
				echo $code."\n";
			echo '</style>';
		}
	}
	
	static public function show_code() {
		foreach(static::$code as $code)
			echo $code."\n";
	}
	
	static public function show_all() {
		static::show_include_js();
		static::show_include_css();
		static::show_code_js();
		static::show_code_css();
		static::show_code();
	}
	
	static public function sanitize($html) {
		return htmlentities($html, ENT_NOQUOTES, 'UTF-8');
	}
}
?>