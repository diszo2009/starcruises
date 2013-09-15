<?php  
/** 
* Override Customer AccountController 
* 
* Override the createPost action 
* 
* @category Mage 
* @package Mage_Customer 
* @author Will Wright 
*/  
  
/*Controllers aren't included automatically, so we include it here so that we can extend it*/  
include_once("Mage/Customer/controllers/AccountController.php");  
class MagenThemes_Customer_AccountController extends Mage_Customer_AccountController {  

	/**
     * Login post action
     */
    public function loginPostAction()
    {	
		$error = false;
    try {
    $session = $this->_getSession();
    	Mage::getSingleton('customer/session')->setDRSFlag(false);
    	$toDrs = false;
    	$login = $this->getRequest()->getPost('login');
    	$initialusername = $login['username'];
    	$usertype = $login['validateRadio'];
    	if(filter_var($initialusername, FILTER_VALIDATE_EMAIL)) {
        	// valid address
        	$toDrs = false;
        	//var_dump( $toDrs);
        	//die();
    	}
    	else {
    		$toDrs = true;
    		//var_dump( $toDrs);
    		//die();
        	// invalid address
    	}
    	
	    	if ($toDrs && $usertype == 'ID') 
	    	{
	    		$profile = Mage::getModel('custom/service')->getCustomerProfile($login);
				
	    		if($profile["OUTPUTPARAMS"]["CUSTOMERID"]) 
	    		{
					Mage::getSingleton('customer/session')->setCustomerProfile($profile);
					Mage::getSingleton('customer/session')->setLogin($login);
					Mage::getSingleton('customer/session')->setDRSFlag(true);
					
					$customerName = $profile["OUTPUTPARAMS"]["CUSTOMERNAME"];
					preg_match_all("/\[([^()]+)\]/", $customerName, $matches);		
					$lastName = $matches[1];						
					$firstName = preg_split('/\[.*?\]/', $customerName);			
					
					foreach($firstName as $name){				
						$fName .= $name . " ";			
					}
					
	    			/** START -> Created By: Ralph Arcaya */
	    			$membershipid = $profile["OUTPUTPARAMS"]["CUSTOMERID"];
	    			$websiteId = Mage::app()->getWebsite()->getId();
	    			$store = Mage::app()->getStore();
	    			$password  = $login['password'];
			
					// Check if customer email already exist. return true/false.
	    			$cust_exist = $this->IscustomerEmailExists($membershipid,$websiteId,$lastName,$fName);
					if(!$cust_exist)
					{	
						$customer = Mage::getModel("customer/customer");
			            $customer->website_id = $websiteId;
			            $customer->setStore($store);
			           
			            // If new, save customer information
			            $customer->email = $profile["OUTPUTPARAMS"]["EMAILADDRESS"];
			            $customer->password_hash = md5($password);			            				
						$customer->firstname = ucwords(strtolower($fName));          
						$customer->lastname = ucwords(strtolower($lastName[0]));            
			
						$customer->membershipid = $profile["OUTPUTPARAMS"]["CUSTOMERID"];
						//$customer->dob = '1980-06-25 00:00:00';
						$date = $profile["OUTPUTPARAMS"]["CUSTOMERDATEOFBIRTH"];
						$dob = array();
						$dob = date_parse_from_format("Ymd", $date);
						$customer->dob = $dob["year"] . "-" . $dob["month"] . "-" . $dob["day"] . " 00:00:00";
			            $customer->save();
	           
			            //Build billing and shipping address for customer, for checkout
			            $_custom_address = array (
			                'firstname' => ucwords(strtolower($fName)),
			                'lastname' => ucwords(strtolower($lastName[0])),
			                'street' => array (
			                    '0' => '',
			                    '1' => '',
			                ),
			             
			                'city' => '',
			                'region_id' => '',
			                'region' => '',
			                'postcode' => '',
			                'country_id' => '',
			                'telephone' => '',
			            );
	           
			            $customAddress = Mage::getModel('customer/address');
			            $customAddress->setData($_custom_address)
			                ->setCustomerId($customer->getId())
			                ->setIsDefaultBilling('1')
			                ->setIsDefaultShipping('1')
			                ->setSaveInAddressBook('1');
	           
	            		$customAddress->save(); 
					}	
				
					$login['username'] = $membershipid;
					/** END */
	    		}    	
			}
			elseif ($usertype == 'ID' && $toDrs == false){
				 $message = 'Invalid Membership ID';
	             $session->addError($message);
	             $error = true;
			}
		
		
		} catch (Exception $e) {
            $message = $e->getMessage();
            //$message = "Error: Couldn't connect to DRS.";
            $session->addError($message);
            $session->setUsername($initialusername);
		}


        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
		
        $session = $this->_getSession();

		if ($this->getRequest()->isPost() && $error == false) { 
			if (!empty($login['username']) && !empty($login['password'])) {
				try {
					$session->login($login['username'], $login['password']);
					if ($session->getCustomer()->getIsJustConfirmed()) {
						$this->_welcomeCustomer($session->getCustomer(), true);
					}
				} catch (Mage_Core_Exception $e) {
					switch ($e->getCode()) {
						case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
							$value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
							$message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
							break;
						case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
							$message = $e->getMessage();
							break;
						default:
							$message = $e->getMessage();
					}
					$session->addError($message);
					$session->setUsername($initialusername);
				} catch (Exception $e) {
					// Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
				}
			} else {
				$session->addError($this->__('Login and password are required.'));
			}
		}
		$this->_loginPostRedirect();
    }
	
	
	
	/**
     * 
     * This function check if the customer email already exist
     * @param String $email
     * @param String $websiteId
     * @return boolean
     */
	protected function IscustomerEmailExists($email, $websiteId = null,$lastname = null, $firstname = null)
	{
		$profile = Mage::getSingleton('customer/session')->getCustomerProfile();
	    $customer = Mage::getModel('customer/customer');
	 
	    if ($websiteId) {
	        $customer->setWebsiteId($websiteId);
	    }
		if (Mage::getSingleton('customer/session')->getIsStarGentingUser()) {
			$customer->loadByMembershipid($email);
		} else {
			$customer->loadByEmail($email);
		}    
	    
	    if ($customer->getId()) {
	    	$customer->email = $profile["OUTPUTPARAMS"]["EMAILADDRESS"];
	    	$customer->firstname = ucwords(strtolower($firstname)); 
	    	$customer->lastname = ucwords(strtolower($lastname[0])); 
			$customer->save();
	        return $customer->getId();
	    }
		
	    return false;
	}
     /** END */
	
	
} 