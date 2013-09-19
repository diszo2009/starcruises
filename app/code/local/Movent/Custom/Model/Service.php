<?php
/**
 * 
 *  For Stargenting and Starcruise Payment  
 *
 * @added by: diszo.sasil
 * @date: 2013-08-29
 *
 */
class Movent_Custom_Model_Service
{
	const DEFAULT_CURRENCY_CODE = 'HK';
	
	// Configurable Price Display Attribute value
	/* // Dev Local Settings
	const DISPLAY_BOTH = 51;
	const DISPLAY_PRICE_ONLY = 52;
	const DISPLAY_POINTS_ONLY = 53;
	*/
	
	 // Dev Local Settings
	const DISPLAY_BOTH = 33;
	const DISPLAY_PRICE_ONLY = 32;
	const DISPLAY_POINTS_ONLY = 31;
		
	// Starcruise Supported codes
	protected $_currencyCodes = array('HKD'=>'HK',
									 'SGD'=>'SD',
									 'MYR'=>'RM',
									 'TWD'=>'NT',
									);	
	
	
	
	/*
	 * Get Currency Code for Deduction
	 */
	public function getDeductCurrencyCode(){
		$code = Mage::app()->getStore()->getCurrentCurrencyCode();
		if(isset($this->_currencyCodes[$code])){
			return $this->_currencyCodes[$code];
		}else{
			return self::DEFAULT_CURRENCY_CODE;
		}
	}
	
	public function getCustomerSession()
	{
		return Mage::getSingleton('customer/session');
	}
	
	/*
	 * Get Customer Profile
	 */
	public function getCustomerProfile($login)
	{
		$stargt_obj = $this->getStargentingSettings();
		if(!$stargt_obj->getWsdlEnabled()){
			Mage::throwException($stargt_obj->getWsdlDisabledMessage());
			return false;
		}		
		
		try
		{			
			$soap 			     = new soapclient($stargt_obj->getUrl());
			$request 			 = new stdClass(); 
			$request->paraDrsID  = $stargt_obj->getUsername();  
			$request->paraDrsPwd = $stargt_obj->getPassword();  
			
			$request->paraCid = $login['username'];
			$request->paraPIN = $login['password']; 
			
			$request->paraEnquiryCurrCode = $this->getDeductCurrencyCode(); 
			$result = $soap->API_AutoUA_VerifyPIN_ReturnCustomerProfile($request); 
			
			if($result->API_AutoUA_VerifyPIN_ReturnCustomerProfileResult){
				$result = $this->XMLtoArray($result->API_AutoUA_VerifyPIN_ReturnCustomerProfileResult->any); 
				if(isset($result['ERR'])){
					Mage::throwException($result['ERR']['ERRMSG']);				
				}
				return $result;
			}
		}
		catch(exception $e){
			throw $e;
		}	 	 
	}
	
	
	public function deductPoints($totalpoints=0)
	{
		/* Get Current Stargenting settings - Movent - August 2013
		 * @author Movent - Jerick Y. Duguran
		 * @date August 2013
		 */
		$login = $this->getCustomerSession()->getLoginInfo();
		$stargt_obj = $this->getStargentingSettings();
		if(!$stargt_obj->getWsdlEnabled()){ 
			Mage::throwException($stargt_obj->getWsdlDisabledMessage());
			return false;
		}
		
		try
		{
			$soap 				 = new soapclient($stargt_obj->getUrl());
			$request 			 = new stdClass();  
			
			$request->paraDrsID  			= $stargt_obj->getUsername();  
			$request->paraDrsPwd 			= $stargt_obj->getPassword();  		
			$request->paraCid 				= $login['username'];
			$request->paraCashToAdjust 		= -$totalpoints;
			$request->paraCashTypeToAdjust  = 0; 
			$request->paraCurrCode 			= $this->getDeductCurrencyCode();
			$request->paraProfitCenter 	    = $stargt_obj->getProfitCenter();  
			$request->paraRemark 			= $stargt_obj->getRemarks();
					
			
			$result = $soap->API_AutoUA_CEA_Currency($request);
			if($result->API_AutoUA_CEA_CurrencyResult){
				$result = $this->XMLtoArray($result->API_AutoUA_CEA_CurrencyResult->any); 
				
				
				if(isset($result['ERR'])){
					Mage::throwException($result['ERR']['ERRMSG']);				
				}
				
				$this->updatePoints(true);
				
				return $result;
			}
		}
		catch(exception $e){
			throw $e;
		}	  
    } 
	
	/*
	 *  Get Current Store Id
	 */
	public function getCurrentStoreId()
	{
		return Mage::app()->getStore()->getId();
	}		
	
	/* Get Current Stargenting settings 
	 * @date - August 27, 2013
	 * @author Movent - Jerick Y. Duguran
	 * @return Star Genting Settings in Object Format
	 **/
	public function getStargentingSettings()
	{ 
		$store_config_ids = array('wsdl_enabled',
		                          'wsdl_testmode',
		                          'wsdl_prod_url',
		                          'wsdl_prod_username',
		                          'wsdl_prod_password',
		                          'wsdl_prod_profitcenter',
		                          'wsdl_prod_remarks',
		                          'wsdl_test_url',
		                          'wsdl_test_username',
		                          'wsdl_test_password',
		                          'wsdl_test_profitcenter',
		                          'wsdl_test_remarks',
								  'wsdl_disabled_message'
								 );
									
		$resp_settings = new Varien_Object();			
		foreach($store_config_ids as $config_id){
			$resp_settings->setData($config_id,Mage::getStoreConfig('custom/stargenting/'. $config_id, $this->getCurrentStoreId())); 
		}
		
		//auto set URL, username, password
		if($resp_settings->getWsdlTestmode()){ 
			$resp_settings->setUrl($resp_settings->getWsdlTestUrl())
						  ->setUsername($resp_settings->getWsdlTestUsername())
						  ->setPassword($resp_settings->getWsdlTestPassword())
						  ->setProfitCenter($resp_settings->getWsdlTestProfitcenter())
						  ->setRemarks($resp_settings->getWsdlTestRemarks());
		}else{  
			$resp_settings->setUrl($resp_settings->getWsdlProdUrl())
						  ->setUsername($resp_settings->getWsdlProdUsername())
						  ->setPassword($resp_settings->getWsdlProdPassword())
						  ->setProfitCenter($resp_settings->getWsdlProdProfitcenter())
						  ->setRemarks($resp_settings->getWsdlProdRemarks());
		}		
		return $resp_settings;
	} 
		
	public function getStargentingPoints($fetchLatest=false)
	{
		if($fetchLatest){
			$profile = $this->getCustomerProfile($this->getCustomerSession()->getLoginInfo());
			$this->getCustomerSession()->setStargentingProfile($profile);
		}else{
			$profile = $this->getCustomerSession()->getStargentingProfile();
		}
		
		if(isset($profile["OUTPUTPARAMS"]["VISIBLEENTITLEMENTBALANCE"])){
			$points = $profile["OUTPUTPARAMS"]["VISIBLEENTITLEMENTBALANCE"];
			$this->getCustomerSession()->setCustomerPoints($points);
			return $points;
		}
		return 0;
	}
	
	
	public function updatePoints(){
		$this->getStargentingPoints(true);
	}
	
	public function getExchangeRateByProductId($productid=0){
		$exchange_rate = 0;
		if($productid>0)
		{
			$exchange_rate = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($productid)->getExchange();
		    $exchange_rate = floatval($exchange_rate);
			if ($exchange_rate == 0 )
			{
				$baseCode = Mage::app()->getBaseCurrencyCode();
				$allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies(); 
				$rates = Mage::getModel('directory/currency')->getCurrencyRates($baseCode, array_values($allowedCurrencies));
				$exchange_rate = floatval($rates['PTS']);	
			}
		}
		return $exchange_rate;
	}
	
	
	/*
	 * Utility to Convert XML to Array
	 */
	public function XMLtoArray($XML)
	{
    	$xml_parser = xml_parser_create();
    	xml_parse_into_struct($xml_parser, $XML, $vals);
	    xml_parser_free($xml_parser);
	    $_tmp='';
	    foreach ($vals as $xml_elem) {
    	    $x_tag=$xml_elem['tag'];
        	$x_level=$xml_elem['level'];
	        $x_type=$xml_elem['type'];
    	    if ($x_level!=1 && $x_type == 'close') {
        	    if (isset($multi_key[$x_tag][$x_level]))
            	    $multi_key[$x_tag][$x_level]=1;
	            else
    	            $multi_key[$x_tag][$x_level]=0;
        	}
	        if ($x_level!=1 && $x_type == 'complete') {
    	        if ($_tmp==$x_tag)
        	        $multi_key[$x_tag][$x_level]=1;
        	    $_tmp=$x_tag;
	        }
    	}

    	foreach ($vals as $xml_elem) {
        	$x_tag=$xml_elem['tag'];
	        $x_level=$xml_elem['level'];
    	    $x_type=$xml_elem['type'];
        	if ($x_type == 'open')
            	$level[$x_level] = $x_tag;
	        $start_level = 1;
    	    $php_stmt = '$xml_array';
        	if ($x_type=='close' && $x_level!=1)
            	$multi_key[$x_tag][$x_level]++;
	        while ($start_level < $x_level) {
    	        $php_stmt .= '[$level['.$start_level.']]';
        	    if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
            	    $php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';
	            $start_level++;
    	    }
        	$add='';
	        if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type=='open' || $x_type=='complete')) {
    	        if (!isset($multi_key2[$x_tag][$x_level]))
        	        $multi_key2[$x_tag][$x_level]=0;
            	else
                	$multi_key2[$x_tag][$x_level]++;
	            $add='['.$multi_key2[$x_tag][$x_level].']';
    	    }
        	if (isset($xml_elem['value']) && trim($xml_elem['value'])!='' && !array_key_exists('attributes', $xml_elem)) {
	            if ($x_type == 'open')
    	            $php_stmt_main=$php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
        	    else
            	    $php_stmt_main=$php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';
	            eval($php_stmt_main);
    	    }
        	if (array_key_exists('attributes', $xml_elem)) {
            	if (isset($xml_elem['value'])) {
                	$php_stmt_main=$php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
    	            eval($php_stmt_main);
	            }
        	    foreach ($xml_elem['attributes'] as $key=>$value) {
            	    $php_stmt_att=$php_stmt.'[$x_tag]'.$add.'[$key] = $value;';
                	eval($php_stmt_att);
	            }
    	    }
	    } 
    	return $xml_array;
	} 	
}