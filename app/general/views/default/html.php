<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> -->
	<?php \Coxis\Core\App::get('html')->printTitle() ?>
	<?php \Coxis\Core\App::get('html')->printDescription() ?>
	<?php \Coxis\Core\App::get('html')->printKeywords() ?>
	<base href="<?php echo \Coxis\Core\App::get('url')->base() ?>" />
	<?php \Coxis\Core\App::get('html')->printJSInclude() ?>
	<?php \Coxis\Core\App::get('html')->printCSSInclude() ?>
	<?php \Coxis\Core\App::get('html')->printJSCode() ?>
	<?php \Coxis\Core\App::get('html')->printCSSCode() ?>
	<?php \Coxis\Core\App::get('html')->printCode() ?>
</head>
<body>
	<?php echo $content ?>
</body>
</html>