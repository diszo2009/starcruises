<?php
/***
 * Custom CC Payment Method
 * @date August 29, 2013
 * @Author Movent, Inc. - jerick.duguran@movent.com
 *
 ***/

class Movent_InfinitiumEpayment_Model_Method_Direct extends Mage_Payment_Model_Method_Cc {
	protected $_code = 'epayment';
	protected $_canSaveCc = false;
	protected $_formBlockType = 'infinitiumepayment/form_direct';
	protected $_infoBlockType = 'infinitiumepayment/info_direct';
	private $_url = null;
	private $_message = null;
	private $_error = false;

    protected $_isGateway               = false;
    protected $_canAuthorize            = false;	## Auth Only
    protected $_canCapture              = true;	    ## Sale, Capture
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = false;     ## Creation of a transaction from the admin panel
    protected $_canUseCheckout          = true;

	/**
	 * Get checkout session namespace
	 *
	 * @return Mage_Checkout_Model_Session
	 */
	public function getCheckout() {
		return Mage::getSingleton('checkout/session');
	}

	/**
	 * Get current quote
	 *
	 * @return Mage_Sales_Model_Quote
	 */
	public function getQuote() {
		return $this -> getCheckout() -> getQuote();
	}

	/**
	 *  @return	  string Return cancel URL
	 */
	public function getCancelReturnUrl() {
		return Mage::getUrl('infinitiumepayment/processing/cancel', array('_secure' => true));
	}

	/**
	 *  Return URL for customer response
	 *
	 *  @return	  string Return customer URL
	 */
	public function getNormalReturnUrl() {
		return Mage::getUrl('infinitiumepayment/processing/normal', array('_secure' => true));
	}

	/**
	 *  Return URL for automatic response
	 *
	 *  @return	  string Return automatic URL
	 */
	public function getAutomaticReturnUrl() {
		return Mage::getUrl('infinitiumepayment/processing/automatic', array('_secure' => true));
	}

	/**
	 *  Return Order Place Redirect URL
	 *
	 *  @return	  string Order Redirect URL
	 */
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('infinitiumepayment/processing/redirect');
	}

	public function getSystemUrl() {
		return $this -> _url;
	}

	/** Return Payment Gateway parameters
	 *
	 * @return string payment order parameters
	 */
	public function getSystemMessage() {
		return $this -> _message;
	}

	public function getApiRequest() {
		return Mage::getSingleton('infinitiumepayment/api_request');
	}

	public function getSystemError() {
		return $this -> _error;
	}

	public function getConfig() {
		return Mage::getModel("infinitiumepayment/config") -> getinfinitiumepaymentConfig();
	}

	public function getCheckoutFormFields() {
		$infinitiumepayment_api = $this -> getApiRequest();
		$infinitiumepayment_api -> setOrder() 
								-> setAutoUrl(Mage::getUrl('infinitiumepayment/processing/automatic')) 
								-> setNormalUrl(Mage::getUrl('infinitiumepayment/processing/normal')) 
								-> setCancelUrl(Mage::getUrl('infinitiumepayment/processing/cancel')) 
								-> setInfinitiumEpaymentConfig($this -> getConfig()) 
								-> setCheckout($this -> getCheckout());

		return $infinitiumepayment_api -> getInfinitiumEpaymentCheckoutRequest();
	}

}
