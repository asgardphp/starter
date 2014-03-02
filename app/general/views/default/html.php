<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> -->
	<?php \Coxis\Core\App::get('html')->show_title() ?>
	<?php \Coxis\Core\App::get('html')->show_description() ?>
	<?php \Coxis\Core\App::get('html')->show_keywords() ?>
	<base href="<?php echo \Coxis\Core\App::get('url')->base() ?>" />
	<?php \Coxis\Core\App::get('html')->show_include_js() ?>
	<?php \Coxis\Core\App::get('html')->show_include_css() ?>
	<?php \Coxis\Core\App::get('html')->show_code_js() ?>
	<?php \Coxis\Core\App::get('html')->show_code_css() ?>
	<?php \Coxis\Core\App::get('html')->show_code() ?>
</head>
<body>
	<?php echo $content ?>
</body>
</html>