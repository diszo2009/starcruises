<?php

class Movent_InfinitiumEpayment_Model_System_Config_Source_Payment_Integrationtype {
	public function toOptionArray() {
		$options = array();

		foreach (Mage::getSingleton('infinitiumepayment/config')->getIntegrationTypes() as $key => $val) {
			$options[] = array('value' => $key, 'label' => $val);
		}

		return $options;
	}

}
