<?php

class Inchoo_Heared4us_Block_Onepage_Heared4us extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {    	
  /*      $this->getCheckout()->setStepData('heared4us', array(
        'label'     => Mage::helper('checkout')->__('How did you hear about us?'),
        'is_show'   => true
	));
        
        parent::_construct();
        */
        
        $this->getCheckout()->setStepData('heared4us', array(
            'label'     => Mage::helper('checkout')->__('Pick Up Location'),
            'is_show'   => $this->isShow()
        ));

        parent::_construct();
    }
}