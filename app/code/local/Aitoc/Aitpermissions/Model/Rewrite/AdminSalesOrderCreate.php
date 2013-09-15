<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.4.0_2.0.3_467520
 * Purchase ID: JlNeBKBvSqsIsT7whc80ZpBA38zH86mwW38f4D7p5H
 * Generated:   2013-01-14 03:56:09
 * File path:   app/code/local/Aitoc/Aitpermissions/Model/Rewrite/AdminSalesOrderCreate.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ rsrikSCeiIBamrkE('c6e950eddad07ab46f71bdfb49bec8ac'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Model_Rewrite_AdminSalesOrderCreate extends Mage_Adminhtml_Model_Sales_Order_Create
{
    public function initFromOrder(Mage_Sales_Model_Order $order)
    {
        try
        {
            parent::initFromOrder($order);
        }
        catch (Exception $e)
        {
            return Mage::app()->getFrontController()->getResponse()->setRedirect(getenv("HTTP_REFERER"));
        }
        
        return $this;
    }
} } 