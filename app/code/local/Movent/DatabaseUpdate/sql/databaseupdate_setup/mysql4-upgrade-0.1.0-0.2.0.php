<?php 
$installer = $this;
$installer->getConnection()->addColumn($this->getTable('sales_flat_quote'), 'heared4us', "TEXT");	
$installer->getConnection()->addColumn($this->getTable('sales_flat_quote'), 'giftaid', "TEXT AFTER `heared4us`"); 	  