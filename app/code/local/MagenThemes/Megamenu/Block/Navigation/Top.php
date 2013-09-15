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
class MagenThemes_Megamenu_Block_Navigation_Top extends Mage_Core_Block_Template
{
    public function getType($type) {
        return $this->getLayout()->getBlock('megamenu.nav')->getType($type);
    }
    
    public function getRootMenu() {
        return Mage::getModel('megamenu/megamenu')->getCollection()
                ->addFieldToFilter('level', 1)
                ->setOrder('position', 'ASC');
    }
}