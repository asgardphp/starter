<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<?php $controller->getContainer()['html']->printTitle() ?>
	<?php $controller->getContainer()['html']->printDescription() ?>
	<?php $controller->getContainer()['html']->printKeywords() ?>
	<base href="<?=$controller->request->url->base() ?>" />
	<?php $controller->getContainer()['html']->printJSInclude() ?>
	<?php $controller->getContainer()['html']->printCSSInclude() ?>
	<?php $controller->getContainer()['html']->printJSCode() ?>
	<?php $controller->getContainer()['html']->printCSSCode() ?>
	<?php $controller->getContainer()['html']->printCode() ?>
</head>
<body>
	<?=$content ?>
</body>
</html>