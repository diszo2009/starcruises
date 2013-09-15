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
-- DROP TABLE IF EXISTS {$this->getTable('megamenu')};
CREATE TABLE {$this->getTable('megamenu')} (
  `megamenu_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `parent_id` int(11) NOT NULL default '0',
  `is_group` smallint(6) NOT NULL default '2',
  `width` varchar(255),
  `subitem_width` varchar(255),
  `article` int(11),
  `col` varchar(255),
  `type` varchar(255) NOT NULL,
  `is_content` smallint(6) NOT NULL default '2',
  `show_title` smallint(6) NOT NULL default '1',
  `level` smallint(6) NOT NULL default '0',
  `status` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`megamenu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

                    ");

$installer->endSetup();