<?php 

class Movent_InfinitiumEpayment_ProcessingController extends Mage_Core_Controller_Front_Action
{
 
	 /**
     * Send expire header to ajax response
     *
     */
    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    } 
 
    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }  
	
	/*
    public function redirectAction()
    {  
	    $session = $this->_getCheckout();

		if ($session->getQuote()->getHasError()) {
		    $this->_redirect('checkout/cart');
		} else {
			$this->getResponse()->setBody($this->getLayout()->createBlock('infinitiumepayment/redirect')->toHtml());
		}
    }
	
	public function cancelAction()
	{   	    
		
	}
	*/
	
	public function directAction() 
	{
		//if ($this->getRequest()->isPost()) {
        //$data = $this->getRequest()->getPost('payment', array());
		
		
		 			
		$model   = Mage::getModel('infinitiumepayment/method_direct');
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
	
	
   /**
     * When a customer chooses Infinitium E-Payment on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setEpaymentQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('infinitiumepayment/redirect')->toHtml());
        $session->unsQuoteId();
        $session->unsRedirectUrl();
    }

    /**
     * When a customer cancel payment.
     */
    public function cancelAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getEpaymentQuoteId(true));
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId() && ($order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING || $order->getState() == Mage_Sales_Model_Order::STATE_NEW || $order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) ) {
                $order->cancel()->save();
            }
        }
        $this->_redirect('checkout/cart');
    }

    /**
     * when Infinitium returns
     * The order information at this point is in POST
     * variables.  However, you don't want to "process" the order until you
     * get validation from the IPN.
     */
    public function successAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getEpaymentQuoteId(true));
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();	
        $this->_redirect('checkout/onepage/success', array('_secure'=>true));
    }
	
  
}
