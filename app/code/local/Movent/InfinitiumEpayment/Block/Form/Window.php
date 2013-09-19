<?php
class Movent_InfinitiumEpayment_Block_Form_Window extends Mage_Payment_Block_Form_Cc{

	protected function _construct() {
		parent::_construct();
		$this -> setTemplate('payment/form/epayment_window.phtml');
	}
}
