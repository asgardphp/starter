placeholder = function(selector, placeholder) {
	(function($){
		$(function(){
			var e = $(selector);
			e.val(placeholder);
			e.focus(function(){
				if(e.val()==placeholder)
					e.val("");
			});
			e.focusout(function(){
				if(e.val()=="")
					e.val(placeholder);
			});
		});
	})(jQuery);
}