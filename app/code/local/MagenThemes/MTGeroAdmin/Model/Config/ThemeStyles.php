<?php
/*------------------------------------------------------------------------
# APL Solutions and Vision Co., LTD
# ------------------------------------------------------------------------
# Copyright (C) 2008-2010 APL Solutions and Vision Co., LTD. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Author: APL Solutions and Vision Co., LTD
# Websites: http://www.joomlavision.com/ - http://www.magentheme.com/
-------------------------------------------------------------------------*/ 
class MagenThemes_MTGeroAdmin_Model_Config_ThemeStyles
{ 
    public function toOptionArray()
    {        
        return array(
            array('value' => 'red', 'label'=>Mage::helper('adminhtml')->__('Red')),
            array('value' => 'green', 'label'=>Mage::helper('adminhtml')->__('Green')),
            array('value' => 'blue', 'label'=>Mage::helper('adminhtml')->__('Blue')),
        	array('value' => 'custom', 'label'=>Mage::helper('adminhtml')->__('Custom'))
        );
    }
}
