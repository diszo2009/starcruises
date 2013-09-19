<?php
/**
 * Common Config
 *
 * @category   Movent
 * @package    Movent_InfinitiumEpayment
 * @copyright  Movent - jerick.duguran@movent.com / diszo.sasil@movent.com 
 * @name       Movent_InfinitiumEpayment_Model_Config
 */
class Movent_InfinitiumEpayment_Model_Config extends Mage_Payment_Model_Config {
	
	const INTEGRATION_TYPE_WINDOW = "window";
	const INTEGRATION_TYPE_DIRECT = "direct";
	
	protected $_infinitiumepayment_config = 'payment/epayment/';

	/**
	 * Retrieve array of available years
	 *
	 * @return array
	 */
	public function getYears() {
		$years = array();
		$first = date("Y");

		for ($index = 0; $index <= 20; $index++) {
			$year = $first + $index;
			$years[$year] = $year;
		}
		return $years;
	}

	/**
	 * Retrieve array of credit card types (Starcruise)
	 *
	 * @return array
	 */
	public function getCcTypes() {
		$_types = Mage::getConfig() -> getNode('global/payment/epayment/types') -> asArray();

		uasort($_types, array('Mage_Payment_Model_Config', 'compareCcTypes'));

		$types = array();
		foreach ($_types as $data) {
			if (isset($data['code']) && isset($data['name'])) {
				$types[$data['code']] = $data;
			}
		}
		return $types;
	}

	/**
	 * Retrieve array of payment methods (Starcruise)
	 *
	 * @return array
	 */
	public function getPaymentMethods() {
		$_types = Mage::getConfig() -> getNode('global/payment/epayment/paymentmethod/types') -> asArray();

		asort($_types);

		$types = array();
		foreach ($_types as $data) {
			if (isset($data['code']) && isset($data['name'])) {
				$types[$data['code']] = $data['name'];
			}
		}
		return $types;
	}

	/**
	 * Retrieve array of transaction types (Starcruise)
	 *
	 * @return array
	 */
	public function getTransactionTypes() {
		$_types = Mage::getConfig() -> getNode('global/payment/epayment/transaction/types') -> asArray();
		asort($_types);
		$types = array();
		foreach ($_types as $data) {
			if (isset($data['code']) && isset($data['name'])) {
				$types[$data['code']] = $data['name'];
			}
		}
		return $types;
	}

	/**
	 * Retrieve array of Integration types
	 *
	 * @return array
	 */
	public function getIntegrationTypes() {
		$_types = Mage::getConfig() -> getNode('global/payment/epayment/integrationtypes/types') -> asArray();
		asort($_types);
		$types = array();
		foreach ($_types as $data) {
			if (isset($data['code']) && isset($data['name'])) {
				$types[$data['code']] = $data['name'];
			}
		}
		return $types;
	}

	/* Get Current Stargenting settings
	 * @date - August 30, 2013
	 * @author Movent - Jerick Y. Duguran
	 * @return Star Pay Settings in Object Format
	 **/
	public function getInfinitiumEpaymentConfig() {
		$store_config_ids = array(	'active', 
									'gatewayurl_window_live', 
									'gatewayurl_direct_live', 
									'merchant_id_live', 
									'merchant_password_live', 
									'gatewayurl_window_test',
									'gatewayurl_direct_test', 
									'merchant_id_test', 
									'merchant_password_test',
                                    'paymentmethod',
									'transaction', 
									'enable_testmode',
									'integration_type');

		$resp_settings = new Varien_Object();
		foreach ($store_config_ids as $config_id) {
			$resp_settings -> setData($config_id, $this -> getStoreConfig($config_id));
			;
		}

		//auto set URL, username, password
		if (!$resp_settings -> getEnableTestmode()) {
			
			if($resp_settings -> getIntegrationType() == self::INTEGRATION_TYPE_DIRECT){
				$resp_settings -> setInfinitiumEpaymentUrl($resp_settings -> getGatewayurlDirectLive());
			}else{ // Window
				$resp_settings -> setInfinitiumEpaymentUrl($resp_settings -> getGatewayurlWindowLive());
			}
			
			$resp_settings -> setMerchantId($resp_settings -> getMerchantIdLive()) 
							-> setMerchantPassword($resp_settings -> getMerchantPasswordLive());
							
		} else {
			
			if($resp_settings -> getIntegrationType() == self::INTEGRATION_TYPE_DIRECT){
				$resp_settings -> setInfinitiumEpaymentUrl($resp_settings -> getGatewayurlDirectTest());
			}else{ // Window
				$resp_settings -> setInfinitiumEpaymentUrl($resp_settings -> getGatewayurlWindowTest());
			}
			
			$resp_settings -> setMerchantId($resp_settings -> getMerchantIdTest()) 
							-> setMerchantPassword($resp_settings -> getMerchantPasswordTest());
		}
        return $resp_settings;
	}

	public function getCurrentStoreId() {
		return Mage::app() -> getStore() -> getId();
	}

	protected function getStoreConfig($config_id) {
		return Mage::getStoreConfig($this -> _infinitiumepayment_config . $config_id, $this -> getCurrentStoreId());
	}

    public function getMethodHandler(){
        if($this -> getStoreConfig('integration_type') ==  self::INTEGRATION_TYPE_DIRECT){
            return Mage::getModel('infinitiumepayment/method_direct');
        }else{
            return Mage::getModel('infinitiumepayment/method_window');
        }
    }

}
