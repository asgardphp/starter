<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=7" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo Value::val('name') ?> &#9679; Administration</title>
	<base href="<?php echo URL::to('admin/') ?>" />
	<style type="text/css" media="all">
		@import url("../bundles/admin/css/admin.css");
		@import url("../bundles/admin/css/jquery.wysiwyg.css");
		@import url("../bundles/admin/css/facebox.css");
		@import url("../bundles/admin/css/visualize.css");
		@import url("../bundles/admin/css/date_input.css");
	</style>
	<!--[if lt IE 8]><style type="text/css" media="all">@import url("../bundles/admin/css/ie.css");</style><![endif]-->
	<script type="text/javascript" src="../js/jquery.js"></script>
</head>
<body>
	<div id="hld">
		<div class="wrapper">	
			<div id="header">
				<div class="hdrl"></div>
				<div class="hdrr"></div>
				<h1><a href=".."><?php echo Value::val('name') ?></a></h1>
				
				
				<ul id="nav">
					<li><a href="#">Tableau de bord</a></li>
					<?php
					function showMenu($menu) {
						foreach($menu as $item) {
							if(is_array($item))
							?>
							<li><a href="<?php echo $item['link'] ?>"><?php echo $item['label'] ?></a>
							<?php
							if(isset($item['childs']) && $item['childs']) {
								echo '<ul>';
								showMenu($item['childs']);
								echo '</ul>';
							}
							?>
							</li>
							<?php
						}
					}
					showMenu(AdminMenu::$menu);
					?>
				</ul>
				<p class="user"><a href="..">Voir le site</a> | <a href="logout">Déconnexion</a></p>
			</div>	
			
			<?php echo $content; ?>
			
			<div id="footer">
				<p class="left"><?php echo Config::get('admin', 'footer') ?></p>
			</div>
		</div>			
	</div>	

	<!--[if IE]><script type="text/javascript" src="../bundles/admin/js/excanvas.js"></script><![endif]-->	
	<script type="text/javascript" src="../bundles/admin/js/jquery.img.preload.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/jquery.filestyle.mini.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/jquery.wysiwyg.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/jquery.date_input.pack.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/facebox.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/jquery.visualize.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/jquery.visualize.tooltip.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/jquery.select_skin.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/jquery.tablesorter.min.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/ajaxupload.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/jquery.pngfix.js"></script>
	<script type="text/javascript" src="../bundles/admin/js/custom.js"></script>
	
	<script type="text/javascript" src="../bundles/admin/uploadify/swfobject.js"></script>
	<script type="text/javascript" src="../bundles/admin/uploadify/jquery.uploadify.v2.1.4.min.js"></script>
	<script type="text/javascript">
	// <![CDATA[
	function multiple_upload(el, url) {
		elID = '#'+el;
	  $('#'+el+'-filesupload').uploadify({
		'uploader'  : '../bundles/admin/uploadify/uploadify.swf',
		'script'    : url,
		'cancelImg' : '../bundles/admin/uploadify/cancel.png',
		'auto'      : true,
		'multi'           : true,
		'scriptData'  : {'PHPSESSID':'<?php echo session_id() ?>'},
		'queueID'        : el+'-custom-queue',
		'simUploadLimit' : 3,
		'onSelectOnce'   : function(event,data) {
			$(elID).find('.uploadmsg').text(data.filesSelected + ' file(s) were added to the list.');
		},
		'onComplete' : function(event, ID, fileObj, response, data) {
			var result = JSON.parse(response);
			$('.imglist').append('<li>\
							<img src="../imagecache/admin_thumb/'+result.url+'" alt=""/>\
							<ul>\
								<li class="view"><a href="../'+result.url+'" rel="facebox">See</a></li>\
								<li class="delete"><a href="'+result.deleteurl+'">Delete</a></li>\
							</ul>\
						</li>');
			$('a[rel*=facebox]').facebox()
		},
		'onAllComplete'  : function(event,data) {
			$(elID).find('.uploadmsg').text(data.filesUploaded + ' fichiers uploadés, ' + data.errors + ' erreurs.');
		},
		'onError': function (event,ID,fileObj,errorObj) {
			console.log(errorObj.type + ' Error: ' + errorObj.info);
		},
	  });
	};
	// ]]>
	</script>
	<?php HTML::show_all() ?>
</body>
</html>