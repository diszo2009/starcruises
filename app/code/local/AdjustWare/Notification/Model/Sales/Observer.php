<?php
/**
 * Product:     Admin Email Notifications for 1.4.x-1.6.1.0 
 * Package:     AdjustWare_Notification_2.2.0_2.1.0_462115
 * Purchase ID: kNDpaTzxfA9cu1lMIvDCAGJ7OTpJlxcSVJoKJewbeD
 * Generated:   2013-01-23 02:01:08
 * File path:   app/code/local/AdjustWare/Notification/Model/Sales/Observer.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Notification')){ oIrwhrwPgyUmWjrO('42578cf487310b553f2a02659794e12b'); ?><?php
class AdjustWare_Notification_Model_Sales_Observer extends Mage_Core_Model_Abstract
{
    public function onControllerFrontInitRouters($observer)
    {
        if(!Mage::registry('aitpagecache_check_14') && Mage::getConfig()->getNode('modules/Aitoc_Aitpagecache/active')==='true')
        {
            if(file_exists(Mage::getBaseDir('magentobooster').DS.'use_cache.ser'))
            {
                Mage::register('aitpagecache_check_14', 1);
            }
            elseif(file_exists(Mage::getBaseDir('app/etc').DS.'use_cache.ser'))
            {
                Mage::register('aitpagecache_check_13', 1);
            }
        }
    }
} } 