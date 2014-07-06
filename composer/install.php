<?php
parent::install($repo, $package);

if($package->getType() !== 'asgard-bundle')
	return;

require_once dirname(__DIR__).'/autoload.php';

$path = $this->getInstallPath($package);

$kernelPath = dirname(__DIR__).'/app/Kernel.php';
$kernelCode = file_get_contents($kernelPath);

$class = '';
foreach(explode('/', $package->getPrettyName()) as $piece)
	$class .= '\\'.ucfirst($piece);
$class .= '\\Bundle';

if(file_exists($path.'\\Bundle.php') && $class = \Asgard\Common\Tools::loadClassFile($path.'\\Bundle.php'))
	$line = "new $class,";
else
	$line = "'$path',";

$kernelCode = preg_replace('/(#Composer Bundles.*?)/', $line."\n\t\t\t\t".'\1', $kernelCode);

file_put_contents($kernelPath, $kernelCode);