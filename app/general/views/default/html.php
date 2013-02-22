<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php \HTML::show_title() ?>
	<?php \HTML::show_description() ?>
	<?php \HTML::show_keywords() ?>
	<base href="<?php echo \URL::base() ?>" />
	<?php \HTML::minify_js() ?>
	<?php \HTML::minify_css() ?>
	<?php \HTML::show_code_js() ?>
	<?php \HTML::show_code_css() ?>
	<?php \HTML::show_code() ?>
</head>
<body>
	<?php echo $content ?>
</body>
</html>