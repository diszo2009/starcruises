<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
include_once("Mage/Checkout/controllers/OnepageController.php");  
class MagenThemes_Checkout_OnepageController extends Mage_Checkout_OnepageController
{
    protected $_sectionUpdateFunctions = array(
        'payment-method'  => '_getPaymentMethodsHtml',
        'shipping-method' => '_getShippingMethodsHtml',
        'review'          => '_getReviewHtml',
    );
    
    /** @var Mage_Sales_Model_Order */
    protected $_order;

    /**
     * @return Mage_Checkout_OnepageController
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_preDispatchValidateCustomer();

        $checkoutSessionQuote = Mage::getSingleton('checkout/session')->getQuote();
        if ($checkoutSessionQuote->getIsMultiShipping()) {
            $checkoutSessionQuote->setIsMultiShipping(false);
            $checkoutSessionQuote->removeAllAddresses();
        }

        if(!$this->_canShowForUnregisteredUsers()){
            $this->norouteAction();
            $this->setFlag('',self::FLAG_NO_DISPATCH,true);
            return;
        }

        return $this;
    }

    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
        return $this;
    }

    /**
     * Validate ajax request and redirect on failure
     *
     * @return bool
     */
    protected function _expireAjax()
    {
        if (!$this->getOnepage()->getQuote()->hasItems()
            || $this->getOnepage()->getQuote()->getHasError()
            || $this->getOnepage()->getQuote()->getIsMultiShipping()) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        $action = $this->getRequest()->getActionName();
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
            && !in_array($action, array('index', 'progress'))) {
            $this->_ajaxRedirectResponse();
            return true;
        }

        return false;
    }

    /**
     * Get shipping method step html
     *
     * @return string
     */
    protected function _getShippingMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_shippingmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    /**
     * Get payment method step html
     *
     * @return string
     */
    protected function _getPaymentMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_paymentmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    protected function _getAdditionalHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_additional');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        Mage::getSingleton('core/translate_inline')->processResponseBody($output);
        return $output;
    }

    /**
     * Get order review step html
     *
     * @return string
     */
    protected function _getReviewHtml()
    {
        return $this->getLayout()->getBlock('root')->toHtml();
    }

    /**
     * Get one page checkout model
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    /**
     * Checkout page
     */
    public function indexAction()
    {
        if (!Mage::helper('checkout')->canOnepageCheckout()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));
            $this->_redirect('checkout/cart');
            return;
        }
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message') ?
                Mage::getStoreConfig('sales/minimum_order/error_message') :
                Mage::helper('checkout')->__('Subtotal must exceed minimum order amount');

            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure'=>true)));
        $this->getOnepage()->initCheckout();
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Checkout'));
        $this->renderLayout();
    }

    /**
     * Checkout status block
     */
    public function progressAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function shippingMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function reviewAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Order success action
     */
    public function successAction()
    {
        $session = $this->getOnepage()->getCheckout();
        if (!$session->getLastSuccessQuoteId()) {
            $this->_redirect('checkout/cart');
            return;
        }

        $lastQuoteId = $session->getLastQuoteId();
        $lastOrderId = $session->getLastOrderId();
        $lastRecurringProfiles = $session->getLastRecurringProfileIds();
        if (!$lastQuoteId || (!$lastOrderId && empty($lastRecurringProfiles))) {
            $this->_redirect('checkout/cart');
            return;
        }

        $session->clear();
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
        $this->renderLayout();
    }

    public function failureAction()
    {
        $lastQuoteId = $this->getOnepage()->getCheckout()->getLastQuoteId();
        $lastOrderId = $this->getOnepage()->getCheckout()->getLastOrderId();

        if (!$lastQuoteId || !$lastOrderId) {
            $this->_redirect('checkout/cart');
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }


    public function getAdditionalAction()
    {
        $this->getResponse()->setBody($this->_getAdditionalHtml());
    }

    /**
     * Address JSON
     */
    public function getAddressAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $addressId = $this->getRequest()->getParam('address', false);
        if ($addressId) {
            $address = $this->getOnepage()->getAddress($addressId);

            if (Mage::getSingleton('customer/session')->getCustomer()->getId() == $address->getCustomerId()) {
                $this->getResponse()->setHeader('Content-type', 'application/x-json');
                $this->getResponse()->setBody($address->toJson());
            } else {
                $this->getResponse()->setHeader('HTTP/1.1','403 Forbidden');
            }
        }
    }

    /**
     * Save checkout method
     */
    public function saveMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $method = $this->getRequest()->getPost('method');
            $result = $this->getOnepage()->saveCheckoutMethod($method);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * save checkout billing address
     */
    public function saveBillingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
//            $postData = $this->getRequest()->getPost('billing', array());
//            $data = $this->_filterPostData($postData);
            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                /* check quote for virtual */
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    $result['goto_section'] = 'payment';
                }
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Shipping address save action
     */
    public function saveShippingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Shipping method save action
     */
    public function saveShippingMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $result = $this->getOnepage()->saveShippingMethod('flatrate_flatrate');
       // if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($data);
            /*
            $result will have erro data if shipping method is empty
            */
            if(!$result) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
                        array('request'=>$this->getRequest(),
                            'quote'=>$this->getOnepage()->getQuote()));
                $this->getOnepage()->getQuote()->collectTotals();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

                $result['goto_section'] = 'payment';
                $result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
                );
            }
            $this->getOnepage()->getQuote()->collectTotals()->save();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
       // }
    }

    /**
     * Save payment ajax action
     *
     * Sets either redirect or a JSON response
     */
    public function savePaymentAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        try {
            if (!$this->getRequest()->isPost()) {
                $this->_ajaxRedirectResponse();
                return;
            }

            // set payment to quote
            $result = array();
            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->getOnepage()->savePayment($data);
            
            // get section and redirect data
            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (empty($result['error']) && !$redirectUrl) {
                $this->loadLayout('checkout_onepage_review');
                $result['goto_section'] = 'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
                );
            }
            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }
        } catch (Mage_Payment_Exception $e) {
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $this->__('Unable to set Payment Method.');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Get Order by quoteId
     *
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder()
    {
        if (is_null($this->_order)) {
            $this->_order = Mage::getModel('sales/order')->load($this->getOnepage()->getQuote()->getId(), 'quote_id');
            if (!$this->_order->getId()) {
                throw new Mage_Payment_Model_Info_Exception(Mage::helper('core')->__("Can not create invoice. Order was not found."));
            }
        }
        return $this->_order;
    }

    /**
     * Create invoice
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    protected function _initInvoice()
    {
        $items = array();
        foreach ($this->_getOrder()->getAllItems() as $item) {
            $items[$item->getId()] = $item->getQtyOrdered();
        }
        /* @var $invoice Mage_Sales_Model_Service_Order */
        $invoice = Mage::getModel('sales/service_order', $this->_getOrder())->prepareInvoice($items);
        $invoice->setEmailSent(true)->register();

        Mage::register('current_invoice', $invoice);
        return $invoice;
    }

    /**
     * Create order action
     */
    public function saveOrderAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
		
        $result = array();
        try {
            if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = $this->__('Please agree to all the terms and conditions before placing the order.');
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
            }
            if ($data = $this->getRequest()->getPost('payment', false)) {
                $this->getOnepage()->getQuote()->getPayment()->importData($data);
            }
            Mage::throwException(Mage::helper('checkout')->__("TEST"));
    	        	return;
            $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            $login = Mage::getSingleton('customer/session')->getLogin();
            $DRSFlag = Mage::getSingleton('customer/session')->getIsStarGentingUser();
            $paymentMethod = $this->getOnepage()->getQuote()->getPayment()->getMethod();
            if($DRSFlag)
            {
            	if($paymentMethod == "checkmo") {
            		$response = $this->deductPoints($login);
	            	if (!$response["OUTPUTPARAMS"]) {
    	        		$result['success'] = false;
	        	        $result['error'] = true;
    	        	    $result['error_messages'] = $this->__('Insufficient points.');
        	        	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	        	        return;
    	        	}
    	        }
    	        
    	        if($paymentMethod == "ccsave") {
    	        	//Mage::throwException(Mage::helper('checkout')->__($data['cc_owner']));
    	        	//return;
    	        	//$this->setCcInfo($data['cc_owner'],$data['cc_type'],$data['cc_number'],$data['cc_exp_month'],$data['cc_exp_year'],$data['cc_cid']);
					$this->useCreditCard();
    	        }
            } else {
            	$this->useCreditCard();
            	//Mage::throwException(Mage::helper('checkout')->__($data['cc_owner']));
    	        //return;
            }
            $this->getOnepage()->saveOrder();

            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
            $result['success'] = true;
            $result['error']   = false;
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $message = $e->getMessage();
            if( !empty($message) ) {
                $result['error_messages'] = $message;
            }
            $result['goto_section'] = 'payment';
            $result['update_section'] = array(
                'name' => 'payment-method',
                'html' => $this->_getPaymentMethodsHtml()
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $e->getMessage();

            if ($gotoSection = $this->getOnepage()->getCheckout()->getGotoSection()) {
                $result['goto_section'] = $gotoSection;
                $this->getOnepage()->getCheckout()->setGotoSection(null);
            }

            if ($updateSection = $this->getOnepage()->getCheckout()->getUpdateSection()) {
                if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                    $result['update_section'] = array(
                        'name' => $updateSection,
                        'html' => $this->$updateSectionFunction()
                    );
                }
                $this->getOnepage()->getCheckout()->setUpdateSection(null);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success']  = false;
            $result['error']    = true;
            $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
        }
        $this->getOnepage()->getQuote()->save();
        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('dob'));
        return $data;
    }

    /**
     * Check can page show for unregistered users
     *
     * @return boolean
     */
    protected function _canShowForUnregisteredUsers()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn()
            || $this->getRequest()->getActionName() == 'index'
            || Mage::helper('checkout')->isAllowedGuestCheckout($this->getOnepage()->getQuote())
            || !Mage::helper('checkout')->isCustomerMustBeLogged();
    }
    
    public function deductPoints($login) {
    	//$url = "http://222.127.99.72/DRSAPI/Service.asmx?wsdl"; //prod
		$url = "http://10.236.9.121/DRSAPI2/Service.asmx?wsdl"; //dev
		$soap = new soapclient($url);
		$request = new stdClass();
		$request->paraDrsID = "solution";
		$request->paraDrsPwd = "solution";
		$request->paraCid = $login['username'];
		$request->paraCashToAdjust = -2;
		$request->paraCashTypeToAdjust = 0;
		$request->paraCurrCode = "HK";
		$request->paraProfitCenter = "7SHW";
		$request->paraRemark = "Deduct Points";
		$result = $soap->API_AutoUA_CEA_Currency($request);
		//var_dump($soap->__getFunctions());
		$result = $this->XMLtoArray($result->API_AutoUA_CEA_CurrencyResult->any);
		//echo $result["OUTPUTPARAMS"]["EMAILADDRESS"];
		//echo $result["ERR"]["CUSTOMERID"];
		return $result;
    }
    
    public function XMLtoArray($XML)
	{
    	$xml_parser = xml_parser_create();
    	xml_parse_into_struct($xml_parser, $XML, $vals);
	    xml_parser_free($xml_parser);
	    $_tmp='';
	    foreach ($vals as $xml_elem) {
    	    $x_tag=$xml_elem['tag'];
        	$x_level=$xml_elem['level'];
	        $x_type=$xml_elem['type'];
    	    if ($x_level!=1 && $x_type == 'close') {
        	    if (isset($multi_key[$x_tag][$x_level]))
            	    $multi_key[$x_tag][$x_level]=1;
	            else
    	            $multi_key[$x_tag][$x_level]=0;
        	}
	        if ($x_level!=1 && $x_type == 'complete') {
    	        if ($_tmp==$x_tag)
        	        $multi_key[$x_tag][$x_level]=1;
        	    $_tmp=$x_tag;
	        }
    	}

    	foreach ($vals as $xml_elem) {
        	$x_tag=$xml_elem['tag'];
	        $x_level=$xml_elem['level'];
    	    $x_type=$xml_elem['type'];
        	if ($x_type == 'open')
            	$level[$x_level] = $x_tag;
	        $start_level = 1;
    	    $php_stmt = '$xml_array';
        	if ($x_type=='close' && $x_level!=1)
            	$multi_key[$x_tag][$x_level]++;
	        while ($start_level < $x_level) {
    	        $php_stmt .= '[$level['.$start_level.']]';
        	    if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
            	    $php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';
	            $start_level++;
    	    }
        	$add='';
	        if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type=='open' || $x_type=='complete')) {
    	        if (!isset($multi_key2[$x_tag][$x_level]))
        	        $multi_key2[$x_tag][$x_level]=0;
            	else
                	$multi_key2[$x_tag][$x_level]++;
	            $add='['.$multi_key2[$x_tag][$x_level].']';
    	    }
        	if (isset($xml_elem['value']) && trim($xml_elem['value'])!='' && !array_key_exists('attributes', $xml_elem)) {
	            if ($x_type == 'open')
    	            $php_stmt_main=$php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
        	    else
            	    $php_stmt_main=$php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';
	            eval($php_stmt_main);
    	    }
        	if (array_key_exists('attributes', $xml_elem)) {
            	if (isset($xml_elem['value'])) {
                	$php_stmt_main=$php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
    	            eval($php_stmt_main);
	            }
        	    foreach ($xml_elem['attributes'] as $key=>$value) {
            	    $php_stmt_att=$php_stmt.'[$x_tag]'.$add.'[$key] = $value;';
                	eval($php_stmt_att);
	            }
    	    }
	    }
    	//var_dump( $xml_array["OUTPUTPARAMS"]["EMAILADDRESS"]);
    	return $xml_array;
	}
	
	public function useCreditCard() {
		//credit card integration
		$PASSWORD = "IRFMS";
		//credit card
		$PAYMENT_METHOD = 1; 
		//sales
		$TRANSACTIONTYPE = 1; 
		$MERCHANTID = "SCM_DEV";
		$date = new DateTime();
		$MERCHANT_TRANID = "SC_" . $date->getTimestamp(); //create sequence number for transaction id must be unique
		$TRANSACTIONID = '0';
		$CURRENCYCODE = "HKD";
		$AMOUNT = "1.00";
		$CUSTNAME = "TEST";
		$CUSTEMAIL = "test@yahoo.com";
		$SHOPPER_IP = $_SERVER[SERVER_ADDR];
		//for redirect
		//$RESPONSE_TYPE = "2"; 
		//$RETURN_URL = "www.google.com";

		// no redirect
		$RESPONSE_TYPE = "2"; 
		$RETURN_URL = "http://ec2-107-20-112-213.compute-1.amazonaws.com/starcruise/index.php/checkout/onepage/success/";
		$CARDNO = "5426340900064101";
		$CARDNAME= "starcruise";
		// Visa
		$CARDTYPE= "M";
		// MM
		$EXPIRYMONTH= "01";
		// YYYY
		$EXPIRYYEAR= "2030"; 
		$CARDCVC= "123";
		$SHARP = "##";
		$ENCRYPT = $SHARP.$MERCHANTID.$SHARP.$PASSWORD.$SHARP.$MERCHANT_TRANID.$SHARP.$AMOUNT.$SHARP.$TRANSACTIONID.$SHARP;
		//echo $ENCRYPT."              ";
		$SIGNATURE= strtoupper(sha1($ENCRYPT));
		//echo $ENCRYPT."    |   ".$SIGNATURE;
		$url = 'https://epgdev.starcruises.com/payment/PaymentInterface.jsp';
		//$url = 'https://dvlp.infinitium.com/payment/PaymentInterface.jsp';
		$fields = array(
			'PAYMENT_METHOD'=>$PAYMENT_METHOD,
			'TRANSACTIONTYPE'=>$TRANSACTIONTYPE,
            'MERCHANTID' => $MERCHANTID,
            'MERCHANT_TRANID' => $MERCHANT_TRANID,
            'PYMT_IND' => '',
            'PAYMENT_CRITERIA' => '',
            'CURRENCYCODE' => $CURRENCYCODE,
            'AMOUNT' => floatval($AMOUNT),
            'SIGNATURE' => $SIGNATURE,   
            'CUSTNAME' => $CUSTNAME,
            'CUSTEMAIL' => $CUSTEMAIL,
            'SHOPPER_IP' => $SHOPPER_IP,
            'DESCRIPTION' => '',
            'RESPONSE_TYPE' => $RESPONSE_TYPE,
            'RETURN_URL' => $RETURN_URL,
            'CARDNO' => $CARDNO,
            'CARDNAME' => $CARDNAME,
            'CARDTYPE' => $CARDTYPE,
            'EXPIRYMONTH' => $EXPIRYMONTH,
            'EXPIRYYEAR' => $EXPIRYYEAR,
            'CARDCVC' => $CARDCVC
        );

		//url-ify the data for the POST
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			rtrim($fields_string, '&');

		$param = rtrim(($url.'?'.$fields_string),'&');

		header('Location: ' . $param);
	}
}
