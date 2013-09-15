<?php

class Movent_Customer_Block_Account_Dashboard_Info extends Mage_Customer_Block_Account_Dashboard_Info
{        
	public function getPoints() {
		$points = Mage::getSingleton('customer/session')->getCustomerPoints();
		if(!$points){
			$points = Mage::getModel('custom/service')->getStargentingPoints();
		}
		return $points;
	}
}
