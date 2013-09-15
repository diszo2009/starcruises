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

-- DROP TABLE IF EXISTS {$this->getTable('megamenu_group')};
CREATE TABLE {$this->getTable('megamenu_group')} (
  `megamenu_group_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL default '',
  `position` varchar(255) NOT NULL,
  `sort_order` smallint(6) NOT NULL default '0',
  `status` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`megamenu_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- DROP TABLE IF EXISTS {$this->getTable('megemenu_group_store')};
CREATE TABLE {$this->getTable('megamenu_group_store')} (
    `megamenu_group_id` int(11) unsigned NOT NULL,
    `store_id` smallint(5) unsigned NOT NULL,
    PRIMARY KEY(`megamenu_group_id`, `store_id`),
    CONSTRAINT `FK_megamenu_group_store_group` FOREIGN KEY (`megamenu_group_id`) REFERENCES `{$this->getTable('megamenu_group')}` (`megamenu_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_megamenu_group_store_store` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 