/*
mycheckbox('#content input[type="checkbox"]', 'img/checkbox-checked.png', 'img/checkbox-unchecked.png');
*/

function mycheckbox(selector, checkedImg, uncheckedImg) {
	var toggleCb = function(img) {
		var e = img.next();
		if(e.is(':checked'))
			img.attr('src', checkedImg);
		else
			img.attr('src', uncheckedImg);
	}

	$(selector).each(function() {
		var e = $(this);
		var img = $('<img style="cursor:pointer" class="checkbox">');
		img.click(function() {
			$(this).next().attr('checked', !$(this).next().attr('checked'));
			toggleCb($(this));
		});
		e.before(img);
		toggleCb(img);
		e.css('display', 'none');
	});
}