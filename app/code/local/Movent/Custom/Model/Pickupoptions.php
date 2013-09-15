<?php
/*
 * Movent Custom : Pickupoptions
 *
 * Copyright 2013 diszo.sasil <diszo.sasil@movent.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 *
 *
 */
class Movent_Custom_Model_Pickupoptions extends Mage_Core_Model_Abstract
{
	
	const LOCATION_TYPE_SHORE = 'SHORE';
	const LOCATION_TYPE_CRUISE = 'CRUISE';
	
	protected $__options = null;	
	
	public function __construct(){
		parent::__construct();
		$this->prepareOptions();
	}	
	
	public function getAttributeAdmin($attribute,$attributeValueId){
        $_collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                        ->setStoreFilter(0)
                            ->setAttributeFilter($attribute->getId())
                                ->load();
		
        foreach( $_collection->toOptionArray() as $_cur_option ) {
            if ($_cur_option['value'] == $attributeValueId){ 
				return $_cur_option;
			}
          
        }		
		
        return false;
    }
	
	public function getPickupLocationAttribute(){
		return Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'pickup_location');	
	}
	
	// Get Website Store Location
	public function getAllPickUpLocations(){
		$attribute = $this->getPickupLocationAttribute();
		return $attribute->getSource()->getAllOptions(false);
	}

	protected function prepareOptions(){
		$options = array();
		$allLocations = $this->getAllPickUpLocations();
		foreach($allLocations as $row){		
			$options[$row['value']] = $row;
		}
		$this->__options = $options;
	}
	
    public function getStoreLocationByValue($value){	
		if(is_null($this->__options)) {
			$this->prepareOptions();
		}
		return 	isset($this->__options[$value]) ? $this->removePrefixType($this->__options[$value]) : null;		
	}
	
	public function removePrefixType($label=""){
		return str_replace(array(self::LOCATION_TYPE_SHORE.':',self::LOCATION_TYPE_CRUISE.':'),'',$label);
	}
	
	public function getStoreAttributeLocationType($id){
		$attr = $this->getAttributeAdmin($this->getPickupLocationAttribute(),$id);	
		if( $attr != false && strpos($attr['label'],':') !== false ){
			$part = explode(":",$attr['label']);			
			return strtoupper($part[0]);
		}else{ 
			return self::LOCATION_TYPE_SHORE; // Default
		}
	}
	
	public function getPickupItems(){
		$items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
		$products = array();		
		
		foreach ($items as $item) {
			$_item = Mage::getModel('catalog/product')->setStore(Mage::app()->getStore())->load($item->getProductId());
					
			if ($_item->getPickupLocation()) {
				$products[] = array(
					'name'  => $_item->getName(),
					'pickupLocation'   => $_item->getPickupLocation(),
					'qty' => (int) $item->getQty()
					);
			}
		}
		
		return $products;
	}
		
	public function getPickupData($type=""){	
		
		if($type == "ORDERED_ITEMS"){
			$products = $this->getLastPurchasePickupItems();
		}else{
			$products = $this->getPickupItems();
		}
		
		Mage::getSingleton('core/session')->setProducts($products);		
		$store = array();
		
		foreach($products as $item) {			
			$prodInfo = $this->getStoreLocationByValue($item['pickupLocation']);				
			if(!is_null($prodInfo)){
				$store[$item['pickupLocation']][] = array(
															$item['name'],
															$item['qty']
														);
			}
		}		
		return $store;
	}
	
	public function getLastPurchasePickupItems(){
		$session = Mage::getSingleton('checkout/session');		
		$products = array();		
		if ($session->getLastRealOrderId()) {
			if($order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId())){
				$items = $order->getAllItems();
				foreach ($items as $itemId => $item)
                {
					$_item = Mage::getModel('catalog/product')->setStore(Mage::app()->getStore())->load($item->getProductId());					
					if ($_item->getPickupLocation()) {
						$products[] = array(
							'name'  => $_item->getName(),
							'pickupLocation'   => $_item->getPickupLocation(),
							'qty' => (int) $item->getQtyOrdered()
						);
					}
				}
			}
		}		
		return $products;
	}
	
	
}