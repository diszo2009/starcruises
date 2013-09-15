<?php
/*
 * Extend Customer Session
 * 
 * @author: diszo.sasil (2013-09-02)
 */
class Movent_Customer_Model_Session extends Mage_Customer_Model_Session
{
 
 	const STARGENTING_GROUP_ID = 4;  
    /**
     * Customer authorization for Stargenting Member
     *
     * @param   string $username
     * @param   string $password
     * @return  bool
     */
    public function loginStargentingMember($username, $password)
    {
    	return $this->authenticate3rdParty(array('username'=>$username,'password'=>$password));
    }
		
	public function authenticate3rdParty($login){
		try
		{
			$profile = Mage::getModel('custom/service')->getCustomerProfile($login);
			$this->setIsStarGentingUser(false);
			if(isset($profile["OUTPUTPARAMS"])) 
		    {
		    	$params = $profile["OUTPUTPARAMS"];			
				$customer = $this->getCustomerByMembershipId($params["CUSTOMERID"]);
				
				$_customerObj = Mage::getSingleton('customer/customer')->loadByEmail($params["EMAILADDRESS"]);
				
				if(!$customer->getId() && !$_customerObj->getId()){
					$customer = $this->createStarGentingMember($params,$login);
				}
				elseif(!$customer->getId() && $_customerObj->getId())
				{
					// To do: Priority issue (create new user with different email or override found email info)
					$customer = $_customerObj;
				}
							
				$this->setIsStarGentingUser(true);
				$this->setStargentingProfile($profile);
				
				// Syncronize address and customer info
				$customer = $this->syncronizeMemberInfo($customer);
				
				$this->setCustomerAsLoggedIn($customer);
	           	$this->renewSession();			
				$this->setLoginInfo($login); // contains associative array username and password
				 // contains associative array of user profile
		    	return true;
			}else{
				$this->addError(Mage::helper('customer')->__('Invalid Star Genting Membership ID / PIN.'));
			}
			return false;
		}catch(Exception $e){			
			Mage::throwException($e->getMessage());
		}
	}

	
	public function createStarGentingMember($params=array(),$loginInfo=array())
	{		
		try
		{	
			$name = $this->getProfileName();
			$customer = Mage::getModel("customer/customer")
								->setWebsiteId(Mage::app()->getWebsite()->getId())
								->setStore(Mage::app()->getStore())
								->setEmail($params["EMAILADDRESS"])
								->setPassword($loginInfo['password'])
								->setPasswordHash(md5($loginInfo['password']))
								->setFirstname($name['firstname'])
								->setLastname($name['lastname'])
								->setMembershipid($params["CUSTOMERID"])
								->setDob($this->getFormattedDob())
								->setGroupId(self::STARGENTING_GROUP_ID)
								->setPrefix(ucwords(strtolower($params["CUSTOMERTITLE"])))
								->setMembershipid($params["CUSTOMERID"])
								->setGender(($params["CUSTOMERGENDER"] == "M" ? 1 : 2))
								->save();
								
			if($customer->getId())
			{
				//Build billing and shipping address for customer, for checkout				
				$customAddress = Mage::getModel('customer/address')
								->setCustomerId($customer->getId())
								->setPrefix($customer->getPrefix())
								->setFirstname($customer->getFirstname())
								->setLastname($customer->getLastname())
								->setStreet(array($params['CUSTOMERADDRESSLINE1'],
												$params['CUSTOMERADDRESSLINE2'],
												$params['CUSTOMERADDRESSLINE3']
											))
								->setCity($params["CUSTOMERADDRESSCITY"])
								->setRegion($params["CUSTOMERADDRESSSTATE"])
								->setPostcode($params["CUSTOMERADDRESSPOSTCODE"])
								->setCountryId('')
								->setTelephone('')
								->setIsDefaultBilling('1')
								->setIsDefaultShipping('1')
								->setSaveInAddressBook('1')
			    				->save();
			}
			return $customer;
		}
		catch(exception $e){
			throw $e;
		}
	}   

	public function getCustomerByMembershipId($membershipid){
		return Mage::getModel('customer/customer')->getCustomerByMembershipId($membershipid);
	}
		
	public function syncronizeMemberInfo($customerObj){
		$profile = $this->getStargentingProfile();
		
		if(is_object($customerObj) && isset($profile["OUTPUTPARAMS"]))
		{
			$params = $profile["OUTPUTPARAMS"];
			
			try
			{
				$customer = Mage::getModel("customer/customer")->load($customerObj->getData('entity_id'));
				
				if($customer->getId())
				{
					// Update for Customer
					$name = $this->getProfileName();
					$customer->setEmail($params["EMAILADDRESS"]);
					$customer->setFirstname($name['firstname']);
					$customer->setLastname($name['lastname']);
					$customer->setMembershipid($params["CUSTOMERID"]);
					$customer->setDob($this->getFormattedDob());
					$customer->setGroupId(self::STARGENTING_GROUP_ID);
					$customer->setPrefix(ucwords(strtolower($params["CUSTOMERTITLE"])));
					$customer->setMembershipid($params["CUSTOMERID"]);
					$customer->setGender(($params["CUSTOMERGENDER"] == "M" ? 1 : 2));
					$customer->setMembershipid($params["CUSTOMERID"]);
					$customer->setGroupId(self::STARGENTING_GROUP_ID);
					$customer->save();					
										
					$billingaddress = Mage::getModel('customer/address')->load($customer->default_billing);
					if($billingaddress)
					{
						$notGiven = "Not given";
						$street = array();
						for($i=1; $i<=3;$i++){
							if(isset($params['CUSTOMERADDRESSLINE'.$i])){
								$street[] = $params['CUSTOMERADDRESSLINE'.$i] == "" ? $notGiven : $params['CUSTOMERADDRESSLINE'.$i];
							}
						}
						
						if ($params["CUSTOMERADDRESSCITY"]) {
							$city = ucwords(strtolower($params["CUSTOMERADDRESSCITY"]));
						} 
			        
			        	$country = $params["CUSTOMERADDRESSCOUNTRY"];
						
			        	if ($params["CUSTOMERADDRESSSTATE"]) {
			        		$state = ucwords(strtolower($params["CUSTOMERADDRESSSTATE"]));
			        	} 
						
			        	if ($params["CUSTOMERREGIONCODE"]) {
			        		$zipCode = $params["CUSTOMERREGIONCODE"];
			        	} 
						
						if($params["CONTACT"]["CONTACTSECTION"]["CUST_CONTACT"][0]) {
							foreach($params["CONTACT"]["CONTACTSECTION"]["CUST_CONTACT"] as $number) {
								if($number["BESTFLAG"] == 'True' ) {
									$telephone = $number["CONTACTNO"];
								} else {
									$mobileNumber = $number["CONTACTNO"];
								}
							}
						} else {
							foreach($params["CONTACT"]["CONTACTSECTION"] as $number) {
								if($number["BESTFLAG"] == 'True' ) {
									$telephone = $number["CONTACTNO"];
								} else {
									$mobileNumber = $number["CONTACTNO"];
								}
							}
						}
				
						
						$city = $city == "" ? $notGiven : $city;
						$state = $state == "" ? $notGiven : $state;
						$zipCode = $zipCode == "" ? $notGiven : $zipCode;
						$mobileNumber = $mobileNumber == "" ? $notGiven : $mobileNumber;
						$telephone = $telephone == "" ? $notGiven : $telephone;
						
						$billingaddress->setPrefix($customer->getPrefix());
						$billingaddress->setFirstname($customer->getFirstname());
						$billingaddress->setLastname($customer->getLastname());
						$billingaddress->setStreet($street);
						$billingaddress->setCity($city);
						$billingaddress->setRegion($state);
						$billingaddress->setPostcode($zipCode);
						$billingaddress->setCountryId($this->getCountryId($country));
						$billingaddress->setTelephone($telephone);
						$billingaddress->setFax($notGiven);				
						$billingaddress->save();
						
						$this->setMobileNumber($mobileNumber);
						$this->setAddress1($street[0]);
						$this->setCustomerPoints($params["VISIBLEENTITLEMENTBALANCE"]);
					}			
				}
	
			}catch(Exception $e){
				throw $e;
			}
		}
		return $customer;
	}
	
	public function getProfileName(){
    	if($this->getIsStarGentingUser())
		{
			$profile = $this->getStargentingProfile();
			
			if(isset($profile["OUTPUTPARAMS"]))
			{
				$customerName = $profile["OUTPUTPARAMS"]["CUSTOMERNAME"];
				preg_match_all("/\[([^()]+)\]/", $customerName, $matches);		
				
				$lastName = (isset($matches[1])) ? $matches[1]: ''; 
				$firstName = preg_split('/\[.*?\]/', $customerName);			
				$fName = implode(" ",$firstName);
				
				return array('firstname'=>ucwords(strtolower($fName)) ,'lastname'=>ucwords(strtolower($lastName[0])));
			}
		}
		return array();
    }
	
	public function getFormattedDob(){
		if($this->getIsStarGentingUser())
		{
			$profile = $this->getStargentingProfile();
			if(isset($profile["OUTPUTPARAMS"]))
			{
				$dob_parts = date_parse_from_format("Ymd", $profile["OUTPUTPARAMS"]["CUSTOMERDATEOFBIRTH"]);		
				return sprintf('%s-%s-%s',$dob_parts['year'],$dob_parts['month'],$dob_parts['day']);
			}
		}
		return '';
	}
	
	
	public function getCountryId($country)
    {
    	$countries = array("AFGHANISTAN" => "AF",
"ÅLAND ISLANDS" => "AX",
"ALBANIA" => "AL",
"ALGERIA" => "DZ",
"AMERICAN SAMOA" => "AS",
"ANDORRA" => "AD",
"ANGOLA" => "AO",
"ANGUILLA" => "AI",
"ANTARCTICA" => "AQ",
"ANTIGUA AND BARBUDA" => "AG",
"ARGENTINA" => "AR",
"ARMENIA" => "AM",
"ARUBA" => "AW",
"AUSTRALIA" => "AU",
"AUSTRIA" => "AT",
"AZERBAIJAN" => "AZ",
"BAHAMAS" => "BS",
"BAHRAIN" => "BH",
"BANGLADESH" => "BD",
"BARBADOS" => "BB",
"BELARUS" => "BY",
"BELGIUM" => "BE",
"BELIZE" => "BZ",
"BENIN" => "BJ",
"BERMUDA" => "BM",
"BHUTAN" => "BT",
"BOLIVIA, PLURINATIONAL STATE OF" => "BO",
"BONAIRE, SINT EUSTATIUS AND SABA" => "BQ",
"BOSNIA AND HERZEGOVINA" => "BA",
"BOTSWANA" => "BW",
"BOUVET ISLAND" => "BV",
"BRAZIL" => "BR",
"BRITISH INDIAN OCEAN TERRITORY" => "IO",
"BRUNEI DARUSSALAM" => "BN",
"BULGARIA" => "BG",
"BURKINA FASO" => "BF",
"BURUNDI" => "BI",
"CAMBODIA" => "KH",
"CAMEROON" => "CM",
"CANADA" => "CA",
"CAPE VERDE" => "CV",
"CAYMAN ISLANDS" => "KY",
"CENTRAL AFRICAN REPUBLIC" => "CF",
"CHAD" => "TD",
"CHILE" => "CL",
"CHINA" => "CN",
"CHRISTMAS ISLAND" => "CX",
"COCOS (KEELING) ISLANDS" => "CC",
"COLOMBIA" => "CO",
"COMOROS" => "KM",
"CONGO" => "CG",
"CONGO, THE DEMOCRATIC REPUBLIC OF THE" => "CD",
"COOK ISLANDS" => "CK",
"COSTA RICA" => "CR",
"CÔTE D'IVOIRE" => "CI",
"CROATIA" => "HR",
"CUBA" => "CU",
"CURAÇAO" => "CW",
"CYPRUS" => "CY",
"CZECH REPUBLIC" => "CZ",
"DENMARK" => "DK",
"DJIBOUTI" => "DJ",
"DOMINICA" => "DM",
"DOMINICAN REPUBLIC" => "DO",
"ECUADOR" => "EC",
"EGYPT" => "EG",
"EL SALVADOR" => "SV",
"EQUATORIAL GUINEA" => "GQ",
"ERITREA" => "ER",
"ESTONIA" => "EE",
"ETHIOPIA" => "ET",
"FALKLAND ISLANDS (MALVINAS)" => "FK",
"FAROE ISLANDS" => "FO",
"FIJI" => "FJ",
"FINLAND" => "FI",
"FRANCE" => "FR",
"FRENCH GUIANA" => "GF",
"FRENCH POLYNESIA" => "PF",
"FRENCH SOUTHERN TERRITORIES" => "TF",
"GABON" => "GA",
"GAMBIA" => "GM",
"GEORGIA" => "GE",
"GERMANY" => "DE",
"GHANA" => "GH",
"GIBRALTAR" => "GI",
"GREECE" => "GR",
"GREENLAND" => "GL",
"GRENADA" => "GD",
"GUADELOUPE" => "GP",
"GUAM" => "GU",
"GUATEMALA" => "GT",
"GUERNSEY" => "GG",
"GUINEA" => "GN",
"GUINEA-BISSAU" => "GW",
"GUYANA" => "GY",
"HAITI" => "HT",
"HEARD ISLAND AND MCDONALD ISLANDS" => "HM",
"HOLY SEE (VATICAN CITY STATE)" => "VA",
"HONDURAS" => "HN",
"HONG KONG" => "HK",
"HUNGARY" => "HU",
"ICELAND" => "IS",
"INDIA" => "IN",
"INDONESIA" => "ID",
"IRAN, ISLAMIC REPUBLIC OF" => "IR",
"IRAQ" => "IQ",
"IRELAND" => "IE",
"ISLE OF MAN" => "IM",
"ISRAEL" => "IL",
"ITALY" => "IT",
"JAMAICA" => "JM",
"JAPAN" => "JP",
"JERSEY" => "JE",
"JORDAN" => "JO",
"KAZAKHSTAN" => "KZ",
"KENYA" => "KE",
"KIRIBATI" => "KI",
"KOREA, DEMOCRATIC PEOPLE'S REPUBLIC OF" => "KP",
"KOREA, REPUBLIC OF" => "KR",
"KUWAIT" => "KW",
"KYRGYZSTAN" => "KG",
"LAO PEOPLE'S DEMOCRATIC REPUBLIC" => "LA",
"LATVIA" => "LV",
"LEBANON" => "LB",
"LESOTHO" => "LS",
"LIBERIA" => "LR",
"LIBYA" => "LY",
"LIECHTENSTEIN" => "LI",
"LITHUANIA" => "LT",
"LUXEMBOURG" => "LU",
"MACAO" => "MO",
"MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF" => "MK",
"MADAGASCAR" => "MG",
"MALAWI" => "MW",
"MALAYSIA" => "MY",
"MALDIVES" => "MV",
"MALI" => "ML",
"MALTA" => "MT",
"MARSHALL ISLANDS" => "MH",
"MARTINIQUE" => "MQ",
"MAURITANIA" => "MR",
"MAURITIUS" => "MU",
"MAYOTTE" => "YT",
"MEXICO" => "MX",
"MICRONESIA, FEDERATED STATES OF" => "FM",
"MOLDOVA, REPUBLIC OF" => "MD",
"MONACO" => "MC",
"MONGOLIA" => "MN",
"MONTENEGRO" => "ME",
"MONTSERRAT" => "MS",
"MOROCCO" => "MA",
"MOZAMBIQUE" => "MZ",
"MYANMAR" => "MM",
"NAMIBIA" => "NA",
"NAURU" => "NR",
"NEPAL" => "NP",
"NETHERLANDS" => "NL",
"NEW CALEDONIA" => "NC",
"NEW ZEALAND" => "NZ",
"NICARAGUA" => "NI",
"NIGER" => "NE",
"NIGERIA" => "NG",
"NIUE" => "NU",
"NORFOLK ISLAND" => "NF",
"NORTHERN MARIANA ISLANDS" => "MP",
"NORWAY" => "NO",
"OMAN" => "OM",
"PAKISTAN" => "PK",
"PALAU" => "PW",
"PALESTINIAN TERRITORY, OCCUPIED" => "PS",
"PANAMA" => "PA",
"PAPUA NEW GUINEA" => "PG",
"PARAGUAY" => "PY",
"PERU" => "PE",
"PHILIPPINES" => "PH",
"PITCAIRN" => "PN",
"POLAND" => "PL",
"PORTUGAL" => "PT",
"PUERTO RICO" => "PR",
"QATAR" => "QA",
"RÉUNION" => "RE",
"ROMANIA" => "RO",
"RUSSIAN FEDERATION" => "RU",
"RWANDA" => "RW",
"SAINT BARTHÉLEMY" => "BL",
"SAINT HELENA, ASCENSION AND TRISTAN DA CUNHA" => "SH",
"SAINT KITTS AND NEVIS" => "KN",
"SAINT LUCIA" => "LC",
"SAINT MARTIN (FRENCH PART)" => "MF",
"SAINT PIERRE AND MIQUELON" => "PM",
"SAINT VINCENT AND THE GRENADINES" => "VC",
"SAMOA" => "WS",
"SAN MARINO" => "SM",
"SAO TOME AND PRINCIPE" => "ST",
"SAUDI ARABIA" => "SA",
"SENEGAL" => "SN",
"SERBIA" => "RS",
"SEYCHELLES" => "SC",
"SIERRA LEONE" => "SL",
"SINGAPORE" => "SG",
"SINT MAARTEN (DUTCH PART)" => "SX",
"SLOVAKIA" => "SK",
"SLOVENIA" => "SI",
"SOLOMON ISLANDS" => "SB",
"SOMALIA" => "SO",
"SOUTH AFRICA" => "ZA",
"SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS" => "GS",
"SOUTH SUDAN" => "SS",
"SPAIN" => "ES",
"SRI LANKA" => "LK",
"SUDAN" => "SD",
"SURINAME" => "SR",
"SVALBARD AND JAN MAYEN" => "SJ",
"SWAZILAND" => "SZ",
"SWEDEN" => "SE",
"SWITZERLAND" => "CH",
"SYRIAN ARAB REPUBLIC" => "SY",
"TAIWAN" => "TW",
"TAJIKISTAN" => "TJ",
"TANZANIA, UNITED REPUBLIC OF" => "TZ",
"THAILAND" => "TH",
"TIMOR-LESTE" => "TL",
"TOGO" => "TG",
"TOKELAU" => "TK",
"TONGA" => "TO",
"TRINIDAD AND TOBAGO" => "TT",
"TUNISIA" => "TN",
"TURKEY" => "TR",
"TURKMENISTAN" => "TM",
"TURKS AND CAICOS ISLANDS" => "TC",
"TUVALU" => "TV",
"UGANDA" => "UG",
"UKRAINE" => "UA",
"UNITED ARAB EMIRATES" => "AE",
"UNITED KINGDOM" => "GB",
"UNITED STATES" => "US",
"UNITED STATES MINOR OUTLYING ISLANDS" => "UM",
"URUGUAY" => "UY",
"UZBEKISTAN" => "UZ",
"VANUATU" => "VU",
"VENEZUELA, BOLIVARIAN REPUBLIC OF" => "VE",
"VIET NAM" => "VN",
"VIRGIN ISLANDS, BRITISH" => "VG",
"VIRGIN ISLANDS, U.S." => "VI",
"WALLIS AND FUTUNA" => "WF",
"WESTERN SAHARA" => "EH",
"YEMEN" => "YE",
"ZAMBIA" => "ZM",
"ZIMBABWE" => "ZW");
		return $countries[$country];
    }
}
