<?php
namespace Coxis\Core {

class FrontController extends Controller {
	public function mainAction() {
		if(!defined('_ENV_'))
			if(isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost'))
				define('_ENV_', 'dev');
			else
				define('_ENV_', 'prod');

		/* CONFIG */
		import('Coxis\Core\Config');
		\Coxis\Core\Config::loadConfigDir('config');
		if(\Coxis\Core\Config::get('error_display'))
			\Coxis\Core\Error::display(true);
	
		/* WEB RESOURCES */
		$this->getResource();
		
		Coxis::load();

		Event::trigger('start');
			
		//Dispatch to target controller
		$output = Router::dispatch($this);
		
		$output = Event::filter('output', $output);

		//Send the response
		Response::setContent($output)->send();
	}

	public function send_file($file) {
		$mimetypes = array(
			'gif' => 'image/gif',
			'png' => 'image/png',
			'jpg' => 'image/jpeg',
			'css' => 'text/css',
			'js' => 'application/javascript',
		);
		$path_parts = pathinfo($file);
		$ext = $path_parts['extension'];
		if (array_key_exists($ext, $mimetypes))
			$mime = $mimetypes[$ext];
		elseif($ext == 'php') {
			chdir(dirname($file));
			include(basename($file));
			exit();
		}
		else {
			$finfo = new finfo(FILEINFO_MIME);
			$mime = $finfo->file(getcwd().'/'.$file);
		}
		
		header("Content-type: ".$mime);
		header("Content-Length: ".filesize($file));
		header('Last-Modified:'.gmdate('D, d M Y H:i:s \G\M\T', filemtime($file)));
		if($ext == 'js' || $ext == 'css')
			include($file);
		else
			echo file_get_contents($file);
			
		exit();
	}

	#todo remove and replace with "coxis publish"
	public function getResource() {
		#WEB RESOURCES
		if(isset($_SERVER['REDIRECT_URL']) || isset($_SERVER['PATH_INFO'])) {
			if(isset($_SERVER['PATH_INFO']))
				$file = $_SERVER['PATH_INFO'];//todo use another var to get file path directly ?
			else
				$file = $_SERVER['REDIRECT_URL'];
		
			list($file) = explode('&', $file);
			if(file_exists(_WEB_DIR_.$file) && is_file(_WEB_DIR_.$file))
				$this->send_file(_WEB_DIR_.$file);
			else {
				$dirs = explode('/', trim($file, '/'));
				
				if($dirs[0] == 'bundles' && sizeof($dirs)>=3) {
					$bundle = $dirs[1];
					$path = implode('/', array_slice($dirs, 2));
					
					foreach(BundlesManager::$directories as $dir)
						if(file_exists($dir.'/'.$bundle.'/web/'.$path) && is_file($dir.'/'.$bundle.'/web/'.$path)) {
							$this->send_file($dir.'/'.$bundle.'/web/'.$path);
							return;
						}
					foreach(BundlesManager::$directories as $dir)
						if($dirs['2'] == 'web' && file_exists($dir.'/'.$bundle.'/'.$path) && is_file($dir.'/'.$bundle.'/'.$path)) {
							$this->send_file($dir.'/'.$bundle.'/'.$path);
							return;
						}
				}
			}
		}
	}
}
}