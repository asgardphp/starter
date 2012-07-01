$(function(){
	window.cinema = function(e){
		$(window).scrollTop(0);
		
		var black_screen = $('<div id="cinema" style="display:none; position:absolute; z-index:2000; top:0; left:0; height:100%; margin:0; width:100%; background-color:#000; opacity:0.9;">');
		$('body').prepend(black_screen);
		var dialog = $('<div id="dialog" style="display:none; position:absolute; top:0; left:50%; top:50%; background-color:#fff; z-index:3000;">');
		//margin-top:-150px; margin-left:-200px; width:400px; height:300px;
		var width = e.width()+(parseInt(e.css('padding-left')) ? parseInt(e.css('padding-left')):0)+(parseInt(e.css('padding-right')) ? parseInt(e.css('padding-right')):0);
		var height = e.height()+(parseInt(e.css('padding-top')) ? parseInt(e.css('padding-top')):0)+(parseInt(e.css('padding-bottom')) ? parseInt(e.css('padding-bottom')):0);
		dialog.width(width);
		dialog.height(height);
		dialog.css('margin-top', -(height/2));
		dialog.css('margin-left', -(width/2));
		e.css('position', 'relative');
		e.css('z-index', 4000);
		/*e.css('overflow', 'default');*/
		dialog.append(e);
		black_screen.click(function(){
			black_screen.remove();
			dialog.remove();
			e.remove();
		});
		$('body').prepend(dialog);
		black_screen.fadeIn();
		dialog.fadeIn();
	};
});