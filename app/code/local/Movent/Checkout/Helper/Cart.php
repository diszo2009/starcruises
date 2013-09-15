<?php

class Movent_Checkout_Helper_Cart extends Mage_Checkout_Helper_Cart
{	
	protected $_payment_methods     = array();
	protected $_payment_method_free = 'free'; 
	protected $_product			    = null;
	
    const PAYMENT_METHOD_CARD   	= 'star_ccsave';
    const PAYMENT_METHOD_POINTS 	= 'checkmo';
	
	public function checkCartItemForbiddenPaymentMethods()
	{  
		$this->hasForbiddenPaymentMethods();
	}
	
	/* Check whether any item in the cart has forbidden payment method uniform with the other exept free */	
	public function hasForbiddenPaymentMethods(Mage_Catalog_Model_Product $product = null,$notify = true)
	{
		$has_forbidden = false; 
		
		//validate cart items payment methods
		foreach ($this->getCartItems() as $item){
			$_product 		 = $item->getProduct();
			$method_validated = $this->validateProductPaymentMethods($_product);
			if($method_validated !== true){	
				if($notify){
					$this->setInvalidPaymentMethodMessage($method_validated);
				}
				$has_forbidden = true; 
				break;
			}
        } 
		
		//Validate product before add to cart
		if(!is_null($product)){ 
			$this->_product   = $product; 
			$method_validated = $this->validateProductPaymentMethods($product);
			if($method_validated !== true){ 
				if($notify){
					$this->setInvalidPaymentMethodMessage($method_validated);
				}
				$has_forbidden = true; 
			} 
		}
 		return $has_forbidden;
	}
	
	public function setInvalidPaymentMethodMessage($payment_method)
	{ 
		if(!is_null($this->_product) && $this->_product instanceof Mage_Catalog_Model_Product){
			if($payment_method == self::PAYMENT_METHOD_CARD){
				$this->getCart()->getCheckoutSession()->setPopupNotice("Would you like to clear your existing cart, to continue to purchase <a href=\"".$this->_product->getProductUrl()."\" target=\"_blank\">".$this->_product->getName()."</a> using Credit Card payment?  <a href=\"".$this->getCartUrl()."\"><br/>No, keep my existing cart</a> or <a href=\"".Mage::getUrl("checkout/cart/clearandadd")."\">Yes, clear my cart to continue</a>");
			}elseif($payment_method == self::PAYMENT_METHOD_POINTS){
				$this->getCart()->getCheckoutSession()->setPopupNotice("Would you like to clear your existing cart, to continue to purchase <a href=\"".$this->_product->getProductUrl()."\" target=\"_blank\">".$this->_product->getName()."</a> using StarGenting points?  <a href=\"".$this->getCartUrl()."\"><br/>No, keep my existing cart</a> or <a href=\"".Mage::getUrl("checkout/cart/clearandadd")."\">Yes, clear my cart to continue</a>");
			}else{
				$this->getCart()->getCheckoutSession()->addNotice('Oooops!');
			}
		}else{
			$this->getCart()->getCheckoutSession()->addNotice("Some of your items are in conflict with payment methods. Please remove conflicting item/s or <a href=\"".Mage::getUrl("checkout/cart/clearandadd")."\"> clear cart to continue</a>");
		}		
	}
	
	/** checks if product hase forbidden items compared to other items */
	protected function validateProductPaymentMethods($_product)
	{	 
		if($product_forbidden_methods =  $_product->getProductPaymentMethods()){ 
			$product_forbidden_methods = explode(',', (string) $product_forbidden_methods);
			foreach($product_forbidden_methods as $forbidden_method){  
				
				/* check if items has uniform forbidden methods and is now free*/
				if($forbidden_method != $this->_payment_method_free){  				
					$this->_payment_methods[$forbidden_method] = $forbidden_method; 					
					if(count($this->_payment_methods) > 1){
						  return $forbidden_method; 
					}
				}
			}
		}
		return true; 
	} 
	
	/* get all current cart items */
	public function getCartItems()
	{
		return $this->getCart()->getItems();
	}
	
	protected function getStoreId()
	{
		return Mage::app()->getStore()->getId();
	} 
}