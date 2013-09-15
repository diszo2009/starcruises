<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.4.0_2.0.3_467520
 * Purchase ID: JlNeBKBvSqsIsT7whc80ZpBA38zH86mwW38f4D7p5H
 * Generated:   2013-01-14 03:56:09
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminPollGrid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ dsdojYNyoukBZejl('89caf74b15f5020fdc894d992d6fc978'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminPollGrid extends Mage_Adminhtml_Block_Poll_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('poll/poll')->getCollection();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            $collection->addStoreFilter($role->getAllowedStoreviewIds());
        }

        $this->setCollection($collection);
        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();

        if (!Mage::app()->isSingleStoreMode())
        {
            $this->getCollection()->addStoreData();
        }
        
        return $this;
    }
} } 