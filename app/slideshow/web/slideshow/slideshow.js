(function($) {
	var delay = 2000;
	
	function slideshow(id) {
		var _this = this;
		this.j = $(id);
		this.pos = 1;

		this.j.find('.slideshow-left').click(function() {
			_this.move(_this.pos-1);
		});
		this.j.find('.slideshow-right').click(function() {
			_this.move(_this.pos+1);
		});
		
		this.interval = function(fct, delay) {
			clearInterval(this.inter);
			this.inter = setInterval(fct, delay);
		}
	
		this.move = function(newpos) {
			var width = this.j.find('.slideshow-container li:first-child').width();
			var count = this.j.find('.slideshow-container li').length;
			
			if(newpos < 1)
				newpos = count;
			if(newpos > count)
				newpos = 1;
			
			var margin = (newpos-1)*width;
			this.j.find('.slideshow-container').animate({'margin-left': -margin}, 'slow');
			
			_this.pos = newpos;
			
			this.interval(function(){_this.move(_this.pos+1)}, delay);
		}
		
		this.interval(function(){_this.move(_this.pos+1)}, delay);
	}
	
			
	$(function(){
		slideshow1 = new slideshow('.slideshow');
	});
})(jQuery);