<?php

/**
 * Used in creating options for "Product Attributes display" config value selection
 *
 */
class Movent_Custom_Model_Configurabledisplay
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(        	
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Show Member Login Form Only')),            
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Show Stargenting Form Only')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('Both'))
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(            
            0 => Mage::helper('adminhtml')->__('Show Member Login Form Only'),
            1 => Mage::helper('adminhtml')->__('Show Stargenting Form Only'),
            2 => Mage::helper('adminhtml')->__('Both')
        );
    }

}
