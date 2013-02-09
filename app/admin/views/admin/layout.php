<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=7" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo \App\Value\Models\Value::val('name') ?> &#9679; <?php echo __('Administration') ?></title>
	<base href="<?php echo \URL::to('admin/') ?>" />
	<style type="text/css" media="all">
		@import url("../admin/css/admin.css");
		@import url("../admin/css/jquery.wysiwyg.css");
		@import url("../admin/css/facebox.css");
		@import url("../admin/css/visualize.css");
		@import url("../admin/css/date_input.css");
	</style>
	<!--[if lt IE 8]><style type="text/css" media="all">@import url("../admin/css/ie.css");</style><![endif]-->
	<script type="text/javascript" src="../js/jquery.js"></script>
			<script src="http://code.jquery.com/ui/1.8.22/jquery-ui.min.js" type="text/javascript"></script>
			<link rel="stylesheet" href="http://code.jquery.com/ui/1.8.22/themes/base/jquery-ui.css" type="text/css" media="all" />
			<link rel="stylesheet" href="http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css" type="text/css" media="all" />
	<script>
	window.i18n = {
		'admin': {
			'are_you_sure': '<?php echo addcslashes(__('Etes vous sûr ?'), '\'') ?>',
		}
	};
	</script>
</head>
<body>
	<div id="hld">
		<div class="wrapper">	
			<div id="header">
				<div class="hdrl"></div>
				<div class="hdrr"></div>
				<h1><a href=".."><?php echo \App\Value\Models\Value::val('name') ?></a></h1>
				
				<!-- <img src="<?php echo URL::to('') ?>" style="height:80px; float:left; margin-left:-50px; margin-top:-10px;"> -->
				<ul id="nav">
					<li><a href="#"><?php echo __('Dashboard') ?></a></li>
					<?php
					if(!function_exists('showMenu')) {
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
					}
					showMenu(\App\Admin\Libs\AdminMenu::$menu);
					?>
				</ul>
				<p class="user"><a href=".."><?php echo __('See website') ?></a> | <a href="logout"><?php echo __('Disconnect') ?></a></p>
			</div>	
			
			<?php echo $content; ?>
			
			<div id="footer">
				<p class="left"><?php echo Config::get('admin', 'footer') ?></p>
			</div>
		</div>			
	</div>	

	<!--[if IE]><script type="text/javascript" src="../admin/js/excanvas.js"></script><![endif]-->	
	<script type="text/javascript" src="../admin/js/jquery.img.preload.js"></script>
	<script type="text/javascript" src="../admin/js/jquery.filestyle.mini.js"></script>
	<script type="text/javascript" src="../admin/js/jquery.wysiwyg.js"></script>
	<script type="text/javascript" src="../admin/js/jquery.date_input.pack.js"></script>
	<script type="text/javascript" src="../admin/js/facebox.js"></script>
	<script type="text/javascript" src="../admin/js/jquery.visualize.js"></script>
	<script type="text/javascript" src="../admin/js/jquery.visualize.tooltip.js"></script>
	<script type="text/javascript" src="../admin/js/jquery.select_skin.js"></script>
	<script type="text/javascript" src="../admin/js/jquery.tablesorter.min.js"></script>
	<script type="text/javascript" src="../admin/js/ajaxupload.js"></script>
	<script type="text/javascript" src="../admin/js/jquery.pngfix.js"></script>
	<script type="text/javascript" src="../admin/js/custom.js"></script>
	
	<script type="text/javascript" src="../admin/uploadify/swfobject.js"></script>
	<script type="text/javascript" src="../admin/uploadify/jquery.uploadify.v2.1.4.min.js"></script>
	<script type="text/javascript">
	// <![CDATA[
	function multiple_upload(el, url) {
		elID = '#'+el;
	  $('#'+el+'-filesupload').uploadify({
		'uploader'  : '../admin/uploadify/uploadify.swf',
		'script'    : url,
		'cancelImg' : '../admin/uploadify/cancel.png',
		'auto'      : true,
		'multi'           : true,
		'scriptData'  : {'PHPSESSID':'<?php echo session_id() ?>'},
		'queueID'        : el+'-custom-queue',
		'simUploadLimit' : 3,
		'onSelectOnce'   : function(event,data) {
			$(elID).find('.uploadmsg').text(data.filesSelected + ' <?php echo __('file(s) were added to the list.') ?>');
		},
		'onComplete' : function(event, ID, fileObj, response, data) {
			var result = JSON.parse(response);
			$('.imglist').append('<li>\
							<img src="<?php echo \URL::to('imagecache/admin_thumb/') ?>'+result.url+'" alt=""/>\
							<ul>\
								<li class="view"><a href="../'+result.url+'" rel="facebox"><?php echo __('See') ?></a></li>\
								<li class="delete"><a href="'+result.deleteurl+'"><?php echo __('Delete') ?></a></li>\
							</ul>\
						</li>');
			$('a[rel*=facebox]').facebox()
		},
		'onAllComplete'  : function(event,data) {
			$(elID).find('.uploadmsg').text(data.filesUploaded + ' <?php echo __('files uploaded') ?>, ' + data.errors + ' <?php echo __('erreurs') ?>.');
		},
		'onError': function (event,ID,fileObj,errorObj) {
			console.log(errorObj.type + ' <?php echo __('Error:') ?> ' + errorObj.info);
		},
	  });
	};
	// ]]>
	</script>
	<?php \Coxis\Core\HTML::show_all() ?>
</body>
</html>