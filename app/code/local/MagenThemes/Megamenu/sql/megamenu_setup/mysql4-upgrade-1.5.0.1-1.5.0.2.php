<?php
/******************************************************
 * @package Megamenu module for Magento 1.4.x.x and Magento 1.5.x.x
 * @version 1.5.0.4
 * @author http://www.9magentothemes.com
 * @copyright (C) 2011- 9MagentoThemes.Com
 * @license PHP files are GNU/GPL
*******************************************************/
?>
<?php

$installer = $this;

$installer->startSetup();

$installer->run("
                
ALTER TABLE {$this->getTable('megamenu')} ADD COLUMN store_id smallint(5) unsigned NOT NULL default '0';
ALTER TABLE {$this->getTable('megamenu')} ADD CONSTRAINT `FK_megamenu_store` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;

                    ");

$installer->endSetup();