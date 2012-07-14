<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo Config::get('website', 'name') ?> &#9679; Login</title>
	<base href="<?php echo URL::to('admin/') ?>" />
    <style type="text/css" media="all">
		@import url("<?php echo URL::to('bundles/admin/css/admin.css') ?>");
    </style>
	<!--[if lt IE 8]><style type="text/css" media="all">@import url("<?php echo URL::to('css/ie.css') ?>");</style><![endif]-->
</head>
<body>
	<div id="hld">
		<div class="wrapper">		<!-- wrapper begins -->
			<div class="block small center login">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2>Login</h2>
					<ul>
						<li><a href="<?php echo URL::to('') ?>">retour au site</a></li>
					</ul>
				</div>		<!-- .block_head ends -->
				<div class="block_content">
					<?php Messenger::getInstance()->showAll() ?>
					<form action="login" method="post">
						<p>
							<label>Utilisateur :</label> <br />
							<input type="text" class="text" name="username"/>
						</p>
						<p>
							<label>Mot de passe :</label> <br />
							<input type="password" class="text" name="password"/> (<a href="forgotten">Mot de passe oublié ?</a>)
						</p>
						<p>
							<input type="submit" class="submit" value="Login" /> <span><label for="remember">Se souvenir de moi</label> <input type="checkbox" id="remember" name="remember" value="yes"/></span>
						</p>
					</form>
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		<!-- .login ends -->
		</div>						<!-- wrapper ends -->
	</div>		<!-- #hld ends -->
	
</body>
</html>