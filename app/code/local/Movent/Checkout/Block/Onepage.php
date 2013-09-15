<?php

class Movent_Checkout_Block_Onepage extends Mage_Checkout_Block_Onepage{
	
	public function __construct()
    {  
        parent::__construct();  
		
		//Check items payment method. If conflict found, redirect to cart and notify user
		if($this->helper('checkout/cart')->hasForbiddenPaymentMethods(null,false)){
			Mage::app()->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
			return;
		}
	} 
	
	/** Moved from abstract  class ovvride of starcruise dev */
	public function getCountryHtmlSelect($type)
    {
    	if (Mage::getSingleton('customer/session')->getIsStarGentingUser()) {
        $countryId = $this->getAddress()->getCountryId();
        if (is_null($countryId)) {
            $countryId = Mage::helper('core')->getDefaultCountry();
        }
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[country_id]')
            ->setId($type.':country_id')
            ->setTitle(Mage::helper('checkout')->__('Country'))
            ->setClass('input-right')
            ->setValue($countryId)
            ->setOptions($this->getCountryOptions())
			->setExtraParams('disabled="disabled"');
        if ($type === 'shipping') {
            $select->setExtraParams('onchange="if(window.shipping)shipping.setSameAsBilling(false);"');
        }
		} else {
			$countryId = $this->getAddress()->getCountryId();
        if (is_null($countryId)) {
            $countryId = Mage::helper('core')->getDefaultCountry();
        }
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[country_id]')
            ->setId($type.':country_id')
            ->setTitle(Mage::helper('checkout')->__('Country'))
            ->setClass('validate-select input-right')
            ->setValue($countryId)
            ->setOptions($this->getCountryOptions());
        if ($type === 'shipping') {
            $select->setExtraParams('onchange="if(window.shipping)shipping.setSameAsBilling(false);"');
        }
		}

        return $select->getHtml();
    }


    public function getRegionHtmlSelect($type)
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[region]')
            ->setId($type.':region')
            ->setTitle(Mage::helper('checkout')->__('State/Province'))
            ->setClass('required-entry validate-state input-right')
            ->setValue($this->getAddress()->getRegionId())
            ->setOptions($this->getRegionCollection()->toOptionArray());

        return $select->getHtml();
    }
	
	protected function _getStepCodes()
    {
        return array('login', 'billing', 'heared4us', 'payment', 'review');
    }

}