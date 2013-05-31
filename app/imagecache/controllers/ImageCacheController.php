<?php
namespace Coxis\App\Imagecache\Controllers;

/**
@Prefix('imagecache')
*/
class ImageCacheController extends \Coxis\Core\Controller {
	private function apply($img, $preset) {
		try {
			$preset = ImageCache::getPreset($preset);
		} catch(\Exception $e) {
			return \Response::setCode(404);
		}
		foreach($preset as $op=>$params)
			switch($op) {
				case 'resize':
					$img->resize($params, (isset($params['force']) && $params['force']));
					break;
				case 'crop':
					$img->crop($params);
					break;
			}
		return $img;
	}

	/**
	@Route(value = ':preset/:src', requirements = {
		src = {
			type = 'regex',
			regex = '.+'
		}	
	})
	*/
	public function imgAction($request) {
		if(\Config::get('imagecache')) {
			$file = _WEB_DIR_.'/cache/imagecache/'.$request['preset'].'/'.$request['src'];
			if(file_exists($file)) {
				$img = ImageManager::load($file);
				$img->output();
			}
			else {
				$img = ImageManager::load(_WEB_DIR_.'/'.$request['src']);
				$this->apply($img, $request['preset'])->save($file);
				$img->output();
			}
		}
		else {
			try {
				$img = ImageManager::load(_WEB_DIR_.'/'.$request['src']);
			} catch(\Exception $e) {
				return \Response::setCode(500);
			}
			$this->apply($img, $request['preset']);
			$img->output();
		}
		
		\Response::setHeader('Content-Type', image_type_to_mime_type($img->type));
	}
	
	/**
	@Route()
	*/
	public function testAction() {
		$src = ImageCache::src('img/2.jpg', 'thumb');
		$content = $src.'<br/><img src="'.$src.'" alt=""/>';
		return \Response::setContent($content);
	}
}