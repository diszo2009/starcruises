<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.4.0_2.0.3_467520
 * Purchase ID: JlNeBKBvSqsIsT7whc80ZpBA38zH86mwW38f4D7p5H
 * Generated:   2013-01-14 03:56:09
 * File path:   app/code/local/Aitoc/Aitpermissions/Model/Approve.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ PsPqMYUZqRDyBmMO('c37861d535517b4fc7293c5d4428affd'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Model_Approve extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('aitpermissions/approve', 'id');
    }

/*
 * @refactor
 * load single record by product id
 * rename to isProductApproved
 */
    public function isApproved($productId)
    {
        $collection = array();
        $collection = $this->getCollection()->loadByProductId($productId);
        foreach ($collection as $item)
        {
            return $item->getIsApproved();
        }

        return true;
    }

/*
 * @refactor
 * second parameter does not correspond to method name/purpose
 * split into separate methods approveProduct / disapproveProduct
 * remove magic number
 */
    public function approve($productId, $status = 1)
    {
        if ($status == Aitoc_Aitpermissions_Model_Rewrite_CatalogProductStatus::STATUS_AWAITING)
        {
            $status = 0;
        }
        
        $collection = $this->getCollection()->loadByProductId($productId);
        if ($collection->getSize() > 0)
        {
            foreach ($collection as $item)
            {
                $item->setIsApproved($status)->save();
            }
        }
        else
        {
            $this->setProductId($productId)->setIsApproved($status)->save();
        }

        return true;
    }
} } 