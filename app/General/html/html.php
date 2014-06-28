<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<?php $controller->getApp()['html']->printTitle() ?>
	<?php $controller->getApp()['html']->printDescription() ?>
	<?php $controller->getApp()['html']->printKeywords() ?>
	<base href="<?=$controller->request->url->base() ?>" />
	<?php $controller->getApp()['html']->printJSInclude() ?>
	<?php $controller->getApp()['html']->printCSSInclude() ?>
	<?php $controller->getApp()['html']->printJSCode() ?>
	<?php $controller->getApp()['html']->printCSSCode() ?>
	<?php $controller->getApp()['html']->printCode() ?>
</head>
<body>
	<?=$content ?>
</body>
</html>