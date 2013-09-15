<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.4.0_2.0.3_467520
 * Purchase ID: JlNeBKBvSqsIsT7whc80ZpBA38zH86mwW38f4D7p5H
 * Generated:   2013-01-14 03:56:09
 * File path:   app/code/local/Aitoc/Aitpermissions/Model/Rewrite/CatalogModelResourceEavMysql4CategoryTree.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ asawrYhBwcgZyark('de40c2bed4d65161465e1ee1b429cf9d'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Model_Rewrite_CatalogModelResourceEavMysql4CategoryTree
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree
{
    protected function _updateAnchorProductCount(&$data)
    {
        foreach ($data as $key => $row)
        {
            if (isset($row['is_anchor']) && 0 === (int)$row['is_anchor'])
            {
                $data[$key]['product_count'] = $row['self_product_count'];
            }
        }
    }
} } 