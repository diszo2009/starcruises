<?php
/***
 * Window Integration Payment Method
 * @date 2013-09-19
 * @author diszo.sasil@movent.com (Movent Inc.)
 *
 ***/

class Movent_InfinitiumEpayment_Model_Method_Window extends Mage_Payment_Model_Method_Abstract {
	protected $_code = 'epayment_window';
	protected $_canSaveCc = false;
	protected $_formBlockType = 'infinitiumepayment/form_window';
	protected $_infoBlockType = 'infinitiumepayment/info_window';
	protected $_url = null;
	protected $_message = null;
	protected $_error = false;
	
	protected $_isGateway               = true;
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
	public function getWindowReturnUrl() {
		return Mage::getUrl('infinitiumepayment/processing/window', array('_secure' => true));
	}

	
	/**
	 *  Return Order Place Redirect URL
	 *
	 *  @return	  string Order Redirect URL
	 */
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('infinitiumepayment/processing/redirect', array('_secure' => true));
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
		return Mage::getModel("infinitiumepayment/config") -> getInfinitiumEpaymentConfig();
	}

	public function getCheckoutFormFields() {
		$infinitiumepayment_api = $this -> getApiRequest();
		$infinitiumepayment_api -> setReturnUrl($this->getWindowReturnUrl())
								-> setCancelUrl($this->getCancelReturnUrl())
								-> setInfinitiumEpaymentConfig($this -> getConfig()) 
								-> setCheckout($this -> getCheckout());

		return $infinitiumepayment_api -> getInfinitiumEpaymentWindowCheckoutRequest();
	}

}
