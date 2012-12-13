<?php
namespace Coxis\Core\Cli;

#todo replace with a distributed architecture

class CoxisController extends CLIController {
	public function versionAction($request) {
		echo 'Version: alpha';
	}

	public function getAction($request) {
		$browser = new Browser;
		echo $browser->get($request[0]);
	}

	public function publishAction($request) {
		$bundle = $request[0];
		static::publishBundle($bundle);
	}

	public static function publishBundle($bundle_path) {
		$bundle = basename($bundle_path);
		echo 'Publishing assets of bundle '.$bundle.'..'."\n";
		#todo find path to bundle
		// $bundle_path = 'bundles\\'.$bundle.'\\';
		// d($bundle);

		if(file_exists(_DIR_.$bundle_path.'/web') && is_dir($bundle_path.'/web'))
			static::copyDir($bundle_path.'/web', 'web/bundles/'.$bundle);
	}

	public function consoleAction($request) {
		echo 'Starting console mode..'."\n";
		echo 'Type quit to quit.'."\n";
		$fh = fopen('php://stdin', 'r');
		echo '>';
		$cmd = fgets($fh, 1024);
		while($cmd != "quit\r\n") {
			$res = null;
			if(!preg_match('/;$/', $cmd))
				$cmd .= ';';

			$worked = @eval('$res = '.$cmd);
			if($worked !== null) {
				$worked = @eval($cmd);
				if($worked !== null)
					echo 'Invalid command'."\n";
			}
			echo "\n".'Result: '.var_export($res);
			echo "\n";
			echo '>';
			$cmd = fgets($fh, 1024);
		}
		echo 'Quit..'."\n";
	}

	public function installAllAction($request) {
		echo 'Installing all bundles..'."\n";
		foreach(\BundlesManager::getBundles() as $bundle)
			static::installBundle($bundle);
	}

	public function installAction($request) {
		$bundle = $request[0];

		echo 'Installing '.$bundle.'..'."\n";

		$bundle_path = 'bundles\\'.$bundle.'\\';

		if(!(file_exists(_DIR_.$bundle_path) && is_dir($bundle_path))) {
			#copy bundle files
			#todo replace with distributed solution
			if(file_exists('C:\Users\leyou\Documents\projects\coxisgenerator\bundles\\'.$bundle))
				static::copyDir('C:\Users\leyou\Documents\projects\coxisgenerator\bundles\\'.$bundle, 'bundles\\'.$bundle);
			else
				die('Bundle '.$bundle.' does not exist.');
		}

		static::installBundle($bundle);
	}

	public static function installBundle($bundle_path) {
		// $bundle_path = 'bundles\\'.$bundle.'\\';

		\BundlesManager::loadModelFixtures($bundle_path);

		static::publishBundle($bundle_path);

		if(file_exists(_DIR_.$bundle_path.'install.php'))
			include($bundle_path.'install.php');
	}
	
	public function backupfilesAction($request) {
		if(isset($request[0]))
			$output = $request[0];
		else
			$output = 'backup/files/'.time().'.zip';
		echo 'Dumping files into '.$output;
		
		$path = 'web/upload';
		FileManager::mkdir(dirname($output));
		Tools::zip($path, $output);
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
	
		$yaml = new sfYamlParser();
		$raw = $yaml->parse(file_get_contents($input));
		$bundles = array();
		foreach($bundles as $bundle) {
			if(file_exists(_DIR_.'app/'.$bundle.'/')) {
				static::promptConfirmation('Some bundles already exist. Are you sure you want to continue?');
				break;
			}
		}
		
		foreach($raw as $bundle_name=>$raw_bundle) {
			if(file_exists(_DIR_.'app/'.$bundle_name.'/'))
				static::rrmdir('app/'.$bundle_name.'/');
			
			$bundle = $raw_bundle;
			$bundle['name'] = strtolower($bundle_name);
			
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
			if(!isset($bundle['model']['relations']))
				$bundle['model']['relations'] = array();
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
			
			static::copyDir('core/cli/base_bundle/', 'app/'.strtolower($bundle['name']).'/');
			static::processDir('app/'.$bundle['name'], $bundle, $bundle_filenames);
		
			\Coxis\Core\Context::get('autoloader')->preloadDir('app/'.$bundle['name'].'/models');
		}
		
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
					array_keys($modelName::properties()),
					array_keys($modelName::$relations)
				);
			}
			
			static::copyDir('core/cli/base_bundle2/', 'app/'.strtolower($bundle['name']).'/');
			static::processDir('app/'.$bundle['name'], $bundle, $bundle_filenames);
		}
			
		echo "\n\n".'Bundles created: '.implode(', ', array_keys($bundles));
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
		if(!file_exists(_DIR_.$dst))
			mkdir($dst, 0777, true);
	    while(false !== ($file = readdir($dir))) { 
			if ($file != '.' && $file != '..') { 
			    if(is_dir($src.'/'.$file))
					static::copyDir($src.'/'.$file,$dst.'/'.$file);
			    else
					copy($src.'/'.$file,$dst.'/'.$file);
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
			else 
				static::processDir($dir.'/'.$filename, $bundle, $bundle_filenames);
			$filename2 = str_replace('.pre', '', $filename);
			if(isset($bundle_filenames[$filename2])) 
				static::myrename($dir.'/'.$filename, $dir.'/'.$bundle_filenames[$filename2]);
			elseif($filename != $filename2)
				static::myrename($dir.'/'.$filename, $dir.'/'.$filename2);
		}
	}
	
	public static function processFile($file_path, $bundle) {
		ob_start();
		include($file_path);
		$content = ob_get_contents();
		ob_end_clean();

		$content = str_replace('<%', '<?php', $content);
		$content = str_replace('%>', '?>', $content);

		file_put_contents($file_path, $content);
	}
	
	public static function myrename($src, $dst) {
		if(file_exists(_DIR_.$dst)) {
			static::copyDir($src, $dst);
			static::rrmdir($src);
		}
		else
			rename($src, $dst);
	}

	public function testAllAction($request) {
		$this->generateTestSuiteAction(array('autotest.xml'));
		$output = null;
		exec('phpunit --configuration autotest.xml', $output);
		foreach($output as $l)
			echo $l."\n";
	}

	public function generateTestSuiteAction($request) {
		if(isset($request[0]))
			$output = $request[0];
		else
			$output = 'testsuite.xml';

		$content = '<phpunit>
  <testsuites>
    <testsuite name="Coxis">
      <directory>tests</directory>';

    	foreach(BundlesManager::getBundles() as $bundle_dir) {
    		if(file_exists($bundle_dir.'/tests') && is_dir($bundle_dir.'/tests'))
    			$content .= "\n".'      <directory>'.$bundle_dir.'/tests</directory>';
    	}

    	$content .= "\n".'    </testsuite>
  </testsuites>
</phpunit>';

		FileManager::put($output, $content);
	}

	public function generateTestsAction($request) {
		if(isset($request['dir']))
			$directory = $request['dir'];
		else
			$directory = 'app';

		if(isset($request['file']))
			$res = $request['file'];
		else
			$res = 'tests/AutoTest.php';

		copy('sample.php', $res);

		$routes = \Router::getRoutes();
		$routes = \BundlesManager::getRoutesFromDirectory($directory);
		usort($routes, function($a, $b) {
			return $a['route'] > $b['route'];
		});

		foreach($routes as $route=>$params) {
			$method = strtolower($params['method']);
			if(!$method)
				$method = 'get';

			#get
			if($method === 'get' || $method === 'delete') {
				if(strpos($params['route'], ':') !== false) {
					// continue;
					#get params
					file_put_contents($res, '
				/*
				$browser = new Browser;
				$this->assertEquals(200, $browser->'.$method.'(\''.$params['route'].'\')->getCode(), \''.strtoupper($method).' '.$params['route'].'\');
				*/
				', FILE_APPEND);
				}
				else {
				file_put_contents($res, '
				$browser = new Browser;
				$this->assertEquals(200, $browser->'.$method.'(\''.$params['route'].'\')->getCode(), \''.strtoupper($method).' '.$params['route'].'\');
				', FILE_APPEND);
				}
			}
			else {
				// continue;
				#post params
				file_put_contents($res, '
				/*
				$browser = new Browser;
				$this->assertEquals(200, $browser->'.$method.'(\''.strtoupper($method).' '.$params['route'].'\',
					array(),
					array(),
				)->getCode(), \''.$params['route'].'\');
				*/
				', FILE_APPEND);
			}
		}

		file_put_contents($res, '
			}
		}
		', FILE_APPEND);
	}
}