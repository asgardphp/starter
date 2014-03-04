<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> -->
	<?php \Asgard\Core\App::get('html')->printTitle() ?>
	<?php \Asgard\Core\App::get('html')->printDescription() ?>
	<?php \Asgard\Core\App::get('html')->printKeywords() ?>
	<base href="<?php echo \Asgard\Core\App::get('url')->base() ?>" />
	<?php \Asgard\Core\App::get('html')->printJSInclude() ?>
	<?php \Asgard\Core\App::get('html')->printCSSInclude() ?>
	<?php \Asgard\Core\App::get('html')->printJSCode() ?>
	<?php \Asgard\Core\App::get('html')->printCSSCode() ?>
	<?php \Asgard\Core\App::get('html')->printCode() ?>
</head>
<body>
	<?php echo $content ?>
</body>
</html>