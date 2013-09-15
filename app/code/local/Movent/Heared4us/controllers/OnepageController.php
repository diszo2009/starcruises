<?php 

/**
 * Overriden Onepage Controller  
 * @date August 22, 2013
 * @Author Movent, Inc. - jerick.duguran@movent.com
 *
 **/  
 
require_once('Inchoo/Heared4us/controllers/OnepageController.php');
class Movent_Heared4us_OnepageController extends Inchoo_Heared4us_OnepageController
{ 
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
			
			$result		 					= array();
			//$pickupData 					= array();
			$cruise_reservation_numbers  	= $this->getRequest()->getPost('cruise-reservation-number');
			
        	for($i = 1; $i <= $_pickupCounter; $i++) {
        		$_pickupLocation 			= $this->getRequest()->getPost('pickup-location-'.$i);
        		$_cruiseDate 	 			= $this->getRequest()->getPost('cruise-date-'.$i);
				$locType 					= Mage::getSingleton('custom/pickupoptions')->getStoreAttributeLocationType($_pickupLocation);	
				$_pickupLocationVal 		= Mage::getSingleton('custom/pickupoptions')->getStoreLocationByValue($_pickupLocation);
								
				if($locType != false && $locType == Movent_Custom_Model_Pickupoptions::LOCATION_TYPE_CRUISE){
					$storeType = "Cruise";	
				}
				else{
					$storeType = "Pickup";
				} 
				
				$cruise_reservation_number  = isset($cruise_reservation_numbers[$i]) ? $cruise_reservation_numbers[$i] : '';
				$pickupData[] = array('cruise_date' 				=> $_cruiseDate,
									  'pickup_location' 			=> $_pickupLocationVal['label'],
									  'store_type' 					=> $storeType,
									  'cruise_reservation_number'	=> $cruise_reservation_number); 		
				
				if( !(strtotime($_cruiseDate) >= strtotime($currDate))){					
					$result['message'] = Mage::helper('checkout')->__($storeType.' date value for "'.$_pickupLocationVal['label'].'" is invalid.');
					$result['error'] = true;
					echo Zend_Json::encode($result);
					exit;
				}
				
        	}   
			
			//Mage::getSingleton('core/session')->setInchooHeared4us(serialize($pickupData));
			Mage::getSingleton('core/session')->setPickupCounter($_pickupCounter);
			
            $this->getOnepage()->saveHeared4Us($pickupData); 
			
            if (!$result) {
                $this->loadLayout('checkout_onepage_payment');

                $result['goto_section'] = 'payment';
                $result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
                );

            } 
            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
	}
	
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
            
            /*
            $paymentMethod = $this->getOnepage()->getQuote()->getPayment()->getMethod(); 
			Mage::getSingleton('checkout/session')->setPaymentMethod($paymentMethod);
			if ($paymentMethod == "checkmo") {
			
				$stargt_obj = Mage::getModel('custom/service')->getStargentingSettings();
				if(!$stargt_obj->getWsdlEnabled()){ 
					Mage::throwException($stargt_obj->getWsdlDisabledMessage());
					return false;
				} 			 
				
				if (Mage::getSingleton('checkout/session')->getTotalPoints() > Mage::getSingleton('customer/session')->getCustomerPoints()) {
					Mage::throwException(Mage::helper('checkout')->__('Insufficient Points. Please choose Pay by Credit Card to proceed with purchase.'));
                    return;
				}
			}
			*/
			
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
            
			Mage::helper('starpay')->setCardPostData($data); 
			
			/*
            $bill = Mage::getSingleton('core/session')->getEmail();    		
            $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId()+1;            
            $DRSFlag 		= Mage::getSingleton('customer/session')->getIsStarGentingUser();
            $paymentMethod  = $this->getOnepage()->getQuote()->getPayment()->getMethod();
            $grandTotal 	= Mage::getSingleton('checkout/session')->getGrandTotal(); 
			if($DRSFlag)
            {
            	if($paymentMethod == "checkmo") {
            		$response = Mage::getModel('custom/service')->deductPoints();
					
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
            } 
			*/
            $this->getOnepage()->saveOrder();			
            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl(); 
 		  	if (isset($redirectUrl)) {   
				$this->getOnepage()->getCheckout()->setRedirectUrl($redirectUrl); 
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
    
}
