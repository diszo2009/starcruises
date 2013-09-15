(function($){
    $.fn.accordion=function(){
        return this.each(function(){
            var menu= $(this);
            menu.find('ul.listpanel li div.active').slideDown('medium'); 
            menu.find('ul.listpanel li > span.mttitle').bind('click',function(event){ 
                var currentlink=$(event.currentTarget);
                if (currentlink.parent().find('div.active').size()==1)
                {
                    currentlink.parent().find('div.active').slideUp('medium',function(){
                        currentlink.parent().find('div.active').removeClass('active');
						currentlink.find('span.arrows').removeClass('current');
                    });
                }
                else if (menu.find('ul.listpanel li div.active').size()==0)
                {
                    show(currentlink);
                }
                else
                {
                    menu.find('ul.listpanel li div.active').slideUp('medium',function(){
                        menu.find('ul.listpanel li div.mainpattern').removeClass('active');
						menu.find('ul.listpanel li span.mttitle > span.arrows').removeClass('current');
                        show(currentlink);
                    });
                } 
            });
            function show(currentlink){
                currentlink.parent().find('div.mainpattern').addClass('active');
				currentlink.find('span.arrows').addClass('current');
                currentlink.parent().find('div.mainpattern').slideDown('medium');
            }
        });
    }
})(jQuery);