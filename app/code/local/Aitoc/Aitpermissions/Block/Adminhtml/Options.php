<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.4.0_2.0.3_467520
 * Purchase ID: JlNeBKBvSqsIsT7whc80ZpBA38zH86mwW38f4D7p5H
 * Generated:   2013-01-14 03:56:09
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Adminhtml/Options.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ PsPqMYUZqRDyBmMO('71f36ae772f1bff97e663ed533effc69'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Adminhtml_Options extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aitpermissions/options.phtml');
    }

    public function canEditGlobalAttributes()
    {
        return Mage::getModel('aitpermissions/advancedrole')->canEditGlobalAttributes($this->_getRoleId());
    }

    public function canEditOwnProductsOnly()
    {
        return Mage::getModel('aitpermissions/advancedrole')->canEditOwnProductsOnly($this->_getRoleId());
    }

    private function _getRoleId()
    {
        return Mage::app()->getRequest()->getParam('rid');
    }
} } 