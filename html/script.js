$(document).delegate('.ui-page', 'pageshow', function () {
	function open(e) {
		$(e).find('ul').css('display', 'block');
		$(e).find('.morceau img').attr('src', 'img/fleche_bas.png');
	}
	function close(e) {
		$(e).find('ul').css('display', 'none');
		$(e).find('.morceau img').attr('src', 'img/fleche_droite.png');
	}
	open($('#menu > li').get(0));
	$('.morceau').unbind('click').click(function(e) {
		var e = $(e.currentTarget);
		if(e.parent().find('ul').css('display') == 'none')
			open(e.parent());
		else
			close(e.parent());
	});

	$('#zoom').unbind().click(function(e){
		var e = $(e.currentTarget);
		var path = e.attr('data-image');
		// console.log(path, e);
		var el = $('<img src="'+path+'"/>').css({'position':'absolute', 'top':0, 'left':0, 'width':960});
		el.click(function(e){
			var e = $(e.currentTarget);
			e.remove();
		});
		$('body').append(el);
	});
});