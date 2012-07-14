<?php
class FrontController extends Controller {
	public static $loaded = false;

	public function mainAction() {
		Response::init();
		
		/* WEB RESOURCES */
		$this->getResource();
		
		/* BUNDLES */
		if(!function_exists('url_for')) {
			function url_for($what, $params=array(), $relative=true) {
				return URL::url_for($what, $params, $relative);
			}
		}

		if(!static::$loaded)
			$this->load();
		
		Router::parseRoutes(BundlesManager::$routes);

		$this->trigger('start');
			
		//Dispatch to target controller
		$output = Router::dispatch($this);
		
		$output = $this->filter('output', $output);

		//Send the response
		Response::setContent($output)->send();
	}
	
	public function load() {
		BundlesManager::loadBundles();
		//~ d(BundlesManager::$bundles_routes);
		//~ d(Model::$_properties);
	
		//User Session
		User::start();
		
		static::$loaded = true;
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
			//~ $mime = 'application/octet-stream';
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
					
					if(file_exists('bundles/'.$bundle.'/web/'.$path) && is_file('bundles/'.$bundle.'/web/'.$path))
						$this->send_file('bundles/'.$bundle.'/web/'.$path);
					elseif($dirs['2'] == 'web' && file_exists('bundles/'.$bundle.'/'.$path) && is_file('bundles/'.$bundle.'/'.$path))
						$this->send_file('bundles/'.$bundle.'/'.$path);
				}
			}
		}
	}
}