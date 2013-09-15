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
-- DROP TABLE IF EXISTS {$this->getTable('megamenu_relation_group')};
CREATE TABLE {$this->getTable('megamenu_relation_group')} (
    `megamenu_id` int(11) unsigned NOT NULL,
    `megamenu_group_id` int(11) unsigned NOT NULL,
    `sort_order` smallint(6) NOT NULL default '0',
    PRIMARY KEY(`megamenu_id`, `megamenu_group_id`),
    CONSTRAINT `FK_megamenu_relation_group_megamenu` FOREIGN KEY (`megamenu_id`) REFERENCES `{$this->getTable('megamenu')}` (`megamenu_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_megamenu_relation_group_group` FOREIGN KEY (`megamenu_group_id`) REFERENCES `{$this->getTable('megamenu_group')}` (`megamenu_group_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            ");

$installer->endSetup();