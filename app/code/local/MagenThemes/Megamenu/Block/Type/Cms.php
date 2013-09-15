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
class MagenThemes_Megamenu_Block_Type_Cms extends MagenThemes_Megamenu_Block_Type
{
    protected $_type = 'cms_page';
    protected $_hasContent = false;
    protected $_routeName = 'cms';
    protected $_paramName = 'page_id';
    
    public function getUrlType() {
    	$pageId = $this->_menu->getArticle();
        $page = Mage::getModel('cms/page')->load($pageId);
        if($page->getIdentifier() == Mage::getStoreConfig('web/default/cms_home_page')) {
            return Mage::getBaseUrl();
        } else {
            return Mage::helper('cms/page')->getPageUrl($this->_menu->getArticle());
        }
    }
    
    public function activeMenu($param) {
        if($this->_routeName == null) {
            return false;
        }
        
        if($this->getRequest()->getRouteName() == $this->_routeName) {
            if($this->_paramName == null) {
                return false;
            }
            if($this->getRequest()->getParam($this->_paramName)) {
                if($this->getRequest()->getParam($this->_paramName) == $param) {
                    return true;
                }
            } else {
                $identifier = Mage::getSingleton('cms/page')->getIdentifier();
                $cmsPage = Mage::getModel('cms/page')->load($this->_menu->getArticle());
                if($cmsPage->getIdentifier() == $identifier) {
                    return true;
                }
            }
        }
        return false;
    }
}