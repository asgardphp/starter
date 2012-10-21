<?php
namespace Coxis\Core\Tools;

class Locale {
	private $default = 'en';
	public $locales = array();

	function __construct() {
		static::setLocale(\Config::get('locale'));
	
		require_once('vendors/yaml/sfYamlParser.php');
		
		if(\Config::get('phpcache'))
			$this->locales = \Coxis\Core\Cache::get('locales');
		if(!$this->locales)
			$this->importLocales('locales');
		
		\Hook::hookOn('end', function() {
			if(\Config::get('phpcache'))
				\Coxis\Core\Cache::set('locales', Locale::$locales);
		});
	}

	public function setDefault($locale) {
		$this->default = $locale;
	}

	public static function setLocale($locale) {
		\Config::set('locale', $locale);
	}

	public function translate($key, $params=array()) {
		$locale = \Config::get('locale');
		if(isset($this->locales[$locale][$key]) && $this->locales[$locale][$key])
			$str = $this->locales[$locale][$key];
		elseif(isset($this->locales[$this->default][$key]) && $this->locales[$this->default][$key])
			$str = $this->locales[$this->default][$key];
		else
			$str = $key;
	
		foreach($params as $k=>$v)
			$str = str_replace(':'.$k, $v, $str);
		
		return $str;
	}
	
	public function importLocales($dir) {
		if(is_array(glob($dir.'/*')))
			foreach(glob($dir.'/*') as $lang_dir) {
				$lang = basename($lang_dir);
				foreach(glob($lang_dir.'/*') as $file)
					$this->import($lang, $file);
			}
	}
	
	public function import($lang, $file) {
		$yaml = new \sfYamlParser();
		$raw = $yaml->parse(file_get_contents($file));
		if(!isset($this->locales[$lang]))
			$this->locales[$lang] = array();
		$this->locales[$lang] = array_merge($this->locales[$lang], $raw);
	}
}