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
class MagenThemes_Megamenu_Model_Mysql4_Megamenu extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the megamenu_id refers to the key field in your database table.
        $this->_init('megamenu/megamenu', 'megamenu_id');
    }
    
    protected function _afterLoad(Mage_Core_Model_Abstract $object) {
        if (!$object->getIsMassDelete()) {
            $object = $this->__loadGroup($object);
        }

        return parent::_afterLoad($object);
    }
    
    protected function _afterSave(Mage_Core_Model_Abstract $object) {
        if (!$object->getIsMassStatus()) {
            $this->__saveToGroupTable($object);
        }

        return parent::_afterSave($object);
    }
    
    /*protected function _beforeDelete(Mage_Core_Model_Abstract $object) {
        $adapter = $this->_getReadAdapter();
        $adapter->delete($this->getTable('megamenu/megamenu_store'), 'megamenu_id='.$object->getId());

        return parent::_beforeDelete($object);
    }*/
    
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $select->join(array('cbs' => $this->getTable('megamenu/megamenu_store')), $this->getMainTable().'.megamenu_id = cbs.megamenu_id')
                    ->where('cbs.store_id in (0, ?) ', $object->getStoreId())
                    ->order('store_id DESC')
                    ->limit(1);
        }
        return $select;
    }
    
    private function __loadGroup(Mage_Core_Model_Abstract $object) {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('megamenu/megamenu_group'))
                ->where('megamenu_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $array = array();
            foreach ($data as $row) {
                $array[] = array('megamenu_group_id' => $row['megamenu_group_id'],
                                 'sort_order' => $row['sort_order']
                            );
            }
            $object->setData('groups', $array);
        }
        return $object;
    }
    
    private function __saveToGroupTable(Mage_Core_Model_Abstract $object) {
        if ($object->getData('groups')) {
            $condition = $this->_getWriteAdapter()->quoteInto('megamenu_id = ?', $object->getId());
            $this->_getWriteAdapter()->delete($this->getTable('megamenu/megamenu_group'), $condition);
            foreach ((array)$object->getData('groups') as $group) {
                $groupArray = array();
                $groupArray['megamenu_id'] = $object->getId();
                $groupArray['megamenu_group_id'] = $group['megamenu_group_id'];
                if(isset($group['sort_order'])) {
                    $groupArray['sort_order'] = $group['sort_order'];
                } else {
                    $groupArray['sort_order'] = 0;
                }
                $this->_getWriteAdapter()->insert($this->getTable('megamenu/megamenu_group'), $groupArray);
            }
        }
    }
}