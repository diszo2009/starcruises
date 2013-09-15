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
class MagenThemes_Megamenu_Model_Mysql4_Group extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('megamenu/group', 'megamenu_group_id');
    }
    
    protected function _afterLoad(Mage_Core_Model_Abstract $object) {
        if (!$object->getIsMassDelete()) {
            $object = $this->__loadStore($object);
            $object = $this->__loadMegamenus($object);
        }

        return parent::_afterLoad($object);
    }
    
    protected function _afterSave(Mage_Core_Model_Abstract $object) {
        if (!$object->getIsMassStatus()) {
            $this->__saveToStoreTable($object);
            $this->__saveToMegamenuTable($object);
        }

        return parent::_afterSave($object);
    }
    
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $select->join(array('cbs' => $this->getTable('megamenu/group_store')), $this->getMainTable().'.megamenu_group_id = cbs.megamenu_group_id')
                    ->where('cbs.store_id in (0, ?) ', $object->getStoreId())
                    ->order('store_id DESC')
                    ->limit(1);
        }
        return $select;
    }
    
    protected function _beforeDelete(Mage_Core_Model_Abstract $object) {
        $adapter = $this->_getReadAdapter();
        $adapter->delete($this->getTable('megamenu/group_store'), 'megamenu_group_id='.$object->getId());
        $adapter->delete($this->getTable('megamenu/group_relation_group'), 'megamenu_group_id='.$object->getId());

        return parent::_beforeDelete($object);
    }
    
    private function __loadStore(Mage_Core_Model_Abstract $object) {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('megamenu/group_store'))
                ->where('megamenu_group_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $array = array();
            foreach ($data as $row) {
                $array[] = $row['store_id'];
            }
            $object->setData('stores', $array);
        }
        return $object;
    }
    
    private function __loadMegamenus(Mage_Core_Model_Abstract $object) {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('megamenu/megamenu_group'))
                ->where('megamenu_group_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $array = array();
            foreach ($data as $row) {
                $array[] = array('megamenu_id' => $row['megamenu_id'],
                                 'sort_order' => $row['sort_order']
                            );
            }
            $object->setData('megamenus', $array);
        }
        return $object;
    }
    
    private function __saveToStoreTable(Mage_Core_Model_Abstract $object) {
        if (!$object->getData('stores')) {
            $condition = $this->_getWriteAdapter()->quoteInto('megamenu_group_id = ?', $object->getId());
            $this->_getWriteAdapter()->delete($this->getTable('megamenu/group_store'), $condition);

            $storeArray = array(
                'megamenu_group_id' => $object->getId(),
                'store_id' => '0');
            $this->_getWriteAdapter()->insert($this->getTable('megamenu/group_store'), $storeArray);
            return true;
        }

        $condition = $this->_getWriteAdapter()->quoteInto('megamenu_group_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('megamenu/group_store'), $condition);
        foreach ((array)$object->getData('stores') as $store) {
            $storeArray = array();
            $storeArray['megamenu_group_id'] = $object->getId();
            $storeArray['store_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('megamenu/group_store'), $storeArray);
        }
    }
    
    private function __saveToMegamenuTable(Mage_Core_Model_Abstract $object) {
        if ($object->getData('megamenus')) {
            $condition = $this->_getWriteAdapter()->quoteInto('megamenu_group_id = ?', $object->getId());
            $this->_getWriteAdapter()->delete($this->getTable('megamenu/megamenu_group'), $condition);
            foreach ((array)$object->getData('megamenus') as $megamenu) {
                $megamenuArray = array();
                $megamenuArray['megamenu_group_id'] = $object->getId();
                $megamenuArray['megamenu_id'] = $megamenu['megamenu_id'];
                $megamenuArray['sort_order'] = $megamenu['sort_order'];
                $this->_getWriteAdapter()->insert($this->getTable('megamenu/megamenu_group'), $megamenuArray);
            }
        } else {
            if($object->hasData('megamenus')) {
                $adapter = $this->_getReadAdapter();
                $adapter->delete($this->getTable('megamenu/megamenu_group'), 'megamenu_group_id='.$object->getId());
            }
        }
    }
    
    public function loadMegamenus(Mage_Core_Model_Abstract $object) {
        return $this->__loadMegamenus($object);
    }
}