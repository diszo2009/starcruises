<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.4.0_2.0.3_467520
 * Purchase ID: JlNeBKBvSqsIsT7whc80ZpBA38zH86mwW38f4D7p5H
 * Generated:   2013-01-14 03:56:09
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminCatalogCategoryEditForm.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ ZsZhaSqghCMDkZar('aa4122dde503aa4933db387251e610d9'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminCatalogCategoryEditForm
    extends Mage_Adminhtml_Block_Catalog_Category_Edit_Form
{
    public function _prepareLayout()
    {
        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled() && !$role->isAllowedToDelete())
        {
            $category = $this->getCategory()->setIsDeleteable(false);
            Mage::unregister('category');
            Mage::register('category', $category);
        }
        
        return parent::_prepareLayout();
    }
} } 