jQuery.fn.megamenu = function(options) {
	options = jQuery.extend({ 
		animation: "slide",  
		mm_timeout: 300
	}, options);
	var $megamenu_object = this; 
	$megamenu_object.find("li.parent").each(function(){  
		var $mm_item = jQuery(this).children('div'); 
		$mm_item.hide();  
		$mm_item.wrapInner('<div class="mm-item-base clearfix"></div>'); 
		var $timer = 0; 
		jQuery(this).bind('mouseenter', function(e){ 
			var mm_item_obj = jQuery(this).children('div'); 
			clearTimeout($timer);
			$timer = setTimeout(function(){  
				switch(options.animation) {
					case "show":
						mm_item_obj.show().addClass("shown-sub");
						break;
					case "slide":
						mm_item_obj.height("auto");
						mm_item_obj.slideDown('fast').addClass("shown-sub");
						break;
					case "fade":
						mm_item_obj.fadeTo('fast', 1).addClass("shown-sub");
						break; 
				}
			}, options.mm_timeout);	
		}); 
		jQuery(this).bind('mouseleave', function(e){ 
			clearTimeout($timer); 
			var mm_item_obj = jQuery(this).children('div'); 
			switch(options.animation) {
				case "show":
					  mm_item_obj.hide(); 
					  break;
				case "slide":  
					  mm_item_obj.slideUp( 'fast',  function() { 
					  });
					  break;
				case "fade":
					  mm_item_obj.fadeOut( 'fast', function() { 
					  });
					  break;  
              break;
			} 
		}); 
	}); 
	this.show();
};
