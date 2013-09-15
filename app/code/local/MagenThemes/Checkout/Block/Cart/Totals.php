<?php
class MagenThemes_Checkout_Block_Cart_Totals extends Mage_Checkout_Block_Cart_Totals
{
protected $_totalPoints = null;

	public function getTotalPoints()
	{
		$this->_totalPoints = Mage::getSingleton('checkout/session')->getQuote()->getTotalPoints();
       
		return $this->_totalPoints;
	}
}	
?>
