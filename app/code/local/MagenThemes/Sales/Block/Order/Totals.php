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
				$exchange_rate = Mage::getModel('catalog/product')->load($item->getProductId())->getExchange();
				$exchange_rate = floatval($exchange_rate);
				if ($exchange_rate == "")
				{
					$baseCode = Mage::app()->getBaseCurrencyCode();
					$allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies(); 
					$rates = Mage::getModel('directory/currency')->getCurrencyRates($baseCode, array_values($allowedCurrencies));
					$exchange_rate = $rates['PTS'];
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