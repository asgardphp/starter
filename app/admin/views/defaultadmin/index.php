<style>
#dash-menu {
	width:900px;
	margin:0 auto;
}
#dash-menu li {
	float:left;
	list-style-type: none;
	width:200px;
	text-align: center;
	margin:10px;
}
#dash-menu li img, #dash-menu li embed {
	width:100px;
	height:100px;
	display:block;
	margin:0 auto;
}
#dash-menu li .title {
	display:block;
}
#dash-menu li .description {
	display:block;
	
}
</style>

<ul id="dash-menu">
	<?php \Flash::showAll() ?>
	<?php foreach(AdminMenu::$home as $link): ?>
	<li>
		<a href="<?php echo $link['link'] ?>"><img src="<?php echo $link['img'] ?>"></a>
		<a class="title" href="<?php echo $link['link'] ?>"><?php echo $link['title'] ?></a>
		<span class="description"><?php echo $link['description'] ?></span>
	</li>
	<?php endforeach ?>
</ul>