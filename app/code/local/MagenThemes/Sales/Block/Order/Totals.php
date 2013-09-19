<?php
class MagenThemes_Sales_Block_Order_Totals extends Mage_Sales_Block_Order_Totals
{
	public function getTotalPoints()
    {
		$order = $this->getOrder();
		$items = $order->getAllItems();
		$totalPoints = 0;
		$discount = 0;
		$exchange_rate = 1;
		foreach ($items as $itemId => $item)
		{
			if ($item->getParentItemId() == null){
				
				$_productObject = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($item->getProductId());
				if($_productObject->getSpecialPoints()>0){
					$exchange_rate = $_productObject->getSpecialPoints();	
				}else{
					$exchange_rate = Mage::getModel('custom/service')->getExchangeRateByProductId($item->getProductId());
				}				

				$discount += ($item->getDiscountAmount()*$exchange_rate);
				
				$points = floatval($item->getPrice()*$item->getQtyOrdered())*$exchange_rate;
				$totalPoints += $points;
			}
		}
		return $totalPoints-$discount;
    }
}    
?>    