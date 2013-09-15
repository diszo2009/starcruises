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
class MagenThemes_Megamenu_Model_Group extends MagenThemes_Megamenu_Model_Abtract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('megamenu/group');
    }
    
    public function isActive() {
        if($this->getStatus() == Magenthemes_Megamenu_Model_Status::STATUS_ENABLED) {
            return true;
        }
        return false;
    }
    
    public function loadMegamenus() {
        return $this->getResource()->loadMegamenus($this);
    }
}