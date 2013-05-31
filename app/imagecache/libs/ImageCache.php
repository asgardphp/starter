<?php
namespace Coxis\App\Imagecache\Libs;

class ImageCache {
	private static $presets = array();

	public static function getPreset($presetName) {
		if(!isset(static::$presets[$presetName]))
			throw new \Exception('Preset '.$presetName.' does not exist.');
		return static::$presets[$presetName];
	}

	public static function addPreset($presetName, $params) {
		static::$presets[$presetName] = $params;
	}
	
	public static function src($src, $preset) {
		if(is_array($preset)) {
			if(!($presetName = array_search($preset, static::$presets))) {
				$presetName = Tools::randstr();
				$icbundle = file_get_contents('bundles/imagecache/bundle.php');
				$arr = var_export($preset, true);
				$arr = explode("\n", $arr);
				$arr = str_replace('  ', "\t", $arr);
				$arr = implode("\n\t\t", $arr);
				$preset_arr = "		ImageCache::addPreset('".$presetName."', 
				".$arr."
			);";
				$icbundle .= "\n\n".$preset_arr;
				file_put_contents('bundles/imagecache/bundle.php', $icbundle);
			}
		}
		else
			$presetName = $preset;
	
		return 'imagecache/'.$presetName.'/'.trim($src, '/');
	}
	
	public static function clearFile($file) {
		if(!file_exists('web/cache/imagecache/'))
			return;
		if ($handle = opendir('web/cache/imagecache/')) {
			while (false !== ($entry = readdir($handle)))
				if ($entry != "." && $entry != ".." && is_dir('web/cache/imagecache/'.$entry))
					if(file_exists('web/cache/imagecache/'.$entry.'/'.$file)) 
						unlink('web/cache/imagecache/'.$entry.'/'.$file);
			closedir($handle);
		}
	}
	
	public static function clearPreset() {
		//todo
	}
}