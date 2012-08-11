<?php
namespace Coxis\Core\Cli;

#todo replace with a distributed architecture
define('_GENERATOR_DIR_', 'C:\Users\leyou\Documents\projects\coxisgenerator');

class CoxisController extends CLIController {
	public function testAction($request) {
		//~ d($request);
		
		echo 'here';
	}
	
	public function setAction($request) {
		die('TODO');
		$config = file_get_contents(_PROJECT_DIR_.'/config.php');
		$config = preg_replace("/('$key'\s*=>\s*)'.*?'/", "$1'$value'", $config);
		file_put_contents(_PROJECT_DIR_.'/config.php', $config);
	}
	
	public function importAction($request) {
		die('TODO');
	}
	
	public function buildAction($request) {
		$input = $request[0];
	
		//~ include('vendors/yaml/sfYamlParser.php');
		$yaml = new sfYamlParser();
		$raw = $yaml->parse(file_get_contents($input));
		$bundles = array();
		foreach($bundles as $bundle) {
			if(file_exists('app/'.$bundle.'/')) {
				static::promptConfirmation('Some bundles already exist. Are you sure you want to continue?');
				break;
			}
		}
		
		foreach($raw as $bundle_name=>$raw_bundle) {
			if(file_exists('app/'.$bundle_name.'/'))
				static::rrmdir('app/'.$bundle_name.'/');
			
			$bundle = $raw_bundle;
			$bundle['name'] = $bundle_name;
			
			if(!isset($bundle['model']['meta']))
				$bundle['model']['meta'] = array();
			if(!isset($bundle['model']['meta']['name']))
				$bundle['model']['meta']['name'] = $bundle['name'];
			if(!isset($bundle['model']['meta']['plural']))
				$bundle['model']['meta']['plural'] = $bundle['model']['meta']['name'].'s';
			if(!isset($bundle['model']['meta']['label']))
				$bundle['model']['meta']['label'] = $bundle['model']['meta']['name'];
			if(!isset($bundle['model']['meta']['label_plural']))
				$bundle['model']['meta']['label_plural'] = $bundle['model']['meta']['label'].'s';
			if(!isset($bundle['model']['meta']['name_field'])) {
				$properties = array_keys($bundle['model']['properties']);
				$bundle['model']['meta']['name_field'] = $properties[0];
			}
			
			if(!isset($bundle['coxis_admin']['messages']['modified']))
				$bundle['coxis_admin']['messages']['modified'] = ucfirst($bundle['model']['meta']['label']).' modified with success.';
			if(!isset($bundle['coxis_admin']['messages']['created']))
				$bundle['coxis_admin']['messages']['created'] = ucfirst($bundle['model']['meta']['label']).' created with success.';
			if(!isset($bundle['coxis_admin']['messages']['many_deleted']))
				$bundle['coxis_admin']['messages']['many_deleted'] = ucfirst($bundle['model']['meta']['label_plural']).' modified with success.';
			if(!isset($bundle['coxis_admin']['messages']['deleted']))
				$bundle['coxis_admin']['messages']['deleted'] = ucfirst($bundle['model']['meta']['label']).' deleted with success.';
			if(!isset($bundle['coxis_admin']['messages']['unexisting']))
				$bundle['coxis_admin']['messages']['unexisting'] = 'This '.$bundle['model']['meta']['label'].' does not exist.';
				
			if(!isset($bundle['model']['properties']))
				$bundle['model']['properties'] = array();
			if(!isset($bundle['model']['relationships']))
				$bundle['model']['relationships'] = array();
			if(!isset($bundle['model']['files']))
				$bundle['model']['files'] = array();
			if(!isset($bundle['model']['behaviors']))
				$bundle['model']['behaviors'] = array();
				
			if(!isset($bundle['coxis_admin']))
				$bundle['coxis_admin'] = array();
			if(!isset($bundle['coxis_admin']['form']))
				$bundle['coxis_admin']['form'] = array();
			if(!isset($bundle['coxis_admin']['form']['fields']))
				$bundle['coxis_admin']['form']['fields'] = array();
				
			foreach($bundle['model']['properties'] as $k=>$v)
				if(!$v)
					$bundle['model']['properties'][$k] = array();
				
			foreach($bundle['model']['properties'] as $property => $params)
				if(!isset($bundle['coxis_admin']['form']['fields'][$property]))
					$bundle['coxis_admin']['form']['fields'][$property] = array();
			//~ if(is_array($bundle['coxis_admin']['form']['fields']))
				//~ foreach($bundle['coxis_admin']['form']['fields'] as $key=>$params) {
					//~ if(!isset($params['type']))
						//~ $bundle['coxis_admin']['form']['fields'][$key]['type'] = 'text';
				//~ }
			
			$bundles[$bundle_name] = $bundle;
		}
		
		foreach($bundles as $bundle) {
			$bundle_filenames = array(
				'_ModelAdminController.php' =>	ucfirst($bundle['model']['meta']['name']).'AdminController.php',
				'_ModelController.php' =>	ucfirst($bundle['model']['meta']['name']).'Controller.php',
				'_Model.php' =>	ucfirst($bundle['model']['meta']['name']).'.php',
				'_model' =>	$bundle['model']['meta']['name'],
				'_modeladmin' =>	$bundle['model']['meta']['name'].'admin',
			);
			
			static::copyDir(_GENERATOR_DIR_.'/base_bundle/', 'app/'.$bundle['name'].'/');
			static::processDir('app/'.$bundle['name'], $bundle, $bundle_filenames);
		}
		
		\Coxis\Core\Autoloader::preloadDir('app/'.$bundle['name'].'/models');
		
		foreach($bundles as $bundle) {		
			$bundle_filenames = array(
				'_ModelAdminController.php' =>	ucfirst($bundle['model']['meta']['name']).'AdminController.php',
				'_ModelController.php' =>	ucfirst($bundle['model']['meta']['name']).'Controller.php',
				'_Model.php' =>	ucfirst($bundle['model']['meta']['name']).'.php',
				'_model' =>	$bundle['model']['meta']['name'],
				'_modeladmin' =>	$bundle['model']['meta']['name'].'admin',
			);
			
			$modelName = $bundle['model']['meta']['name'];
			
			if(!isset($bundle['coxis_admin']['form']['display'])) {
				$bundle['coxis_admin']['form']['display'] = array_merge(
					array_keys($modelName::getProperties()),
					array_keys($modelName::$relationships),
					array_keys($modelName::$files)
				);
			}
			
			static::copyDir(_GENERATOR_DIR_.'/base_bundle2/', 'app/'.$bundle['name'].'/');
			static::processDir('app/'.$bundle['name'], $bundle, $bundle_filenames);
		}
			
		echo "\n\n".'Bundles created: '.implode(', ', array_keys($bundles));
			
		//~ build_db();
	}
 
	public static function promptConfirmation($msg) {
		if(defined('_CONFIRMATION_') && _CONFIRMATION_)
			return;
		echo $msg.' (y/n)';
		$reply = strtolower(getinput());
		if($reply!='y')
			die('OK!');
	 }
 
	public static function rrmdir($dir) { 
		if (is_dir($dir)) { 
			$objects = scandir($dir); 
			foreach ($objects as $object) { 
				if ($object != "." && $object != "..") { 
					if (filetype($dir."/".$object) == "dir")
						static::rrmdir($dir."/".$object);
					else
						unlink($dir."/".$object); 
				} 
			} 
			reset($objects);
			rmdir($dir); 
		} 
	} 

	public static function copyDir($src,$dst) { 
	    $dir = opendir($src); 
		if(!file_exists($dst))
			mkdir($dst, 0777, true);
	    while(false !== ( $file = readdir($dir)) ) { 
		if (( $file != '.' ) && ( $file != '..' )) { 
		    if ( is_dir($src . '/' . $file) ) { 
			static::copyDir($src . '/' . $file,$dst . '/' . $file); 
		    } 
		    else { 
			copy($src . '/' . $file,$dst . '/' . $file); 
		    } 
		} 
	    } 
	    closedir($dir); 
	} 
 
	public static function processDir($dir, $bundle, $bundle_filenames) {
		$dh = opendir($dir);
		while (false !== ($filename = readdir($dh))) {
			if($filename == '.' || $filename == '..')
				continue;
			if(is_file($dir.'/'.$filename)) {
				if(strpos($filename, '.pre')!==false)
					static::processFile($dir.'/'.$filename, $bundle, $bundle_filenames);
			}
			else {
			//~ die($filename);
				static::processDir($dir.'/'.$filename, $bundle, $bundle_filenames);
			}
			//~ echo $filename."\n";
			$filename2 = str_replace('.pre', '', $filename);
			if(isset($bundle_filenames[$filename2])) {
				//~ echo $bundle_filenames[$filename];
				static::myrename($dir.'/'.$filename, $dir.'/'.$bundle_filenames[$filename2]);
			}
			elseif($filename != $filename2) {
				static::myrename($dir.'/'.$filename, $dir.'/'.$filename2);
			}
		}
	}
	
	public static function processFile($file_path, $bundle) {
	//~ var_dump($bundle['model']['files']);die();
	//~ echo strpos($file_path, '_Model.php')."\n";
		//~ echo '--'."\n";
		ob_start();
		include($file_path);
		$content = ob_get_contents();
		ob_end_clean();
		//~ echo '----'."\n";
	//~ echo $file_path."\n";
		//~ if(strpos($file_path, '_Model.php') > 0)
			//~ die('-'.$content);
		//~ die($file_path);
		//~ if(strpos($file_path, 'Actualite.php'))
			//~ die($content);
		$content = str_replace('<%', '<?php', $content);
		$content = str_replace('%>', '?>', $content);
		//~ $fp = fopen($file_path, 'w+');
		//~ fwrite($fp, $content);
		//~ fclose($fp);
		file_put_contents($file_path, $content);
	}
	
	public static function myrename($src, $dst) {
		if(file_exists($dst)) {
			static::copyDir($src, $dst);
			static::rrmdir($src);
		}
		else
			rename($src, $dst);
	}
}