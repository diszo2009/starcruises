<?php 
/*** 
 * Custom CC Payment Method
 * @date August 29, 2013
 * @Author Movent, Inc. - jerick.duguran@movent.com
 *
 ***/  
 

class Movent_Starpay_Model_Method_Starccsave extends Mage_Payment_Model_Method_Cc
{
    protected $_code          = 'star_ccsave';
    protected $_canSaveCc     =  false;
    protected $_formBlockType = 'starpay/form_starccsave';
    protected $_infoBlockType = 'starpay/info_starccsave';	
	private $_url 			  = null;
	private $_message 		  = null;
	private $_error 		  = false; 
	
	/**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }
	 
	
	 /**
     *  @return	  string Return cancel URL
     */
    public function getCancelReturnUrl()
    {
        return Mage::getUrl('starpay/processing/cancel',array('_secure'=>true));
    }
	
    /**
     *  Return URL for customer response
     *
     *  @return	  string Return customer URL
     */
    public function getNormalReturnUrl()
    {
        return Mage::getUrl('starpay/processing/normal',array('_secure'=>true));
    }
	
    /**
     *  Return URL for automatic response
     *
     *  @return	  string Return automatic URL
     */
    public function getAutomaticReturnUrl()
    {
        return Mage::getUrl('starpay/processing/automatic',array('_secure'=>true));
    }
	
    /**
     *  Return Order Place Redirect URL
     *
     *  @return	  string Order Redirect URL
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('starpay/processing/redirect');
	} 
	
	public function getSystemUrl() 
	{	
	    return $this->_url;
	}
	
	/** Return Payment Gateway parameters
	 *
	 * @return string payment order parameters
	 */
	public function getSystemMessage() 
	{
	    return $this->_message;
	} 
	
	public function getApiRequest()
    {
        return Mage::getSingleton('starpay/api_request');
    }
	
    public function getSystemError() 
	{
	    return $this->_error;
	}
	
	public function getConfig()
	{
		return Mage::getModel("starpay/config")->getStarpayConfig();
	}
	
	public function getCheckoutFormFields()
	{
		$starpay_api = $this->getApiRequest();	
		$starpay_api->setOrder()
                    ->setAutoUrl(Mage::getUrl('starpay/processing/automatic'))
                    ->setNormalUrl(Mage::getUrl('starpay/processing/normal'))
                    ->setCancelUrl(Mage::getUrl('starpay/processing/cancel'))
					->setStarpayConfig($this->getConfig())
					->setCheckout($this->getCheckout());
					
		return $starpay_api->getStarpayCheckoutRequest();
	}
	
	
}
