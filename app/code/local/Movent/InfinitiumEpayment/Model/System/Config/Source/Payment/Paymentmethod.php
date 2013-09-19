<?php

class Movent_InfinitiumEpayment_Model_System_Config_Source_Payment_Paymentmethod {
	public function toOptionArray() {
		$options = array();

		foreach (Mage::getSingleton('infinitiumepayment/config')->getPaymentMethods() as $code => $name) {
			$options[] = array('value' => $code, 'label' => $name);
		}

		return $options;
	}

}
