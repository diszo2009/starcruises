<?php

require_once 'Mage/Checkout/controllers/OnepageController.php';

class Inchoo_Heared4us_OnepageController extends Mage_Checkout_OnepageController
{
    public function doSomestuffAction()
    {
		if(true) {
			$result['update_section'] = array(
            	'name' => 'payment-method',
                'html' => $this->_getPaymentMethodsHtml()
			);					
		}
    	else {
			$result['goto_section'] = 'shipping';
		}		
    }    

/*    public function savePaymentAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('payment', array());

            try {
                $result = $this->getOnepage()->savePayment($data);
            }
            catch (Mage_Payment_Exception $e) {
                if ($e->getFields()) {
                    $result['fields'] = $e->getFields();
                }
                $result['error'] = $e->getMessage();
            }
            catch (Exception $e) {
                $result['error'] = $e->getMessage();
            }
            $redirectUrl = $this->getOnePage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (empty($result['error']) && !$redirectUrl) {
				$this->loadLayout('checkout_onepage_heared4us');

                $result['goto_section'] = 'heared4us';
            }

            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }
*/

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
            Mage::getSingleton('core/session')->setEmail($data);
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                /* check quote for virtual */
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                	$this->loadLayout('checkout_onepage_heared4us');
                    $result['goto_section'] = 'heared4us';
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    $this->loadLayout('checkout_onepage_heared4us');
                    $result['goto_section'] = 'heared4us';
                } else {
                    $this->loadLayout('checkout_onepage_heared4us');
                    $result['goto_section'] = 'heared4us';
                }
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    public function saveHeared4usAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $pickupData = '';
        	//Grab the submited value heared for us value
        	$_pickupCounter = $this->getRequest()->getPost('pickup-counter');
						
			$offset = Mage::getStoreConfig('custom/cruise_pickup/days_count');
			if($offset == ""){
				// Date should plus 3 days from current date
				$currDate = date("m/d/Y",strtotime("+1 days"));
			}else{
				$currDate = date("m/d/Y",strtotime("+".$offset." days"));
			}
						
			
			$result = array();
			$pickupData = array();
        	for($i = 1; $i <= $_pickupCounter; $i++) {
        		$_pickupLocation = $this->getRequest()->getPost('pickup-location-'.$i);
        		$_cruiseDate = $this->getRequest()->getPost('cruise-date-'.$i);

				$locType = Mage::getSingleton('custom/pickupoptions')->getStoreAttributeLocationType($_pickupLocation);	
				$_pickupLocationVal = Mage::getSingleton('custom/pickupoptions')->getStoreLocationByValue($_pickupLocation);
								
				if($locType != false && $locType == Movent_Custom_Model_Pickupoptions::LOCATION_TYPE_CRUISE){
					$storeType = "Cruise";	
				}
				else{
					$storeType = "Pickup";
				}

        			
				$pickupData[] = array('cruise_date' => $_cruiseDate,
									 'pickup_location'=>$_pickupLocationVal['label'],
									 'store_type' => $storeType );
				
				/*
				if($i == 1) {
        			$pickupData .= "['CruiseDate']".$_cruiseDate . ";" . "['PickupLocation']".$_pickupLocationVal['label'];
        		} else {
        			$pickupData .= ":" . "['CruiseDate']".$_cruiseDate . ";" . "['PickupLocation']".$_pickupLocationVal['label'];
        		}
				*/				
				
				if( !(strtotime($_cruiseDate) >= strtotime($currDate))){					
					$result['message'] = Mage::helper('checkout')->__($storeType.' date value for "'.$_pickupLocationVal['label'].'" is invalid.');
					$result['error'] = true;
					echo Zend_Json::encode($result);
					exit;
				}
				
        	}
			
			Mage::getSingleton('core/session')->setInchooHeared4us(serialize($pickupData));
			Mage::getSingleton('core/session')->setPickupCounter($_pickupCounter);
			
            $this->getOnepage()->saveHeared4Us();
            //$redirectUrl = $this->getOnePage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (!$result) {
                $this->loadLayout('checkout_onepage_payment');

                $result['goto_section'] = 'payment';
                $result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
                );

            }

            /*if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }*/

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
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
            $paymentMethod = $this->getOnepage()->getQuote()->getPayment()->getMethod();
			Mage::getSingleton('checkout/session')->setPaymentMethod($paymentMethod);
			if ($paymentMethod == "checkmo") {
				if (Mage::getSingleton('checkout/session')->getTotalPoints > Mage::getSingleton('customer/session')->getCustomerPoints()) {
					Mage::throwException(Mage::helper('checkout')->__('Insufficient Points. Please choose Pay by Credit Card to proceed with purchase.'));
                    return;
				}
			}
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
    
    public function paymentReturnAction() {
    	if ($_POST['ERR_DESC'] == 'No error' && $_POST['USR_CODE'] == '101' && $_POST['BANK_RES_CODE'] && '00') {
    		$this->getResponse()->setRedirect(Mage::getBaseUrl() . "checkout/onepage/success");
			Mage::getSingleton('core/session')->addSuccess(Mage::helper('core')->__('Your transaction ID is '.$_POST['MERCHANT_TRANID']));
			//Mage::getSingleton('core/session')->addSuccess(Mage::helper('core')->__($_POST['TRANSACTIONID']));
			Mage::getSingleton('core/session')->setTxnId($_POST['MERCHANT_TRANID']);
    	} else {
    		$session = Mage::getSingleton('checkout/session');
    		if ($session->getLastRealOrderId()) {
    			$order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
    			if ($order->getId()) {
    				
					
					$_heared4us_data = null;
    				$_heared4us_data = Mage::getSingleton('core/session')->getInchooHeared4us();
					$string = "";
					
					// Modified By: Movent (2013-05-16)
					
					if(!preg_match('/Txn/',$_heared4us_data)){						
						$arrData = unserialize($_heared4us_data);			
						if(count($arrData)>0){		
							foreach($arrData as $row){					
								$string .= $row['pickup_location']."<br>";
								$string .= $row['store_type']." Date: ".$row['cruise_date']."<br><br>";
							}
						}
					}
					
					/*
    				//Save fhc id to order obcject
    				$pickupData = explode(":", $_heared4us_data);
    				$txn = end($pickupData);
    				foreach ($pickupData as $cruiseDate) {
    					if(preg_match('/Txn/',$cruiseDate)) {
    				
    					} else {
    						//print_r($cruiseDate);
    						$string = substr($cruiseDate,43);
    						$string .= "<br>";
    						$string .= "Cruise Date: " . substr($cruiseDate,14,10) . "<br>";
    						//var_dump($string);
    					}
    				}*/
					
					
					
					
    				$order->setData('giftaid', $string . "Transaction ID: ". $_POST['MERCHANT_TRANID']);
    				$order->setData('heared4us', $_heared4us_data . ":['Txn']". $_txn_id);
    				Mage::getSingleton('core/session')->unsTxnId();
    				$order->save();
    				$order->cancel()->save();
    				//$order->sendNewOrderEmail();
    			}
    		}
    		Mage::getSingleton('checkout/session')->addError($_POST['USR_MSG'] . " Your transaction id is " . $_POST['MERCHANT_TRANID'] . ".");
    		Mage::getSingleton('checkout/session')->addError("Your order has been cancelled.");
    		####################################
    		$session = Mage::getSingleton('checkout/session');
    		$cart = Mage::getSingleton('checkout/cart');
    		
    		$items = $order->getItemsCollection();
    		foreach ($items as $item) {
    			try {
    				$cart->addOrderItem($item,$item->getQty());
    			}
    			catch (Mage_Core_Exception $e){
    				if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
    					Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
    				}
    				else {
    					Mage::getSingleton('checkout/session')->addError($e->getMessage());
    				}
    			}
    			catch (Exception $e) {
    				Mage::getSingleton('checkout/session')->addException($e,
    				Mage::helper('checkout')->__('Cannot add the item to shopping cart.')
    				);
    			}
    		}
    		
    		$cart->save();
    		####################################
    		$this->_redirect('checkout/cart');
    	}
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
            
            $bill = Mage::getSingleton('core/session')->getEmail();
    		
            $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId()+1;
            
            $login = Mage::getSingleton('customer/session')->getLogin();
            $DRSFlag = Mage::getSingleton('customer/session')->getIsStarGentingUser();
            $paymentMethod = $this->getOnepage()->getQuote()->getPayment()->getMethod();
            $totalPoints = Mage::getSingleton('checkout/session')->getTotalPoints();
            $grandTotal = Mage::getSingleton('checkout/session')->getGrandTotal();
            $_exp_month = ($data['cc_exp_month'] < 10) ? '0'.$data['cc_exp_month'] : $data['cc_exp_month'];
           // Mage::throwException(Mage::helper('checkout')->__($grandTotal));
    	    //return;
            if($DRSFlag)
            {
            	if($paymentMethod == "checkmo") {
            		$response = $this->deductPoints($login, $totalPoints);
					//Mage::throwException(Mage::helper('checkout')->__($response["ERR"]));
    	    		//return;
	            	if ($response["ERR"]) {
    	        		$result['success'] = false;
	        	        $result['error'] = true;
    	        	    $result['error_messages'] = $this->__($response["ERR"]["ERRMSG"]);
        	        	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	        	        return;
    	        	}
    	        }
    	        
    	        if($paymentMethod == "ccsave") {
    	        	$toPayment = $this->useCreditCard($data['cc_owner'],$data['cc_type'],$data['cc_number'],$_exp_month,$data['cc_exp_year'],$data['cc_cid'], $grandTotal,$orderId,$bill);
    	        	//Mage::throwException(Mage::helper('checkout')->__(Mage::getSingleton('checkout/session')->getPaymentMethod()));
    	        	//return;
    	        	//$this->setCcInfo($data['cc_owner'],$data['cc_type'],$data['cc_number'],$data['cc_exp_month'],$data['cc_exp_year'],$data['cc_cid']);
            		//$response = $this->useCreditCard($data['cc_owner'],$data['cc_type'],$data['cc_number'],$_exp_month,$data['cc_exp_year'],$data['cc_cid']);
    	        }
            } else {
            	$toPayment = $this->useCreditCard($data['cc_owner'],$data['cc_type'],$data['cc_number'],$_exp_month,$data['cc_exp_year'],$data['cc_cid'],$grandTotal,$orderId,$bill);
            	//$res = $this->useCreditCard($data['cc_owner'],$data['cc_type'],$data['cc_number'],$_exp_month,$data['cc_exp_year'],$data['cc_cid']);
            	//$this->getResponse()->setRedirect($res);
            	//$response = $this->useCreditCard($data['cc_owner'],$data['cc_type'],$data['cc_number'],$_exp_month,$data['cc_exp_year'],$data['cc_cid']);
            	//$response = $this->useCreditCard($data['cc_owner'],$data['cc_type'],$data['cc_number'],$_exp_month,$data['cc_exp_year'],$data['cc_cid'],);
    	        //Mage::throwException(Mage::helper('checkout')->__($toPayment));
    	        //return;
            }
			
            $this->getOnepage()->saveOrder();
 		  	if (isset($toPayment)) {
            	$redirectUrl = $toPayment;
            	$this->getOnepage()->getCheckout()->setRedirectUrl($toPayment);
            } else {
            	$redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
            	$result['success'] = true;
            	$result['error']   = false;
            }
			
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
        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        } 
        $this->getOnepage()->getQuote()->save();
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    public function deductPoints($login, $totalPoints) {
    	
		$DEDUCTCURRENCYCODE = Mage::app()->getStore()->getCurrentCurrencyCode();
		//$DEDUCTION = substr($DEDUCTION,0,2);
		
		if($DEDUCTCURRENCYCODE == 'HKD'){
			$DEDUCTCODE = 'HK';}
		elseif($DEDUCTCURRENCYCODE == 'SGD'){
			$DEDUCTCODE = 'SD';}
		elseif($DEDUCTCURRENCYCODE == 'MYR'){
			$DEDUCTCODE = 'RM';}
		elseif($DEDUCTCURRENCYCODE == 'TWD') {
			$DEDUCTCODE = 'NT'; }
		else{
			$DEDUCTCODE = 'HK'; }
	
    	$url = "http://10.236.9.156/DRS_XML/Service.asmx?wsdl"; //prod
        //$url = "http://10.236.9.121/DRSAPI2/Service.asmx?wsdl"; //dev 
		$soap = new soapclient($url);
		$request = new stdClass();
		$request->paraDrsID = "solution";
		$request->paraDrsPwd = "solution";
		$request->paraCid = $login['username'];
		$request->paraCashToAdjust = -$totalPoints;
		$request->paraCashTypeToAdjust = 0;
		//$request->paraCurrCode = "HK";
		$request->paraCurrCode = $DEDUCTCODE;
		//$request->paraProfitCenter = "7SHW"; //dev
		$request->paraProfitCenter = "B2CA";
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
		
	public function useCreditCard($cc_owner,$cc_type,$cc_number,$_exp_month,$cc_exp_year,$cc_cid, $grandTotal, $orderId,$bill) {
		//credit card integration
		//$PASSWORD = "IRFMS"; //dev
	    //$PASSWORD = "QBBVB"; //live B2C
		$PASSWORD = "gbdww"; //prod B2B2C
		//credit card
		$PAYMENT_METHOD = 1; 
		//sales
		$TRANSACTIONTYPE = 1; 
		//$MERCHANTID = "SCM_DEV"; //dev
		//$MERCHANTID = "SCM_3D"; // live B2C
		$MERCHANTID = "SCB2B2C_LIVE"; //prod B2B2C
		$date = new DateTime();
		$microtime = floatval(substr((string)microtime(), 1, 8));
		$MERCHANT_TRANID = "OS_".date('mdYHis') . substr($microtime, 2, 3); //create sequence number for transaction id must be unique
		$TRANSACTIONID = '0';
		//$CURRENCYCODE = "HKD";
		$CURRENCYCODE = Mage::app()->getStore()->getCurrentCurrencyCode();		
		$grandTotal = number_format($grandTotal, 2, '.', '');
		$AMOUNT = $grandTotal;
		$CUSTNAME = $cc_owner;
		$CUSTEMAIL = $bill['email'];
		$SHOPPER_IP = $_SERVER['SERVER_ADDR'];
		//for redirect
		//$RESPONSE_TYPE = "2"; 
		//$RETURN_URL = "www.google.com";

		// no redirect
		$RESPONSE_TYPE = "2"; 
		$RETURN_URL = Mage::getBaseUrl() . "checkout/onepage/paymentReturn";
		$CARDNO = $cc_number;
		$CARDNAME= $cc_owner;
		if ($cc_type == "VI") {
			// Visa
			$CARDTYPE = "V";
		} else if ($cc_type == "MC"){
			// MM
			$CARDTYPE = "M";
		}
		$EXPIRYMONTH= $_exp_month;
		// YYYY
		$EXPIRYYEAR= $cc_exp_year; 
		$CARDCVC= $cc_cid;
		$SHARP = "##";
		$ENCRYPT = $SHARP.$MERCHANTID.$SHARP.$PASSWORD.$SHARP.$MERCHANT_TRANID.$SHARP.$AMOUNT.$SHARP.$TRANSACTIONID.$SHARP;
		//echo $ENCRYPT."              ";
		$ENCRYPT = strtoupper($ENCRYPT);
		$SIGNATURE = sha1($ENCRYPT);
		//echo $ENCRYPT."    |   ".$SIGNATURE;
		//$url = 'https://epg.starcruises.com/payment/PaymentInterface.jsp';
		$url = 'https://epg2.starcruises.com/payment/PaymentInterface.jsp';
		//$url = 'https://dvlp.infinitium.com/payment/PaymentInterface.jsp';
		$fields = array(
			'PAYMENT_METHOD'=>$PAYMENT_METHOD,
			'TRANSACTIONTYPE'=>$TRANSACTIONTYPE,
            'MERCHANTID' => $MERCHANTID,
            'MERCHANT_TRANID' => $MERCHANT_TRANID,
            'PYMT_IND' => '',
            'PAYMENT_CRITERIA' => '',
            'CURRENCYCODE' => $CURRENCYCODE,
            'AMOUNT' => $AMOUNT,
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
		return $param;
	}
}
