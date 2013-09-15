<?php
/*
 * @author: diszo.sasil (2013-09-02)
 */
class Movent_Customer_Model_Resource_Customer extends Mage_Customer_Model_Resource_Customer
{
    /**
     * Load customer by MembershipId
     *
     * @throws Mage_Core_Exception
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param string $membershipid
     * @param bool $testOnly
     * @return Mage_Customer_Model_Resource_Customer
     */
    public function loadByMembershipId(Mage_Customer_Model_Customer $customer, $membershipid, $testOnly = false)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array('membershipid' => $membershipid);
        $select  = $adapter->select()
            ->from($this->getEntityTable(), array($this->getEntityIdField()))
            ->where('membershipid = :membershipid');

        if ($customer->getSharingConfig()->isWebsiteScope()) {
            if (!$customer->hasData('website_id')) {
                Mage::throwException(
                    Mage::helper('customer')->__('Customer website ID must be specified when using the website scope')
                );
            }
            $bind['website_id'] = (int)$customer->getWebsiteId();
            $select->where('website_id = :website_id');
        }

        $customerId = $adapter->fetchOne($select, $bind);
        if ($customerId) {
            $this->load($customer, $customerId);
        } else {
            $customer->setData(array());
        }

        return $this;
    }
}
