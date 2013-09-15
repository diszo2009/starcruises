<?php 

/*** 
 * Overriden Cart Controller
 * @date August 22, 2013
 * @Author Movent, Inc. - jerick.duguran@movent.com
 *
 ***/  
 
require_once('Mage/Checkout/controllers/CartController.php');
class Movent_Checkout_CartController extends Mage_Checkout_CartController
{  
	  /**
     * Add product to shopping cart action
     */
    public function addAction()
    { 
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
			 
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }

			/*
             * Check whether product has invalid payment methods
			 */
            $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($product->getId());
			$cart_helper = Mage::helper('checkout/cart');
			if($cart_helper->hasForbiddenPaymentMethods($product)){ 
				 $this->_getSession()->setFloatingProduct($product)
									 ->setFloatingProductRelated($related)
									 ->setFloatingProductRequest($this->getRequest())
									 ->setFloatingProductResponse($this->getResponse())
									 ->setFloatingProductParams($params);
			 	 $this->_redirect('checkout/cart'); 
				 return;
			}
			
            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()){
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_goBack();
        }
    }
	
	public function clearandaddAction()
	{ 
		$cart_session = $this->_getSession();
		
		try{			
			//truncate existin shopping cart
			Mage::getSingleton('checkout/cart')->truncate();
			
			if($cart_session->hasFloatingProduct())
			{   
				$cart      = $this->_getCart();				
				$_product  = $cart_session->getFloatingProduct();
				$_related  = $cart_session->getFloatingProductRelated();
				$_request  = $cart_session->getFloatingProductRequest();
				$_response = $cart_session->getFloatingProductResponse();
				$_params   = $cart_session->getFloatingProductParams();
				
				$cart_session->unsFloatingProduct();
				$cart_session->unsFloatingProductRelated();
				$cart_session->unsFloatingProductRequest();
				$cart_session->unsFloatingProductResponse();
				$cart_session->unsFloatingProductParams();
				 
				$cart->addProduct($_product, $_params);
				if (!empty($_related)) {
					$cart->addProductsByIds(explode(',', $_related));
				}
				
				$cart->save();
				$cart_session->setCartWasUpdated(true);

				/**
				 * @todo remove wishlist observer processAddToCart
				 */
				Mage::dispatchEvent('checkout_cart_add_product_complete',
					array('product' => $_product, 'request' => $_request, 'response' => $_response)
				);

				if (!$cart_session->getNoCartRedirect(true)) {
					if (!$cart->getQuote()->getHasError()){
						$message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($_product->getName()));
						$cart_session->addSuccess($message);
					}
				}
			}
		} catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError($message);
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch(Exception $e){
			$this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e); 
		}
		
		$this->_redirect('checkout/cart'); 
		
		return;
		
	}
}

?>