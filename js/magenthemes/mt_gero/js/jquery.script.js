/*$mtkb(window).load(function(){
	$mtkb('#nav li').hover(
		function(){
			$mtkb(this).children('div').addClass('shown-sub').slideDown(300); 
		},
		function(){
			$mtkb(this).children('div').slideUp(100);
		}
	); 
});
*/
$mtkb(document).ready(function() {
	$mtkb('.top-cart').hoverIntent({
		interval: 20,
		over: function() { $mtkb(this).addClass('cart-active').find('.mtajaxcart').slideDown(); },
		out: function() { $mtkb(this).removeClass('cart-active').find('.mtajaxcart').slideUp(); }
	}); 
	$mtkb("#back-top").hide();  
	$mtkb(function () {
		$mtkb(window).scroll(function () {
			if ($mtkb(this).scrollTop() > 100) {
				$mtkb('#back-top').fadeIn();
			} else {
				$mtkb('#back-top').fadeOut();
			}
		}); 
		$mtkb('#back-top a').click(function () {
			$mtkb('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	}); 
});   
function showOptions(id){
	$mtkb('#fancybox'+id).trigger('click');
}
function setAjaxData(data,iframe){
    if(data.status == 'ERROR'){
        alert(data.message);
    }else{
    	$mtkb('.top-cart').html('');
        if($mtkb('.top-cart')){
        	$mtkb('.top-cart').append(data.output);
        } 
        $mtkb.fancybox.close();
    }
}
function ajaxaddcart(url,id){ 
	$mtkb('.cart-loading').show();
	$mtkb('.cart').hide();
    var cart_x = $mtkb('.top-cart').offset().left;
	var cart_y = $mtkb('.top-cart').offset().top;
	var product_x = $mtkb('#'+id).offset().left;
	var product_y = $mtkb('#'+id).offset().top;
	var gotoX = cart_x - product_x;
	var gotoY = cart_y - product_y;
	var newImageWidth = $mtkb('#'+id).width()/6;
	var newImageHeight = $mtkb('#'+id).height()/6;  
	$mtkb('#'+id+' img')
	.clone()
	.prependTo('#'+id+'')
	.css({'position' : 'absolute'})
	.animate({opacity: 0.8}, 100 )
	.animate({opacity: 0, marginLeft: gotoX, marginTop: gotoY, width: newImageWidth, height: newImageHeight}, 1200, function() {
		$mtkb('#'+id+' img:first').remove(); 
	});
	data = '&isAjax=1&qty=1';
    url = url.replace("checkout/cart","mtgeroadmin/index"); 
    try {
    	$mtkb.ajax( {
            url : url,
            dataType : 'json',
            data: data,
            type: 'post',
            success : function(data) { 
                setAjaxData(data,false); 
                $mtkb('.cart-loading').hide();
                $mtkb('.cart').show();
            }
        });
    } catch (e) {
    }
} 
function deletecart(url){
	$mtkb('.cart-loading').show();
	$mtkb('.cart').hide(); 
	data = '&isAjax=1&qty=1';
    url = url.replace("checkout/cart","mtgeroadmin/index");
    $mtkb.ajax( {
        url : url,
        dataType : 'json',
        data: data,
        type: 'post',
        success : function(data) { 
            setAjaxData(data,false); 
            $mtkb('.cart-loading').hide();
            $mtkb('.cart').show();
        }
    });
}
$mtkb(document).ready(function(){
	$mtkb('.fancybox').fancybox(
        {
           hideOnContentClick : true,
           width: 382,
           autoDimensions: true,
           type : 'iframe',
           showTitle: false,
           scrolling: 'no',
           onComplete: function(){ 
        	   $mtkb('#fancybox-frame').load(function() { 
        		   $mtkb('#fancybox-content').height($mtkb(this).contents().find('body').height()+30);
        		   $mtkb.fancybox.resize();
             });

           }
        }
    );
});
