<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.4.0_2.0.3_467520
 * Purchase ID: JlNeBKBvSqsIsT7whc80ZpBA38zH86mwW38f4D7p5H
 * Generated:   2013-01-14 03:56:09
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminCatalogProductHelperFormGallery.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ VsVieSQkifrgDBet('40c7f1e5f095b6c0841257b852bf3764'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminCatalogProductHelperFormGallery
    extends Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery
{
	public function getElementHtml()
	{
		$html = parent::getElementHtml();

        $role = Mage::getSingleton('aitpermissions/role');

		if ($role->isPermissionsEnabled() && !$role->isAllowedToDelete())
		{
            $html = preg_replace(
                '@cell-remove a-center last"><input([ ]+)type="checkbox"@',
                'cell-remove a-center last"><input disabled="disabled" type="checkbox"',
                $html
            );
		}

        return $html;
	}
} } 