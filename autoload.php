<?php
// $composer = require 'vendor/autoload.php';
$composer = require __DIR__.'/../autoload.php';
// $composer->addPsr4('Admin\\', __DIR__.'/modules/admin/app/Admin');
// $composer->addPsr4('News\\', __DIR__.'/modules/news/app/News');
// $composer->addPsr4('Captcha\\', __DIR__.'/modules/captcha/app/Captcha');
// $composer->addPsr4('Wysiwyg\\', __DIR__.'/modules/wysiwyg/app/Wysiwyg');

$composer->setUseIncludePath(true);
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/app');