(function($){
	/* PLACEHOLDER */
	function placeholder(el, placeholder) {
		var e = $(el);
		e.val(placeholder);
		e.focus(function () {
			if (e.val() == placeholder) e.val("");
		});
		e.focusout(function () {
			if (e.val() == "") e.val(placeholder);
		});
	}
	
	$(function () {
		//~ placeholder('input[name="prix_min"]', 'Min');
	});
})(jQuery);