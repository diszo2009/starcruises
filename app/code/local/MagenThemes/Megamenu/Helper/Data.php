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
class MagenThemes_Megamenu_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getColpositions($subitemWidth) {
        $colPositions = array();
        $subitemWidth = explode(';', $subitemWidth);
        foreach($subitemWidth as $colPosition) {
            $colPosition = explode(':', $colPosition);
            $colPositions[$colPosition[0]] = $colPosition[1];
        }
        return $colPositions;
    }
    
    public function isActive() {
    	return Mage::getStoreConfig('megamenu/general/active');
    }
    
    public function isActiveFancy() {
    	return Mage::getStoreConfig('megamenu/fancy/active');
    }
    
    public function getDuration() {
    	return  Mage::getStoreConfig('megamenu/fancy/duration');
    }
}