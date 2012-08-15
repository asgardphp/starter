<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>ARPA</title>
	<?php HTML::show_title() ?>
	<?php HTML::show_description() ?>
	<?php HTML::show_keywords() ?>
	<base href="<?php echo URL::base() ?>" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	<?php HTML::show_all() ?>
	<!--[if IE 7]>
	<style>
	.post-list li {
		clear:both;
	}
	</style>
	<![endif]-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" ></script>
	<script src="js/script.js"></script>
	<script src="js/jquery.form.js"></script>
	<script>
	$(function() {
		function process() {
			$('#sidebar .paging a').click(function(e) {
				var j = $(e.currentTarget);
				$.get(j.attr('href'), function(data) {
					$('#actualites-holder').html(data);
					process();
				});
				return false;
			});
		}
		process();
	});
	</script>
	<link media="all" rel="stylesheet" type="text/css" href="css/all.css" />
</head>
<body>
	<div id="wrapper">
		<div class="w1">
			<div id="header">
				<div class="header-block">
					<strong class="logo"><a href="">ARPA mini-pyrenees</a></strong>
					<span class="slogan">LES PRATIQUES VOCALES OU<br/> LE PARTAGE DES EMOTIONS</span>
				</div>
				<ul id="nav">
					<li><a href="" class="home<?php echo Router::getController()=='default' ? ' actif':'' ?>">Home</a></li>
					<li><a href="formations"<?php echo Router::getController()=='formation' ? ' class="actif"':'' ?>>Formations</a></li>
					<li><a href="annuaire"<?php echo Router::getController()=='annuaire' ? ' class="actif"':'' ?>>Annuaire régional</a></li>
					<li><a href="annonces"<?php echo Router::getController()=='annonce' ? ' class="actif"':'' ?>>Petites annonces</a></li>
					<li><a href="actualites"<?php echo Router::getController()=='actualite' ? ' class="actif"':'' ?>>Actualités</a></li>
					<li><a href="arpa"<?php echo Router::getController()=='arpa' && Router::getAction()!='contact' ? ' class="actif"':'' ?>>L’ARPA</a></li>
					<li><a href="contact"<?php echo Router::getAction()=='contact' ? ' class="actif"':'' ?>>Contact</a></li>
				</ul>
			</div>
<?php echo $content ?>
			<div id="footer">
				<p>ARPA Midi- Pyrénéés Atelier Régional des Pratiques musicales Amateur - mentions légales - <strong>conception et création: <a href="http://glue-design.com">Glue Design</a></strong></p>
			</div>
		</div>
	</div>
</body>
</html>