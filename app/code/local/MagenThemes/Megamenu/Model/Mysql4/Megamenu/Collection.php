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
class MagenThemes_Megamenu_Model_Mysql4_Megamenu_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('megamenu/megamenu');
    }
    
    public function addStatusFilter() {
        $this->addFieldToFilter('status', MagenThemes_Megamenu_Model_Status::STATUS_ENABLED);
        return $this;
    }
    
    public function setStoreFilter($store) {
    	if (!Mage::app()->isSingleStoreMode() && $store) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }

            $this->getSelect()->where('store_id in (?)', array(0, $store));
            return $this;
        }
        return $this;
    }
    
    public function addIdFilter($megamenuIds) {
    	if (is_array($megamenuIds)) {
            if (empty($megamenuIds)) {
                $condition = '';
            } else {
                $condition = array('in' => $megamenuIds);
            }
        } elseif (is_numeric($megamenuIds)) {
            $condition = $megamenuIds;
        } elseif (is_string($megamenuIds)) {
            $ids = explode(',', $megamenuIds);
            if (empty($ids)) {
                $condition = $megamenuIds;
            } else {
                $condition = array('in' => $ids);
            }
        }
        $this->addFieldToFilter('parent_id', $condition);
        return $this;
    }
}