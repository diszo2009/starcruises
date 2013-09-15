<?php

class Movent_Starpay_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
	
	public function setCardPostData($card_data)
	{
		$card_data_obj = new Varien_Object();	
		 
		foreach($card_data as $key=>$value){
			$card_data_obj->setData($key,$value); 
		}
		$this->getCheckout()->setPostCardData($card_data_obj);
	}
}