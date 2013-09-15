<?php

/*
 * @author: diszo.sasil (2013-09-02)
 */
class Movent_Customer_Model_Customer extends Mage_Customer_Model_Customer
{
    
    
    /**
     * Load customer by membershipid
     *
     * @param   string $customerEmail
     * @return  Mage_Customer_Model_Customer
     */
    public function getCustomerByMembershipId($membershipid)
    {
        $customer = Mage::getModel('customer/customer')
						->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
						->setStore(Mage::app()->getStore())
						->getCollection()
						->addAttributeToSelect('membershipid')			
						->addFieldToFilter('membershipid', $membershipid)
						->load();
		
		if($customer->count() == 0){
			$this->setData(array());
		}else{
			$this->setData($customer->getFirstItem()->getData());	
		}
        return $this;
    }
	    
}
