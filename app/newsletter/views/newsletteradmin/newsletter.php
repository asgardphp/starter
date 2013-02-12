<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Mailing</title>
	<style>
	</style>
</head>
<body style="background-color: #feccff;">
	<div style="margin:0 auto; width:600px; background-color:#f8f9f9; font-family:Arial;">
		<div id="header" style="
		background-image:url(<?php echo URL::to('img/newsletter/header-bg.png') ?>);
		background-repeat: repeat-x;
		height:127px;
		position:relative;
		padding:20px;
		color:#fff;
		font-size:38px;">
			<img alt="" src="<?php echo URL::to('img/newsletter/lili.png') ?>" style="float:right">
			Hello!
		</div>
		<div id="content" style="
		padding:0 20px 0 10px;">
			<?php echo $content ?>
			<br style="clear:both">
		</div>
		<div id="footer" style="
		background-color: #e091e0;
		height:100px;
		position: relative;">
			<div id="links" style="float:left;
		padding:50px 0 0 25px;">
				<a href="<?php echo URL::to('contact') ?>" style="color:#fff; text-decoration: none;">Contact</a> | <a href="<?php echo URL::to('newsletter/unsubscribe/'.$key) ?>" style="color:#fff; text-decoration: none;">Se désinscrire</a> | <a href="<?php echo URL::to('newsletter/archives/'.$id.'/'.$subscriber_id) ?>" style="color:#fff; text-decoration: none;">Version HTML</a>
			</div>
			<img alt="" src="<?php echo URL::to('img/newsletter/playbac.png') ?>" style="
		float:right; margin:20px;">
		</div>
	</div>
</body>
</html>