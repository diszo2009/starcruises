<?php
/**
 * Api Request
 *
 * @category   Movent
 * @package    Movent_Starpay
 * @copyright  Movent - jerick.duguran@movent.com
 * @name       Movent_Starpay_Model_Api_Request
 */
class Movent_Starpay_Model_Api_Request extends Mage_Payment_Model_Method_Cc
{ 
	protected $_response_request_fields = array();
	protected $_starpay_config = null;
	protected $_order;
	protected $merchant_trans_id = null;
	
	
	public function setStarpayConfig($star_config)
	{
		$this->_starpay_config = $star_config;
		return $this;
	}
	
	public function getOrder()
    {
        if (empty($this->_order)) {
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
            $this->_order = $order;
        }
        return $this->_order;
    }

	
	
	public function getStarpayCheckoutRequest()
	{ 
		$this->_response_request_fields['PAYMENT_METHOD'] 	= $this->_starpay_config->getPaymentmethod();
		$this->_response_request_fields['TRANSACTIONTYPE'] 	= $this->_starpay_config->getTransaction();
		$this->_response_request_fields['MERCHANTID'] 		= $this->_starpay_config->getMerchantId();
		$this->_response_request_fields['MERCHANT_TRANID'] 	= $this->getMerchantTransactionId();
		$this->_response_request_fields['PYMT_IND'] 		= '';
		$this->_response_request_fields['PAYMENT_CRITERIA'] = '';
		$this->_response_request_fields['CURRENCYCODE']		= $this->getCurrencyCode();
		$this->_response_request_fields['AMOUNT'] 			= $this->getAmount();
		$this->_response_request_fields['SIGNATURE'] 		= $this->getSignature();
		$this->_response_request_fields['CUSTNAME'] 		= $this->getCustomerName();
		$this->_response_request_fields['CUSTEMAIL'] 		= $this->getCustomerEmail();
		$this->_response_request_fields['SHOPPER_IP'] 		= $this->getIpAddress();
		$this->_response_request_fields['DESCRIPTION'] 		= '';
		$this->_response_request_fields['RESPONSE_TYPE'] 	= '2'; //no redirect
		$this->_response_request_fields['RETURN_URL'] 		= $this->getNormalUrl();
		$this->_response_request_fields['CARDNO'] 			= $this->getCheckout()->getPostCardData()->getCcNumber();
		$this->_response_request_fields['CARDNAME'] 		= $this->getOrder()->getPayment()->getCcOwner();
		$this->_response_request_fields['CARDTYPE'] 		= $this->getCardType();
		$this->_response_request_fields['EXPIRYMONTH'] 		= $this->getExpiryMonth();
		$this->_response_request_fields['EXPIRYYEAR'] 		= $this->getOrder()->getPayment()->getCcExpYear(); 
		$this->_response_request_fields['CARDCVC'] 			= $this->getCheckout()->getPostCardData()->getCcCId();
		   
		return $this->_response_request_fields;
	} 
	
	protected function getCardType()
	{
		$card_type  = $this->getOrder()->getPayment()->getCcType();		
		$card_types = Mage::getSingleton('starpay/config')->getCcTypes();
		foreach($card_types as $code => $card_info)
		{
			if($code == $card_type){
				$card_type = $card_info['starcode'];break;
			}
		}		
		
		return $card_type;
	}
	
	protected function getMerchantTransactionId()
	{ 
		if(is_null($this->merchant_trans_id)){		
			$microtime 		 		 = floatval(substr((string)microtime(), 1, 8));		
			$this->merchant_trans_id = "OS_".date('mdYHis') . substr($microtime, 2, 3); //create sequence number for transaction id must be unique
		}		
		return $this->merchant_trans_id;
	}
	
	protected function getSignature()
	{
		$append   		 	= "##";		
		//$transaction_id  	= $this->getOrder()->getIncrementId() != '' ?  $this->getOrder()->getIncrementId() : '0';
		$transaction_id  	= '0';
		
		$encrypt = $append . $this->_starpay_config->getMerchantId().
				   $append . $this->_starpay_config->getMerchantPassword().
				   $append . $this->getMerchantTransactionId().
				   $append . $this->getAmount().
				   $append . $transaction_id.
				   $append; 				   
				   
		$credits = array($this->_starpay_config->getStarPayUrl(),$this->_starpay_config->getMerchantId(),$this->_starpay_config->getMerchantPassword());
		$encrypt   = strtoupper($encrypt);
		$signature = sha1($encrypt);
		
		return $signature;
	
	}
	
	public function getAmount()
	{  
		 return number_format($this->getOrder()->getGrandTotal(), 2, '.', '');
	} 
	
	protected function getCustomerName()
	{
		return $this->_order->getCustomerName();
	}
	
	protected function getCustomerEmail()
	{ 
		if (!$customerEmail = $this->_order->getBillingAddress()->getEmail()) {
			$customerEmail = $this->_order->getData('customer_email');
		}
		return $customerEmail;
	}
	
	public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }
	 	
	/**
     *  Return IP Address
     *
     *  @return	  string
     */
	protected function getIpAddress()
	{
        if (isset($_SERVER)) 
	    {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) 
		    {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) 
		    {
                $ip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $ip = getenv('HTTP_CLIENT_IP');
            } else {
                $ip = getenv('REMOTE_ADDR');
            }
        } 
        return $ip; 
	}
	
	protected function getCurrencyCode()
	{
		return $this->getOrder()->getBaseCurrencyCode();	
	}
	
	protected function getExpiryMonth()
	{
		return str_pad($this->getOrder()->getPayment()->getCcExpMonth(), 2, "0", STR_PAD_LEFT);
	}
}
