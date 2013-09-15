<?php 
class Movent_Checkout_Block_Cart extends Mage_Checkout_Block_Cart
{ 		
	public function __construct()
    { 
        parent::__construct();
		$this->helper('checkout/cart')->checkCartItemForbiddenPaymentMethods(); 
	}
}
