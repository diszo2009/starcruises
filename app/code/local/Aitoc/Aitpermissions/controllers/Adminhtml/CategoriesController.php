<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.4.0_2.0.3_467520
 * Purchase ID: JlNeBKBvSqsIsT7whc80ZpBA38zH86mwW38f4D7p5H
 * Generated:   2013-01-14 03:56:09
 * File path:   app/code/local/Aitoc/Aitpermissions/controllers/Adminhtml/CategoriesController.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ rsrikSCeiIBamrkE('b729e10589212859dbf45c1976cca4c2'); ?><?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

/*
 * @refactor
 * move to common controller
 */

class Aitoc_Aitpermissions_Adminhtml_CategoriesController extends Mage_Adminhtml_Controller_Action
{
/*
 * @refactor
 * $storeCategories is not really store categories
 * make getCategoryIds method
 */
	protected function _init()
	{
        $id = $this->getRequest()->getParam('rid');
		$storeCategories = Mage::getResourceModel('aitpermissions/advancedrole_collection')->loadByRoleId($id);
		Mage::register('store_categories', $storeCategories);
	}

/*
 * @refactor
 * using block "adminhtml_store_switcher" is not right
 * use smth like "adminhtml_roleedit_categories"
 */
    public function categoriesJsonAction()
    {
        $this->_init();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('aitpermissions/adminhtml_store_switcher')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'), $this->getRequest()->getParam('store')));
    }

/*
 * @refactor
 * seems not used, remove
 */
    public function categoriesAction()
    {
        $this->_init();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('aitpermissions/adminhtml_permissions_tab_advanced')->toHtml()
        );
    }
} } 