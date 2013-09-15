<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.4.0_2.0.3_467520
 * Purchase ID: JlNeBKBvSqsIsT7whc80ZpBA38zH86mwW38f4D7p5H
 * Generated:   2013-01-14 03:56:09
 * File path:   app/code/local/Aitoc/Aitpermissions/Model/Mysql4/Advancedrole/Collection.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ EsEpmSIDpTjkgymW('45951c226e389e2fd07ef6b626e153b4'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Model_Mysql4_Advancedrole_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('aitpermissions/advancedrole');
    }

    public function loadByRoleId($roleId)
    {
        $this->addFieldToFilter('role_id', $roleId);
        $this->load();
        return $this;
    }
    
    public function loadByRoleAndStore($roleId, $storeId)
    {
        $this->addFieldToFilter('role_id', $roleId);
        $this->addFieldToFilter('store_id', $storeId);
        $this->load();
        return $this;
    }
} } 