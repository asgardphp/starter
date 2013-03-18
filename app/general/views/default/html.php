<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> -->
	<?php \HTML::show_title() ?>
	<?php \HTML::show_description() ?>
	<?php \HTML::show_keywords() ?>
	<base href="<?php echo \URL::base() ?>" />
	<?php \HTML::show_include_js() ?>
	<?php \HTML::show_include_css() ?>
	<?php \HTML::show_code_js() ?>
	<?php \HTML::show_code_css() ?>
	<?php \HTML::show_code() ?>
</head>
<body>
	<?php echo $content ?>
</body>
</html>