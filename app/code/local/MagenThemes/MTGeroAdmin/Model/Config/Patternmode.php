<?php
/*------------------------------------------------------------------------
# APL Solutions and Vision Co., LTD
# ------------------------------------------------------------------------
# Copyright (C) 2008-2010 APL Solutions and Vision Co., LTD. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Author: APL Solutions and Vision Co., LTD
# Websites: http://www.joomlavision.com/ - http://www.magentheme.com/
-------------------------------------------------------------------------*/ 
class MagenThemes_MTGeroAdmin_Model_Config_Patternmode
{

    public function toOptionArray()
    {        
        return array(
            array('value' => '', 'label'=>Mage::helper('adminhtml')->__('-- None --')),
            array('value' => 'pattern_1', 'label'=>Mage::helper('adminhtml')->__('Pattern 1')),
            array('value' => 'pattern_2', 'label'=>Mage::helper('adminhtml')->__('Pattern 2')),
            array('value' => 'pattern_3', 'label'=>Mage::helper('adminhtml')->__('Pattern 3')),
            array('value' => 'pattern_4', 'label'=>Mage::helper('adminhtml')->__('Pattern 4')),
        	array('value' => 'pattern_5', 'label'=>Mage::helper('adminhtml')->__('Pattern 5')),
        	array('value' => 'pattern_6', 'label'=>Mage::helper('adminhtml')->__('Pattern 6')),
        	array('value' => 'pattern_7', 'label'=>Mage::helper('adminhtml')->__('Pattern 7')),
        	array('value' => 'pattern_8', 'label'=>Mage::helper('adminhtml')->__('Pattern 8')),
            array('value' => 'pattern_8', 'label'=>Mage::helper('adminhtml')->__('Pattern 9'))
        );
    }
}
