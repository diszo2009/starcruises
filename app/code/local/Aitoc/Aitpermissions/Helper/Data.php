<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.4.0_2.0.3_467520
 * Purchase ID: JlNeBKBvSqsIsT7whc80ZpBA38zH86mwW38f4D7p5H
 * Generated:   2013-01-14 03:56:09
 * File path:   app/code/local/Aitoc/Aitpermissions/Helper/Data.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ WsWhgSTahQZmeMgV('b45186f038b4424df9278c7e41c8e1e5'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isShowingAllCustomers()
    {
        return Mage::getStoreConfig('admin/general/showallcustomers');
    }

    public function isAllowedDeletePerWebsite()
    {
        return Mage::getStoreConfig('admin/general/allowdelete_perwebsite');
    }

    public function isAllowedDeletePerStoreview()
    {
        return Mage::getStoreConfig('admin/general/allowdelete');
    }

    /**
     * backward compatibility with Shopping Assistant
     */
    public function getAllowedCategories()
    {
        return Mage::getSingleton('aitpermissions/role')->getAllowedCategoryIds();
    }
} } 