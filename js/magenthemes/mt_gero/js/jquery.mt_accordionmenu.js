(function($){
    $.fn.extend({ 
		mtAccordionMenu: function(options) { 
			var defaults = {
				accordion: 'true',
				speed: 300,
				closedSign: 'collapse',
				openedSign: 'expand'
			}; 
			var opts = $.extend(defaults, options); 
			var $this = $(this);
			$this.find("li").each(function() {
				if($(this).find("ul").size() != 0){ 
					$(this).find("a:first").after("<span class='"+ opts.closedSign +"'>"+ opts.closedSign +"</span>"); 
					if($(this).find("a:first").attr('href') == "#"){
						$(this).find("a:first").click(function(){return false;});
					}
				}
			}); 
			$this.find("li.active").each(function() {
				$(this).parents("ul").slideDown(opts.speed);
				$(this).parents("ul").parent("li").find("span:first").html(opts.openedSign).removeClass(opts.closedSign);
			});
			if(opts.mouseType==0){
				$this.find("li span").click(function() {
					if($(this).parent().find("ul").size() != 0){
						if(opts.accordion){
							//Do nothing when the list is open
							if(!$(this).parent().find("ul").is(':visible')){
								parents = $(this).parent().parents("ul");
								visible = $this.find("ul:visible");
								visible.each(function(visibleIndex){
									var close = true;
									parents.each(function(parentIndex){
										if(parents[parentIndex] == visible[visibleIndex]){
											close = false;
											return false;
										}
									});
									if(close){
										if($(this).parent().find("ul") != visible[visibleIndex]){
											$(visible[visibleIndex]).slideUp(opts.speed, function(){
												$(this).parent("li").find("span:first").html(opts.closedSign).addClass(opts.closedSign);
											});
											
										}
									}
								});
							}
						}
						if($(this).parent().find("ul:first").is(":visible")){
							$(this).parent().find("ul:first").slideUp(opts.speed, function(){
								$(this).parent("li").find("span:first").delay(opts.speed+1000).html(opts.closedSign).addClass(opts.closedSign);
							});
							
							
						}else{
							$(this).parent().find("ul:first").slideDown(opts.speed, function(){
								$(this).parent("li").find("span:first").delay(opts.speed+1000).html(opts.openedSign).removeClass(opts.closedSign);
							});
						}
					}
				});
			}
			if(opts.mouseType>0){
				$this.find("li a").mouseenter(function() { 
					if($(this).parent().find("ul").size() != 0){
						if(opts.accordion){ 
							if(!$(this).parent().find("ul").is(':visible')){
								parents = $(this).parent().parents("ul");
								visible = $this.find("ul:visible");
								visible.each(function(visibleIndex){
									var close = true;
									parents.each(function(parentIndex){
										if(parents[parentIndex] == visible[visibleIndex]){
											close = false;
											return false;
										}
									});
									if(close){
										if($(this).parent().find("ul") != visible[visibleIndex]){
											$(visible[visibleIndex]).slideUp(opts.speed, function(){
												$(this).parent("li").find("span:first").html(opts.closedSign).addClass(opts.closedSign);
											});
											
										}
									}
								});
							}
						}
						if($(this).parent().find("ul:first").is(":visible")){
							$(this).parent().find("ul:first").slideUp(opts.speed, function(){
								$(this).parent("li").find("span:first").delay(opts.speed+1000).html(opts.closedSign).addClass(opts.closedSign);
							});
							
							
						}else{
							$(this).parent().find("ul:first").slideDown(opts.speed, function(){
								$(this).parent("li").find("span:first").delay(opts.speed+1000).html(opts.openedSign).removeClass(opts.closedSign);
							});
						}
					}
				});
			}
		}
	});
})(jQuery);