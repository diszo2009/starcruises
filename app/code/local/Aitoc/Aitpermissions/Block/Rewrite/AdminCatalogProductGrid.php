<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.4.0_2.0.3_467520
 * Purchase ID: JlNeBKBvSqsIsT7whc80ZpBA38zH86mwW38f4D7p5H
 * Generated:   2013-01-14 03:56:09
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminCatalogProductGrid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ OsOwBYRrwNaMjgBd('8fc6dab3fb73eb6e430e31cd53002f9a'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminCatalogProductGrid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $role = Mage::getSingleton('aitpermissions/role');

        if (!$role->isPermissionsEnabled() || $role->isAllowedToDelete())
        {
            $this->getMassactionBlock()->addItem('delete', array(
                'label' => Mage::helper('catalog')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('catalog')->__('Are you sure?')
            ));
        }

        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('catalog')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('catalog')->__('Status'),
                    'values' => $statuses
                )
            )
        ));

        $this->getMassactionBlock()->addItem('attributes', array(
            'label' => Mage::helper('catalog')->__('Update attributes'),
            'url' => $this->getUrl('*/catalog_product_action_attribute/edit', array('_current' => true))
        ));

        return $this;
    }
    
    protected function _toHtml()
    {
        $allowedWebisteIds = Mage::getSingleton('aitpermissions/role')->getAllowedWebsiteIds();
        if (count($allowedWebisteIds) <= 1)
        {
            unset($this->_columns['websites']);
        }
        return parent::_toHtml();
    }
} } 