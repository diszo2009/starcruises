<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.4.0_2.0.3_467520
 * Purchase ID: JlNeBKBvSqsIsT7whc80ZpBA38zH86mwW38f4D7p5H
 * Generated:   2013-01-14 03:56:09
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminPermissionsEditroles.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ PsPqMYUZqRDyBmMO('c2c94ed552b03c3e692b831395907ee7'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminPermissionsEditroles extends Mage_Adminhtml_Block_Permissions_Editroles
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        $id = $this->getRequest()->getParam('rid');
		$storeCategories = Mage::getResourceModel('aitpermissions/advancedrole_collection')->loadByRoleId($id);
		Mage::register('store_categories', $storeCategories);
        
        $this->addTab('advanced', array(
            'label'     => Mage::helper('aitpermissions')->__('Advanced Permissions'),
            'content' => $this->getLayout()->createBlock('aitpermissions/adminhtml_permissions_tab_advanced')->toHtml()           
        ));

        return $this;
    }
} } 