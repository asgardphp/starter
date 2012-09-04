function dialog(msg) {
	var cover = $('<div id="cover" style="position:fixed; width:100%; height:100%; opacity:0.4; background-color:black; z-index:9998;"></div>');
	var dialog = $('<div style="text-align:center; position:fixed; top:50%; left:50%; margin-left:-150px; margin-top:-90px; background-image:url(images/dialog-bg.png); width:298px; height:141px; z-index:9999; padding-top:40px;"><span style="display: block;margin: 0 40px 0 40px;font-weight:bold;">'+msg+'</span><br/><span style="text-decoration:underline" class="close">fermer</span></div>');
	var close_button = $('<img src="images/close.png" style="position:absolute; top:15px; right:15px;" class="close"/>');
	
	function close() {
		cover.remove();
		dialog.remove();
	}
	
	$('body').prepend(cover);
	$('body').prepend(dialog);
	dialog.prepend(close_button);
	
	cover.click(close);
	$('.close').css('cursor', 'pointer').click(close);
}