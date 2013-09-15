<?php 

class Movent_Starpay_Model_System_Config_Source_Payment_Transaction
{
    public function toOptionArray()
    {
        $options =  array();

        foreach (Mage::getSingleton('starpay/config')->getTransactionTypes() as $code => $name) {
            $options[] = array(
               'value' => $code,
               'label' => $name
            );
        }

        return $options;
    }
}
