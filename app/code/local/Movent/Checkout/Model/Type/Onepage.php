<?php 
class Movent_Checkout_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{     
    public function saveHeared4Us($data)
    {  
		if (empty($data) || !is_array($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }
		$quote   = $this->getQuote();
		$giftaid = "";
		
		foreach($data as $heared4us){
			$giftaid .= $heared4us['pickup_location'].'<br>';
			$giftaid .= $heared4us['store_type'].' Date: '.$heared4us['cruise_date'].'<br>';
			$giftaid .= 'Cruise Reservation Number: '.$heared4us['cruise_reservation_number'].'<br><br>'; // ADDED by Movent
		}
		
		$quote->setHeared4us(serialize($data));
		$quote->setGiftaid($giftaid); 		
		$quote->save();
		
    	$this->getCheckout()
            ->setStepData('heared4us', 'complete', true)
            ->setStepData('payment', 'allow', true);
         
        return;
    }
 
}
