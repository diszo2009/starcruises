<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.4.0_2.0.3_467520
 * Purchase ID: JlNeBKBvSqsIsT7whc80ZpBA38zH86mwW38f4D7p5H
 * Generated:   2013-01-14 03:56:09
 * File path:   app/code/local/Aitoc/Aitpermissions/Model/Rewrite/CatalogCategory.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ dsdojYNyoukBZejl('602866e2f5916d9af4f1403bb7033ff7'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Model_Rewrite_CatalogCategory extends Mage_Catalog_Model_Category
{
    protected function _beforeSave()
    {
        if (!$this->getId() && !Mage::registry('aitemails_category_is_new'))
        {
            Mage::register('aitemails_category_is_new', true);
        }
        
        return parent::_beforeSave();
    }
    
    protected function _afterSave()
    {
        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            $role->addAllowedCategoryId($this->getId(), $this->_getCurrentStoreId());
            
            if (true === Mage::registry('aitemails_category_is_new'))
            {
                Mage::unregister('aitemails_category_is_new');
                $this->setStoreId(0);
                $this->setIsActive(false);
                $this->save();
            }
        }
        
        return parent::_afterSave();
    }

    private function _getCurrentStoreId()
    {
        $storeviewId = Mage::app()->getRequest()->getParam('store');
        return Mage::getModel('core/store')->load($storeviewId)->getGroupId();
    }
} } 