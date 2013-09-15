<?php

class Movent_Sales_Model_Order extends Mage_Sales_Model_Order
{
    
	public function getTxnId() {
		$data 		= $this->getHeared4us();
		$arrData    = unserialize($data);			
		if(isset($arrData['Txn'])){
			return $arrData['Txn'];
		}
		return 0;
	}
	
	public function getPickupData() { 
		$data   = $this->getHeared4us();  
		$string = "";
		//if(!preg_match('/Txn/',$data)){
			$arrData = unserialize($data);	 
			if(isset($arrData['Txn'])){
				unset($arrData['Txn']);
			}
			if(count($arrData)>0){ 
				foreach($arrData as $row){					
					$string .= $row['pickup_location'].'<br>';
					$string .= $row['store_type'].' Date: '.$row['cruise_date'].'<br>';
					if(!empty($row['cruise_reservation_number'])){
						$string .= 'Cruise Reservation Number: '.$row['cruise_reservation_number'].'<br><br>';
					}else{
						$string .= "<br/>";
					}
				}
				return $string;
			}
		//} 
		return $string;		
	}  
}
