<?php

class Movent_Custom_Helper_Data extends Mage_Core_Helper_Abstract
{ 

	/* Get Current Stargenting settings 
	 * @date - August 27, 2013
	 * @author Movent - Jerick Y. Duguran
	 * @return Star Genting Settings in Object Format
	 **/
	public function getStargentingSettings()
	{ 
		$store_config_ids = array('wsdl_enabled',
		                          'wsdl_testmode',
		                          'wsdl_prod_url',
		                          'wsdl_prod_username',
		                          'wsdl_prod_password',
		                          'wsdl_prod_profitcenter',
		                          'wsdl_prod_remarks',
		                          'wsdl_test_url',
		                          'wsdl_test_username',
		                          'wsdl_test_password',
		                          'wsdl_test_profitcenter',
		                          'wsdl_test_remarks',
								  'wsdl_disabled_message'
								 );
									
		$resp_settings = new Varien_Object();			
		foreach($store_config_ids as $config_id){
			$resp_settings->setData($config_id,Mage::getStoreConfig('custom/stargenting/'. $config_id, $this->getCurrentStoreId())); 
		}
		
		//auto set URL, username, password
		if($resp_settings->getWsdlTestmode()){
			$resp_settings->setUrl($resp_settings->getWsdlTestUrl())
						  ->setUsername($resp_settings->getWsdlTestUsername())
						  ->setPassword($resp_settings->getWsdlTestPassword());
		}else{ 
			$resp_settings->setUrl($resp_settings->getWsdlTestUrl())
						  ->setUsername($resp_settings->getWsdlTestUsername())
						  ->setPassword($resp_settings->getWsdlTestPassword());
		}		
		return $resp_settings;
	} 
	
	public function getCurrentStoreId()
	{
		return Mage::app()->getStore()->getId();
	}
	
	
	public function getTotalPoints($order=null){
		
		if($order)
		{
			$totalPoints   = 0;
			$discount      = 0;
			$exchange_rate = 1;
			$items = $order->getAllItems();
			foreach ($items as $itemId => $item)
			{
				if ($item->getParentItemId() == null){	
					$_productObject = Mage::getModel('catalog/product')->setStore(Mage::app()->getStore())->load($item->getProductId());	
					$exchange_rate = Mage::getModel('custom/service')->getExchangeRateByProductId($item->getProductId());					
					
					if($_productObject->getSpecialPoints()>0){
						$exchange_rate = $_productObject->getSpecialPoints();
					}					
					
					$discount += ($item->getDiscountAmount()*$exchange_rate);
					$totalPoints += floatval($item->getPrice()*$item->getQtyOrdered())*$exchange_rate;
				}
			}
			return $totalPoints-$discount;
		}
		return 0;
	}
}