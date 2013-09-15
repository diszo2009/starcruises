<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.4.0_2.0.3_467520
 * Purchase ID: JlNeBKBvSqsIsT7whc80ZpBA38zH86mwW38f4D7p5H
 * Generated:   2013-01-14 03:56:09
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminReportReviewCustomerGrid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ ZsZhaSqghCMDkZar('d884eb7cfc70661c89214313b6371960'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminReportReviewCustomerGrid
    extends Mage_Adminhtml_Block_Report_Review_Customer_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('reports/review_customer_collection')->joinCustomers();

        if (!Mage::helper('aitpermissions')->isShowingAllCustomers())
        {
            $role = Mage::getSingleton('aitpermissions/role');
            
            if ($role->isPermissionsEnabled())
            {
                $collection->getSelect()->joinInner(
                    array('_table_customer' => Mage::getSingleton('core/resource')->getTableName('customer_entity')), 
                    ' _table_customer.entity_id = detail.customer_id ', 
                    array()
                    );

                $collection->addFieldToFilter('website_id', array('in' => $role->getAllowedWebsiteIds()));
            }
        }
        
        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
} } 