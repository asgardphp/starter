function prepareModal(trigger, target) {
	$(trigger).css('cursor', 'pointer');
	$(trigger).click(function () {
		modal(target);
	});
}

function modal(id) {
	bg = $('<div style="position:fixed;height:100%;width:100%;background-color:black;opacity:0.6;z-index:1000;"></div>');
	$('body').prepend(bg);
	bg.click(function(){
		close(id);
	});
	
	var modal = $(id);
	$('body').prepend(modal);
	modal.css('display', 'block');
	modal.css('position', 'fixed');
	modal.css('z-index', '1001');
	modal.css('top', '50px');
	modal.css('left', '50%');
	modal.css('margin-left', -modal.width() / 2);
	modal.find('.close').click(function () {
		close(id);
	});
}

function close(id) {
	bg.remove();
	$(id).css('display', 'none');
}