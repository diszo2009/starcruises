(function($) {
	$.fn.funcyMenu = function(o) {
		o = $.extend({ fx: "linear", speed: 500, click: function(){} }, o || {}); 
		return this.each(function() {
			var me = $(this), noop = function(){},
				$back = $('<li class="background"><div class="left"></div></li>').appendTo(me),
				$li = $("li.root", this), curr = $("li.current", this)[0] || $($li[0]).addClass("current")[0]; 
				
			$li.not(".background").hover(function() {
				move(this);
			}, noop); 
			
			$(this).hover(noop, function() {
				move(curr);
			});  
			
			setCurr(curr); 
			function setCurr(el) { 
				$back.css({ "left": el.offsetLeft+"px", "width": el.offsetWidth+"px" });
				curr = el;
			}; 
			function move(el) { 
				$back.each(function() {
					$.dequeue(this, "fx"); }
				).animate({
					width: el.offsetWidth,
					left: el.offsetLeft
				}, o.speed, o.fx);
			}; 
		});
	};
})(jQuery);
