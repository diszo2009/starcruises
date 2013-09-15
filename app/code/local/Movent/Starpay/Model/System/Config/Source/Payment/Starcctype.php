<?php 

class Movent_Starpay_Model_System_Config_Source_Payment_Starcctype
{
    public function toOptionArray()
    {
        $options =  array();

        foreach (Mage::getSingleton('starpay/config')->getCcTypes() as $data) {
            $options[] = array(
               'value' => $data['code'],
               'label' => $data['name']
            );
        }

        return $options;
    }
}
