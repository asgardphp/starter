<?php
namespace Coxis\Core;

class Locale {
	private static $default = 'en';
	private static $locale = 'en';
	public static $locales = array();

	public static function setDefault($locale) {
		static::$default = $locale;
	}

	public static function setLocale($locale) {
		static::$locale = $locale;
	}

	public static function _autoload() {
		static::setLocale(Config::get('locale'));
	
		include_once('vendors/yaml/sfYamlParser.php');
		
		if(\Coxis\Core\Config::get('phpcache'))
			static::$locales = \Coxis\Core\Cache::get('locales');
		if(!static::$locales) {
			Coxis::set('load_locales', true);
			static::importLocales('locales');
		}
		
		\Coxis\Core\Event::addHook('end', function() {
			if(\Coxis\Core\Config::get('phpcache'))
				\Coxis\Core\Cache::set('locales', Locale::$locales);
		});
	}

	public static function translate($key, $params=array()) {
		if(isset(static::$locales[static::$locale][$key]) && static::$locales[static::$locale][$key])
			$str = static::$locales[static::$locale][$key];
		elseif(isset(static::$locales[static::$default][$key]) && static::$locales[static::$default][$key])
			$str = static::$locales[static::$default][$key];
		else
			$str = $key;
	
		foreach($params as $k=>$v)
			$str = str_replace(':'.$k, $v, $str);
		
		return $str;
	}
	
	public static function importLocales($dir) {
		foreach(glob($dir.'/*') as $lang_dir) {
			$lang = basename($lang_dir);
			foreach(glob($lang_dir.'/*') as $file) {
				static::import($lang, $file);
			}
		}
	}
	
	public static function import($lang, $file) {
		$yaml = new \sfYamlParser();
		$raw = $yaml->parse(file_get_contents($file));
		if(!isset(static::$locales[$lang]))
			static::$locales[$lang] = array();
		static::$locales[$lang] = array_merge(static::$locales[$lang], $raw);
	}
}