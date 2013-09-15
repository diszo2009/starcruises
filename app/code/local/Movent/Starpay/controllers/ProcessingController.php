<?php 

class Movent_Starpay_ProcessingController extends Mage_Core_Controller_Front_Action
{
    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }  
	
    public function redirectAction()
    {  
	    $session = $this->_getCheckout();

		if ($session->getQuote()->getHasError()) {
		    $this->_redirect('checkout/cart');
		} else {
			$this->getResponse()->setBody($this->getLayout()->createBlock('starpay/redirect')->toHtml());
		}
    }
	
	public function cancelAction()
	{   	    
		
	}
	
	public function normalAction() 
	{ 		 	
		file_put_contents("./payment_reponse_log.log",print_r($_POST,1),FILE_APPEND);
		$model   = Mage::getModel('starpay/method_starccsave');
		$session = Mage::getSingleton('checkout/session'); 		
		
		if ($_POST['ERR_DESC'] == 'No error' && $_POST['USR_CODE'] == '101' && $_POST['BANK_RES_CODE'] && '00') {
    		$this->getResponse()->setRedirect(Mage::getBaseUrl() . "checkout/onepage/success");
			Mage::getSingleton('core/session')->addSuccess(Mage::helper('core')->__('Your transaction ID is '.$_POST['MERCHANT_TRANID']));
			Mage::getSingleton('core/session')->setTxnId($_POST['MERCHANT_TRANID']);
    	} else {
    		if ($session->getLastRealOrderId()) {
    			$order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
    			if ($order->getId()) {  
				
					// Modified By: Movent  
					if(isset($_POST['MERCHANT_TRANID'])) {				
						$_heared4us_data        = unserialize($order->getHeared4us());
						$_heared4us_data['Txn'] = $_POST['MERCHANT_TRANID'];				
						$_heared4us_data        = serialize($_heared4us_data);
						$giftaid 			    = $order->getGiftaid()." Transaction ID: ". $_txn_id;
						
						$order->setData('heared4us', $_heared4us_data);
						$order->setData('giftaid',$giftaid);
						Mage::getSingleton('core/session')->unsTxnId(); 
					}
					
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
	
	public function automaticAction() 
	{	     
	} 

    protected function getData()
    {
        if ($this->getRequest()->isPost('DATA')) return $_POST['DATA'];
        elseif ($this->getRequest()->isGet('DATA')) return $_GET['DATA'];
        else return;
    }
  
}
