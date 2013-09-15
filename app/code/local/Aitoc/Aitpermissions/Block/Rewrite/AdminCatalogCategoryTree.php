<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.4.0_2.0.3_467520
 * Purchase ID: JlNeBKBvSqsIsT7whc80ZpBA38zH86mwW38f4D7p5H
 * Generated:   2013-01-14 03:56:09
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminCatalogCategoryTree.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ WsWhgSTahQZmeMgV('958bf383109d53947083297fd991a808'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminCatalogCategoryTree
    extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    public function getCategoryCollection()
    {
        $collection = parent::getCategoryCollection();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            $allowedCategoryIds = array();

            foreach ($role->getAllowedCategoryIds() as $allowedCategoryId)
            {
                $category = Mage::getModel('catalog/category')->load($allowedCategoryId);
                $categoryPath = $category->getPath();
                $categoryPathIds = explode('/', $categoryPath);

                $allowedCategoryIds = array_merge($allowedCategoryIds, $categoryPathIds);
            }

            if (!empty($allowedCategoryIds))
            {
                $collection->addIdFilter($allowedCategoryIds);
                $this->setData('category_collection', $collection);
            }
        }

        return $collection;
    }

    public function getMoveUrlPattern()
    {
        return $this->getUrl('*/catalog_category/move', array('store' => ':store'));
    }
} } 