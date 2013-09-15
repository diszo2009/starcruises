/******************************************************
 * @package Megamenu module for Magento 1.4.x.x and Magento 1.5.x.x
 * @version 1.5.0.4
 * @author http://www.9magentothemes.com
 * @copyright (C) 2011- 9MagentoThemes.Com
 * @license PHP files are GNU/GPL
*******************************************************/
if(!Effect.Transitions.backOut)
{
    Effect.Transitions.backOut=function(pos){
            return((pos-1)*(pos-1)*((2.7)*(pos-1)+1.7)+1);
        };
}

var Megamenu = Class.create(
{
    initialize:function(menu,options){
        this.options=Object.extend(
            {
                transition:Effect.Transitions.sineInOut,duration:0.5
            },
            options||{}
        );
        this.menu = $(menu);
        if(options.fancy) {
        	this.activeMenu();
        	if(Prototype.Browser.IE6){
	            try{
	                document.execCommand('BackgroundImageCache',false,true);
	            }catch(e){}
	        }
        	var div = new Element('DIV').addClassName('left');
	        this.back = new Element('DIV').addClassName('background');
	        this.current = this.menu.down('li.current');
        	 
	        /*Add current class */
            if(this.current)
                this.setCurrent(this.current);
            else
                this.setCurrent(this.menu.down('a'));
            
        }
        /* Insert first, last item to ul li of megamenu */
    	$$('#' + this.menu.id + ' ul li.root').first().addClassName('first');
    	$$('#' + this.menu.id + ' ul li.root').last().addClassName('last');
    	$$('#' + this.menu.id + ' ul li.root').each(function(element){
    		this.decorateMenu(element);
    	}.bind(this));
        $$('li.root').each(function(elementRoot){
                Event.observe(elementRoot, 'mouseover', function(event){
                    if(!elementRoot.hasClassName('over'))
                        elementRoot.addClassName('over');
                    else
                        return;
                }.bind(this));
                
                Event.observe(elementRoot, 'mouseout', function(){
                    elementRoot.removeClassName('over');
                }.bind(this));
            });
    },
    
    decorateMenu:function(element) {
    	element.childElements().each(function(childElement){
    		if(childElement.hasClassName('childcontent')) {
    			childElement.childElements().each(function(childChildElement) {
    				if(childChildElement.childElements().length > 0) {
	    				childChildElement.childElements().each(function(childChildChildElement) {
	    					this.decorateMenu(childChildChildElement);
	    				}.bind(this));
	    				childChildElement.childElements().first().addClassName('first');
	    				childChildElement.childElements().last().addClassName('last');
    				}
    			}.bind(this));
    		}
    	}.bind(this));
    },
    
    unregisterEvents:function(){
        this.menu.stopObserving('click',this.observeClick);
        this.menu.stopObserving('mousemove',this.observeMouseMove);
        this.menu.stopObserving('mouseout',this.observeMouseOut);
    },
    
    clickItem:function(element){
        if(!element||element.tagName!="LI")
            return;
        if(!this.current)
            this.setCurrent(element);
            this.menu.down('li.current').removeClassName('current');
            this.current=element;
            $(this.current).addClassName('current');
    },
    
    setCurrent:function(el){
        this.back.setStyle(
            {
                left:this.getOffsetX(el)+'px',
                width:el.offsetWidth+'px',
                visibility:'visible'
            });
        this.current=el;
    },
    
    getOffsetX:function(el){
        return Position.cumulativeOffset(el)[0]-Position.cumulativeOffset(this.menu)[0];
    },
    
    moveBg:function(object){
        if(object == this.object)
            return;
        if(typeof object == 'object' && object != null) {
            //move background div
            if(object.tagName=='LI') {
                object=object;
            } else {
                object=Event.element(object);
            }
            if(!this.current||this.object==object||!object||object.tagName!="LI")
                return;
            this.moveTimeout();
            this.timeout=window.setTimeout(function(){
                var styles="width:"+object.offsetWidth+"px; left:"+this.getOffsetX(object)+"px;";
                if(this.morph&&this.morph.state=='running')
                    this.morph.cancel();
                this.morph=new Effect.Morph(this.back,{
                    style:styles,
                    duration:this.options.duration,
                    transition:this.options.transition
                });
            }.bind(this),90);
            this.object=object;
        }
    },
    
    moveTimeout:function(){
        if(typeof this.timeout=="number"){
            window.clearTimeout(this.timeout);
            delete this.timeout;
        }
    },
    
    toggleMegamenu: function(element, active) {
        if(element.tagName != 'LI' || element.hasClassName('group'))
            return;
        if(active == 0) {
            element.removeClassName('over');
        } else {
            element.addClassName('over');
        }
    },
    
    showSubMegamenu: function(element, active) {
        if(active == 0) {
            //var widthElement = element.down('div.sub-megamenu').getWidth();
            //element.down('div.sub-megamenu').removeAttribute('style');
            //element.down('div.sub-megamenu').setStyle({width:widthElement+'px'});
        	//wait to fix
        	element.down('div.sub-megamenu').setStyle({left:'-10000px'});
        } else {
            if(element.down('div.sub-megamenu')) {
                var ulParentWidth = element.getWidth();
                element.down('div.sub-megamenu').setStyle({left:ulParentWidth+'px'});
            }
        }
    },
    
    activeMenu: function() {
        $$('li.root').each(function(element){
                if(element.hasClassName('active')) {
                    element.addClassName('current');
                } else {
                    elementActives = element.getElementsByClassName('active');
                    if(elementActives.length > 0) {
                        element.addClassName('current');
                    }
                }
            });
        
    }
});