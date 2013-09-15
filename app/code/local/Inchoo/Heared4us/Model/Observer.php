<?php
class Inchoo_Heared4us_Model_Observer
{
	
	const ORDER_ATTRIBUTE_FHC_ID = 'heared4us';
		
    /**
     * Event Hook: checkout_type_onepage_save_order
     * 
     * @author Branko Ajzele
     * @param $observer Varien_Event_Observer
     */
	public function hookToOrderSaveEvent()
	{
		/**
		* NOTE:
		* Order has already been saved, now we simply add some stuff to it,
		* that will be saved to database. We add the stuff to Order object property
		* called "heared4us"
		*/
		
		$incrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();		
		
		//$order = new Mage_Sales_Model_Order();
		if( $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId) )
		{		
			$_txn_id = Mage::getSingleton('core/session')->getTxnId(); 
			
			//Save fhc id to order obcject
			if($_txn_id) {				
				$_heared4us_data        = unserialize($order->getHeared4us());
				$_heared4us_data['Txn'] = $_txn_id;				
				$_heared4us_data        = serialize($_heared4us_data);
				$giftaid 			    = $order->getGiftaid()." Transaction ID: ". $_txn_id;
				
				$order->setData(self::ORDER_ATTRIBUTE_FHC_ID, $_heared4us_data);
				$order->setData('giftaid',$giftaid);
				
				Mage::getSingleton('core/session')->unsTxnId();
			}  
			
			try
			{				
				//deduct points only when pay by points is used.
				if($order->getPayment()->getMethod() == 'checkmo'){
					Mage::getModel('custom/service')->deductPoints(Mage::helper('custom')->getTotalPoints($order));
				}
	
				$order->save();
				$order->sendNewOrderEmail();
				
			}
			catch(Exception $e){
				
			}
			
		}
	}

	
	
}