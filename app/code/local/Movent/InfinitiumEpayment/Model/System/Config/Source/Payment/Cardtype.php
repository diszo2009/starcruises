<?php

class Movent_InfinitiumEpayment_Model_System_Config_Source_Payment_Cardtype {
	public function toOptionArray() {
		$options = array();

		foreach (Mage::getSingleton('infinitiumepayment/config')->getCcTypes() as $data) {
			$options[] = array('value' => $data['code'], 'label' => $data['name']);
		}

		return $options;
	}

}
