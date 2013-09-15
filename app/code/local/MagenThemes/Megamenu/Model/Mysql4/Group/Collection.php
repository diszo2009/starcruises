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
class MagenThemes_Megamenu_Model_Mysql4_Group_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('megamenu/group');
    }
    
    /**
     * Add Filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @return Magenthemes_Megamenu_Model_Mysql4_Group_Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = array($store->getId());
        }

        $this->getSelect()->join(
            array('store_table' => $this->getTable('megamenu/group_store')),
            'main_table.megamenu_group_id = store_table.megamenu_group_id',
            array()
        )
        ->where('store_table.store_id in (?)', ($withAdmin ? array(0, $store) : $store))
        ->group('main_table.megamenu_group_id');

        return $this;
    }
}