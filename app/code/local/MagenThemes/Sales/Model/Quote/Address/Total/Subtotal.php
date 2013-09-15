<?php
class MagenThemes_Sales_Model_Quote_Address_Total_Subtotal extends Mage_Sales_Model_Quote_Address_Total_Subtotal
{
/**
     * Address item initialization
     *
     * @param  $item
     * @return bool
     */
    protected function _initItem($address, $item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $quoteItem = $item->getAddress()->getQuote()->getItemById($item->getQuoteItemId());
        }
        else {
            $quoteItem = $item;
        }
        $product = $quoteItem->getProduct();
        $product->setCustomerGroupId($quoteItem->getQuote()->getCustomerGroupId());

		$_productObject = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($product->getId());
		if($_productObject->getSpecialPoints()>0){
			$exchange_rate = $_productObject->getSpecialPoints();	
		}else{
			$exchange_rate = Mage::getModel('custom/service')->getExchangeRateByProductId($product->getId());
		}
		
        /**
         * Quote super mode flag mean what we work with quote without restriction
         */
        if ($item->getQuote()->getIsSuperMode()) {
            if (!$product) {
                return false;
            }
        }
        else {
            if (!$product || !$product->isVisibleInCatalog()) {
                return false;
            }
        }
		
        if ($quoteItem->getParentItem() && $quoteItem->isChildrenCalculated()) {
            $finalPrice = $quoteItem->getParentItem()->getProduct()->getPriceModel()->getChildFinalPrice(
               $quoteItem->getParentItem()->getProduct(),
               $quoteItem->getParentItem()->getQty(),
               $quoteItem->getProduct(),
               $quoteItem->getQty()
            );
            $item->setPrice($finalPrice)
            	->setData('points',$finalPrice*$exchange_rate)
                ->setBaseOriginalPrice($finalPrice);
            $item->calcRowTotal();
        } else if (!$quoteItem->getParentItem()) {
            $finalPrice = $product->getFinalPrice($quoteItem->getQty());
            $item->setPrice($finalPrice)
            	->setData('points', $finalPrice*$exchange_rate)
                ->setBaseOriginalPrice($finalPrice);
            $item->calcRowTotal();
            $this->_addAmount($item->getRowTotal());
            $this->_addBaseAmount($item->getBaseRowTotal());
            $address->setTotalQty($address->getTotalQty() + $item->getQty());
        }

        return true;
    }
}    