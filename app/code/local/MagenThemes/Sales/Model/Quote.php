<?php 
class MagenThemes_Sales_Model_Quote extends Mage_Sales_Model_Quote
{
	public function getTotalPoints()
	{		
		$items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
		$exchange_rate = 1;
		$discount = 0;
		$_totalPoints = 0;
		foreach ($items as $item) 
		{
			
			if ($item->getParentItemId() == null){
				$_product = Mage::getModel('catalog/product')->load($item->getProductId());				
				$exchange_rate = Mage::getModel('custom/service')->getExchangeRateByProductId($item->getProductId());				
				$special_points = $_product->getSpecialPoints();				
				
				$discount += ($item->getDiscountAmount()*$exchange_rate);					
				$_totalPoints += floatval(($item->getPoints()*$item->getQty()));
			}
		}
		return $_totalPoints-$discount;
	}
}   
?>
