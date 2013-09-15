<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.4.0_2.0.3_467520
 * Purchase ID: JlNeBKBvSqsIsT7whc80ZpBA38zH86mwW38f4D7p5H
 * Generated:   2013-01-14 03:56:09
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminPollEdit.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ dsdojYNyoukBZejl('1e00fc3848b7abf0bbc391ab66b49076'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminPollEdit extends Mage_Adminhtml_Block_Poll_Edit
{
    protected function _preparelayout()
    {
        parent::_prepareLayout();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            $poll = Mage::registry('poll_data');

            // checking if we have permissions to edit this poll
            $allowedStoreviewIds = $role->getAllowedStoreviewIds();

            if ($allowedStoreviewIds && 
                !array_intersect($poll->getStoreIds(), $allowedStoreviewIds)
                && $poll->getId())
            {
                Mage::app()->getResponse()->setRedirect(Mage::getUrl('*/*'));
            }

            if ($poll->getStoreIds() && is_array($poll->getStoreIds()))
            {
                foreach ($poll->getStoreIds() as $pollStoreId)
                {
                    if (!in_array($pollStoreId, $allowedStoreviewIds))
                    {
                        $this->_removeButton('delete');
                        break 1;
                    }
                }
            }
        }

        return $this;
    }
} } 