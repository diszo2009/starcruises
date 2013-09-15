<?php 
class Movent_Checkout_Block_Onepage_Payment extends Mage_Checkout_Block_Onepage_Payment
{
    protected function _construct()
    {
        $this->getCheckout()->setStepData('payment', array(
            'label'     => $this->__('Payment Option'),
            'is_show'   => $this->isShow()
        ));
        parent::_construct();
    }

    /**
     * Getter
     *
     * @return float
     */
    public function getQuoteBaseGrandTotal()
    {
        return (float)$this->getQuote()->getBaseGrandTotal();
    }
    
    public function getTotalPoints()
   	{
        $items    = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
        $discount = 0;
        foreach ($items as $item) 
        {
            $exchange_rate = 1;
            if ($item->getParentItemId() == null){
            $exchange_rate = Mage::getModel('catalog/product')->load($item->getProductId())->getExchange();
               $exchange_rate = floatval($exchange_rate);
            $discount += ($item->getDiscountAmount()*$exchange_rate);
            $this->_totalPoints += floatval(($item->getPoints()*$item->getQty()));
            }
        }
        return $this->_totalPoints-$discount;
   	}
}
