<?php 
class Movent_Starpay_Model_Config extends Mage_Payment_Model_Config
{
   protected $_starpay_config = 'payment/star_ccsave/';
    
	/**
     * Retrieve array of available years
     *
     * @return array
     */
    public function getYears()
    {
        $years = array();
        $first = date("Y");

        for ($index=0; $index <= 20; $index++) {
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
    public function getCcTypes()
    {
        $_types = Mage::getConfig()->getNode('global/payment/starcc/types')->asArray();

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
    public function getPaymentMethods()
    {
        $_types = Mage::getConfig()->getNode('global/payment/starcc/paymentmethod/types')->asArray();

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
    public function getTransactionTypes()
    {
       $_types = Mage::getConfig()->getNode('global/payment/starcc/transaction/types')->asArray(); 
       asort($_types);
       $types = array();
       foreach ($_types as $data){
           if(isset($data['code']) && isset($data['name'])){
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
	public function getStarpayConfig() 
	{ 
		$store_config_ids = array('active', 
		                          'gatewayurl_live',
		                          'merchant_id_live',
		                          'merchant_password_live',
		                          'gatewayurl_test',
		                          'merchant_id_test',
		                          'merchant_password_test',
		                          'paymentmethod',
		                          'transaction', 
								  'enable_testmode'
								 );
									
		$resp_settings = new Varien_Object();			
		foreach($store_config_ids as $config_id){
			$resp_settings->setData($config_id,$this->getStoreConfig($config_id));
			; 
		}
		
		//auto set URL, username, password
		if(!$resp_settings->getEnableTestmode()){ 
			$resp_settings->setStarPayUrl($resp_settings->getGatewayurlLive())
						  ->setMerchantId($resp_settings->getMerchantIdLive())
						  ->setMerchantPassword($resp_settings->getMerchantPasswordLive());
		}else{ 
			$resp_settings->setStarPayUrl($resp_settings->getGatewayurlTest())
						  ->setMerchantId($resp_settings->getMerchantIdTest())
						  ->setMerchantPassword($resp_settings->getMerchantPasswordTest());
		}		
		return $resp_settings;
	}  
	
	public function getCurrentStoreId()
	{
		return Mage::app()->getStore()->getId();
	} 
 
	protected function getStoreConfig($config_id){
		return Mage::getStoreConfig($this->_starpay_config . $config_id, $this->getCurrentStoreId());
	}
}
