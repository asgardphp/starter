<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Coxis</title>
	<?php \Coxis\Core\Tools\HTML::show_title() ?>
	<?php \Coxis\Core\Tools\HTML::show_description() ?>
	<?php \Coxis\Core\Tools\HTML::show_keywords() ?>
	<base href="<?php echo \URL::base() ?>" />
	<?php \Coxis\Core\Tools\HTML::show_all() ?>

</head>
<body>
	<h1>Coxis</h1>
	<div>
		<?php echo $content ?>
	</div>
	<p>By Michel Hognerud - 2012</p>
</body>
</html>