<?php
/**
 * Product:     Admin Email Notifications for 1.4.x-1.6.1.0 
 * Package:     AdjustWare_Notification_2.2.0_2.1.0_462115
 * Purchase ID: kNDpaTzxfA9cu1lMIvDCAGJ7OTpJlxcSVJoKJewbeD
 * Generated:   2013-01-23 02:01:08
 * File path:   app/code/local/AdjustWare/Notification/Model/Rewrite/Newsletter/Subscriber.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Notification')){ wCZqpZqkmrckEBZP('c970dab9fad5859d1d5bd6d264b460ff'); ?><?php
class AdjustWare_Notification_Model_Rewrite_Newsletter_Subscriber extends Mage_Newsletter_Model_Subscriber
{
    /**
     * Subscribe customer to newsletter
     * Rewrite to add AdjustWare code
     *
     * @param object $customer
     * @return AdjustWare_Notification_Model_Rewrite_Newsletter_Subscriber 
     */
    public function sendConfirmationSuccessEmail()
    {
        parent::sendConfirmationSuccessEmail();
        
        if ($this->getImportMode()) {
            return $this;
        }
        if(!Mage::getStoreConfig(self::XML_PATH_SUCCESS_EMAIL_TEMPLATE) || !Mage::getStoreConfig(self::XML_PATH_SUCCESS_EMAIL_IDENTITY))  {
            return $this;
        }
        // AdjustWare code start
        if (Mage::getStoreConfig('adjnotification/new_newsletter/notification'))
        {
            $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
            $params = array(
                'new_newsletter' => $this,
                'customer_name'=>$customer->getName(),
                );
            Mage::getModel('adjnotification/sender')->sendNotification('new_newsletter', $params);
        }
        // AdjustWare code finish
        return $this;
    }

    /**
     * Unsubscribe customer to newsletter
     * Rewrite to add AdjustWare code
     *
     * @param object $customer
     * @return AdjustWare_Notification_Model_Rewrite_Newsletter_Subscriber 
     */
    
    public function sendUnsubscriptionEmail()
    {
        parent::sendUnsubscriptionEmail();
        
        if ($this->getImportMode()) {
            return $this;
        }
        if(!Mage::getStoreConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE) || !Mage::getStoreConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY))  {
            return $this;
        }

        // AdjustWare code start
        if (Mage::getStoreConfig('adjnotification/unsubscribe_newsletter/notification'))
        {
            $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
            $params = array(
                'unsubscribe_newsletter' => $this,
                'customer_name'=>$customer->getName(),
                );
            Mage::getModel('adjnotification/sender')->sendNotification('unsubscribe_newsletter', $params);
        }
        // AdjustWare code finish
        return $this;
    }
} } 