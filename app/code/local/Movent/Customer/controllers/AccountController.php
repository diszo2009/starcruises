<?php  
/** 
* Override MagenThemes Customer AccountController 
* 
* 
* @category Movent 
* @package Movent_Customer 
* @author diszo.sasil (2013-09-02) Movent Inc. 
*/  
  
 
include_once("MagenThemes/Customer/controllers/AccountController.php");  
class Movent_Customer_AccountController extends MagenThemes_Customer_AccountController {  

	/**
     * Login post action
     */
    public function loginPostAction()
    { 
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
		
        $session = $this->_getSession();

		if ($this->getRequest()->isPost()) {			
			$login = $this->getRequest()->getPost('login'); 
			if (!empty($login['username']) && !empty($login['password'])) {
				try {
					
					if ($login['validateRadio'] == 'ID'){
						$session->loginStargentingMember($login['username'], $login['password']);
					}else{
						$session->login($login['username'], $login['password']);
					}
					
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
					$session->setUsername($login['username']);
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
     * This function check if the customer membershipID already exist
     * @param String $membershipId
     * @param String $websiteId
     * @return boolean
	 * @author: diszo.sasil (2013-09-02) by Movent Inc.
     */
	protected function IsMembershipIdExists($membershipId, $websiteId = null)
	{	
		$websiteId = $websiteId == null ? Mage::app()->getWebsite()->getId() : $websiteId;
						
		$customer = Mage::getModel('customer/customer')
						->loadByAttributes(array('membershipid'=>$membershipId,
												'website_id'=>$websiteId));
		
		
		echo print_r($customer->getData(),1);
		
		die();
		
	    if ($customer->getId()) {
	    	return true;
	    }
		
	    return false;
	}	
} 