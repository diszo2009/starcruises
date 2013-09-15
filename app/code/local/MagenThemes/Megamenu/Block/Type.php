<?php
/******************************************************
 * @package Megamenu module for Magento 1.4.x.x and Magento 1.5.x.x
 * @version 1.5.0.4
 * @author http://www.9magentothemes.com
 * @copyright (C) 2011- 9MagentoThemes.Com
 * @license PHP files are GNU/GPL
*******************************************************/
?>
<?php
class MagenThemes_Megamenu_Block_Type extends Mage_Core_Block_Template
{
    protected $_hasContent = true;
    protected $_hasLink = true;
    protected $_type = null;
    protected $_menu = null;
    protected $_level = 0;
    protected $_routeName = null;
    protected $_paramName = null;

    public function setMenu($menu, $level=0) {
        $this->_menu = $menu;
        $this->_level = $level;
        return $this;
    }
    
    public function hasContent() {
        if($this->_hasContent == true) {
            return true;
        }
        return false;
    }
    
    public function hasLink() {
        if($this->_hasLink == true) {
            return true;
        }
        return false;
    }
    
    public function getContentType() {
        if($this->hasContent()) {
            return $this->getLayout()->createBlock('cms/block')->setBlockId($this->_menu->getArticle())->toHtml();
        }
        return null;
    }
    
    public function getObjectType() {
        return $this->getLayout()->getBlock('megamenu.nav')->getType($this->_type);
    }
    
    public function getModelOfType() {
        return $this->getObjectType()->getModel();
    }
    
    public function getUrlType() {
        return Mage::getModel($this->getModelOfType())->load($this->_menu->getArticle())->getUrl();
    }
    
    private function _getObjectType($type) {
        return $this->getLayout()->getBlock('megamenu.nav')->getType($type);
    }
    
    public function activeMenu($param) {
        if($this->_routeName == null) {
            return false;
        }
        
        if($this->getRequest()->getRouteName() == $this->_routeName) {
            if($this->_paramName == null) {
                return false;
            }
            
            if($this->getRequest()->getParam($this->_paramName) == $param) {
                return true;
            }
        }
        return false;
    }
    
    public function drawItem() {
        $html = '';
        if($this->_type == null) {
            return $html;
        }
        
        if(!$this->_menu instanceof MagenThemes_Megamenu_Model_Megamenu) {
            return $html;
        }
        
       	if($this->_menu->getStatus() == MagenThemes_Megamenu_Model_Status::STATUS_DISABLED) {
       		return $html;
       	}
        
        if($this->_level == 0) {
            $html .= '<li class="root ';
            if($this->activeMenu($this->_menu->getArticle()))
                $html .= 'active ';
	    if($this->_menu->hasChild(true))
		    $html .= 'parent';
            $html .= '" ';
        } else {
            if($this->_menu->isGroup()) {
                $html .= '<li class="group ';
                if($this->activeMenu($this->_menu->getArticle()))
                    $html .= 'active ';
                $html .= '" ';
            } else {
                $html .= '<li ';
                if($this->activeMenu($this->_menu->getArticle())) {
                    $html .= 'class="active" ';
                }
            }
        }
        if($this->_menu->hasChild(true)) {
            if(!$this->_menu->isGroup() && $this->_level != 0) {
                $html .= 'onmouseover="megamenu.showSubMegamenu(this, 1);" onmouseout="megamenu.showSubMegamenu(this, 0);"';
            }
        }
        $html .= '>';
	if($this->_menu->getType() == 'external_link') {
	    $html .= '<a class="megamenu-title" ';
	    if($this->_menu->getUrl())
		$html .= 'href="'.$this->_menu->getUrl().'" ';
	    if($this->_menu->getNofollow() == 1) {
		$html .= 'rel="nofollow"';
	    }
	    $html .= '>';
	    if($this->_menu->getImage())
		$html .= '<img alt="'.$this->_menu->getTitle().'" src="'.Mage::getBaseUrl('media').$this->_menu->getImage().'" width="13" height="13" class="icon-megamenu" />';
	    else
		if($this->_level != 0) 
		    $html .= '<span class="no-icon-megamenu"></span>';
	    $html .= '<span>'.$this->_menu->getTitle().'</span></a>';
	} else {
	    if($this->_menu->showTitle()) {
		$html .= '<a href="'.$this->getUrlType().'" class="megamenu-title" ';
		if($this->_menu->getNofollow() == 1) {
		    $html .= 'rel="nofollow" ';
		}
		if(!$this->hasLink())
		    $html .= 'onclick="return false;"';
		$html .= '>';
		if($this->_menu->getImage())
		    $html .= '<img alt="'.$this->_menu->getTitle().'" src="'.Mage::getBaseUrl('media').$this->_menu->getImage().'" width="13" height="13" class="icon-megamenu" />';
		else
		    if($this->_level != 0) 
			$html .= '<span class="no-icon-megamenu"></span>';
		$html .= '<span>'.$this->_menu->getTitle().'</span></a>';
	    }
	    if($this->_menu->isContent())
		$html .= $this->getLayout()->createBlock($this->_getObjectType($this->_menu->getType())->getBlock())
			    ->setMenu($this->_menu, $this->_level+1)
			    ->getContentType();
	}
        if($this->_menu->hasChild(true) && $this->_menu->showSub()) {
            if($this->_level != 0 && !$this->_menu->isGroup()) {
                $html .= '<div class="sub-megamenu" ';
            } else {
                $html .= '<div class="childcontent" ';
            }
            
            if($this->_menu->getWidth()) {
                $html .= 'style="width:'.$this->_menu->getWidth().'px;"';
            }
            
            $html .= '>';
            
            $colPositions = array();
            if($this->_menu->getSubitemWidth()) {
                $colPositions = Mage::helper('megamenu')->getColpositions($this->_menu->getSubitemWidth());   
            }
            
            if(count($colPositions)) {
                foreach($colPositions as $col => $width) {
                    $html .= '<ul class="'.$col.'" style="width:'.$width.'px">';
                    $childItemsWidthCol = $this->_menu->getChildItem($col);
                    foreach($childItemsWidthCol as $childItem) {
                        $html .= $this->getLayout()->createBlock($this->_getObjectType($childItem->getType())->getBlock())
                                    ->setMenu($childItem, $this->_level+1)
                                    ->drawItem();
                    }
                    $html .= '</ul>';
                }
            } else {
                $html .= '<ul style="width:'.$this->_menu->getWidth().'px">';
                $childItemsWidthCol = $this->_menu->getChildItem();
                foreach($childItemsWidthCol as $childItem) {
                    $html .= $this->getLayout()->createBlock($this->_getObjectType($childItem->getType())->getBlock())
                                ->setMenu($childItem, $this->_level+1)
                                ->drawItem();
                }
                $html .= '</ul>';
            }
            
            $html .= '</div>';
        }
        $html .= '</li>';
        return $html;
    }
}