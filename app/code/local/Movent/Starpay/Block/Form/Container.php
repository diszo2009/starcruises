<?php 

class Movent_Starpay_Block_Form_Container extends Mage_Payment_Block_Form_Container
{     
    public function getFlag()
    {
    	return Mage::getSingleton('customer/session')->getIsStarGentingUser();
    }
}
