<?php 

class Movent_Starpay_Model_System_Config_Source_Payment_Paymentmethod
{
    public function toOptionArray()
    { 
        $options =  array();

        foreach (Mage::getSingleton('starpay/config')->getPaymentMethods() as $code => $name) {
            $options[] = array(
               'value' => $code,
               'label' => $name
            );
        }

        return $options;
    }
}
