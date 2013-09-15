<?php 

class Movent_Checkout_Block_Onepage_Billing extends Mage_Checkout_Block_Onepage_Billing
{
    /**
     * Sales Qoute Billing Address instance
     *
     * @var Mage_Sales_Model_Quote_Address
     */
    protected $_address;

    /**
     * Customer Taxvat Widget block
     *
     * @var Mage_Customer_Block_Widget_Taxvat
     */
    protected $_taxvat;

    /**
     * Initialize billing address step
     *
     */
    protected function _construct()
    {
        $this->getCheckout()->setStepData('billing', array(
            'label'     => Mage::helper('checkout')->__('Customer Information'),
            'is_show'   => $this->isShow()
        ));

        if ($this->isCustomerLoggedIn()) {
            $this->getCheckout()->setStepData('billing', 'allow', true);
        }
        parent::_construct();
    }

    public function isUseBillingAddressForShipping()
    {
        if (($this->getQuote()->getIsVirtual())
            || !$this->getQuote()->getShippingAddress()->getSameAsBilling()) {
            return false;
        }
        return true;
    }

    /**
     * Return country collection
     *
     * @return Mage_Directory_Model_Mysql4_Country_Collection
     */
    public function getCountries()
    {
        return Mage::getResourceModel('directory/country_collection')->loadByStore();
    }

    /**
     * Return checkout method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getQuote()->getCheckoutMethod();
    }
	
	public function getCustomerSession()
	{
		return Mage::getSingleton('customer/session');
	}

    /**
     * Return Sales Quote Address model
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {    	
        // regular User	
        if (is_null($this->_address)) {
            if ($this->isCustomerLoggedIn()) {
                $this->_address = $this->getQuote()->getBillingAddress();
                if(!$this->_address->getFirstname()) {
                    $this->_address->setFirstname($this->getQuote()->getCustomer()->getFirstname());
                }
                if(!$this->_address->getLastname()) {
                    $this->_address->setLastname($this->getQuote()->getCustomer()->getLastname());
                }
            } else {
                $this->_address = Mage::getModel('sales/quote_address');
            }
        }
			
		if ($this->getCustomerSession()->getIsStarGentingUser())
		{
			// Auto updated the points/address everytime you login
			
			// get latest points
			//Mage::getModel('custom/service')->updatePoints();
			Mage::getModel('custom/service')->getStargentingPoints();
        }

        return $this->_address;
    }

    /**
     * Return Customer Address First Name
     * If Sales Quote Address First Name is not defined - return Customer First Name
     *
     * @return string
     */
    public function getFirstname()
    {
        $firstname = $this->getAddress()->getFirstname();
        if (empty($firstname) && $this->getQuote()->getCustomer()) {
            return $this->getQuote()->getCustomer()->getFirstname();
        }
        return $firstname;
    }

    /**
     * Return Customer Address Last Name
     * If Sales Quote Address Last Name is not defined - return Customer Last Name
     *
     * @return string
     */
    public function getLastname()
    {
        $lastname = $this->getAddress()->getLastname();
        if (empty($lastname) && $this->getQuote()->getCustomer()) {
            return $this->getQuote()->getCustomer()->getLastname();
        }
        return $lastname;
    }

    /**
     * Check is Quote items can ship to
     *
     * @return boolean
     */
    public function canShip()
    {
        return !$this->getQuote()->isVirtual();
    }

    public function getSaveUrl()
    {
    }

    /**
     * Get Customer Taxvat Widget block
     *
     * @return Mage_Customer_Block_Widget_Taxvat
     */
    protected function _getTaxvat()
    {
        if (!$this->_taxvat) {
            $this->_taxvat = $this->getLayout()->createBlock('customer/widget_taxvat');
        }

        return $this->_taxvat;
    }

    /**
     * Check whether taxvat is enabled
     *
     * @return bool
     */
    public function isTaxvatEnabled()
    {
        return $this->_getTaxvat()->isEnabled();
    }

    public function getTaxvatHtml()
    {
        return $this->_getTaxvat()
            ->setTaxvat($this->getQuote()->getCustomerTaxvat())
            ->setFieldIdFormat('billing:%s')
            ->setFieldNameFormat('billing[%s]')
            ->toHtml();
    }
    
     
	public function getNationality() {
		$nationality = array("","Afghan","Albanian","Algerian","American","American Samoan","Andorran","Angolan","Anguillan","Antarctican","Antigua and Barbuda national","Antillean","Argentinian","Armenian","Aruban","Australian","Austrian","Azerbaijani","Bahamian","Bahraini","Bangladeshi","Barbadian","Basotho","Belarusian","Belgian","Belizean","Beninese","Bermudian","Bhutanese","Bolivian","Bosnia and Herzegovina national","Botswanan","Bouvet Island","Brazilian","British Indian Ocean Territory","British Virgin Islander","Briton","Bruneian","Bulgarian","Burkinabe","Burmese","Burundian","Cambodian","Cameroonian","Canadian","Cape Verdean","Caymanian","Central African","Chadian","Chilean","Chinese",
"Christmas Islander","Cocos Islander","Colombian","Comorian","Congolese","Congolese","Cook Islander","Costa Rican","Croat","Cuban","Cypriot","Czech","Dane","Djiboutian","Dominican","East Timorese","Ecuadorian","Egyptian","Emirian","Equatorial Guinean","Eritrean","Estonian","Ethiopian","Faeroese","Falkland Islander","Fiji Islander","Filipino","Finn","French","French Southern Territories","Gabonese","Gambian","Georgian","German","Ghanaian","Gibraltarian","Greek","Greenlander","Grenadian","Guadeloupean","Guamanian","Guatemalan","Guianese","Guinea-Bissau national","Guinean","Guyanese","Haitian","Heard Island and McDonald Islands","Honduran","Hong Kong","Hungarian","Icelander","Indian","Indonesian",
"Iranian","Iraqi","Irish","Israeli","Italian","Ivorian","Jamaican","Japanese","Jordanian","Kazakh","Kenyan","Kiribatian","Kuwaiti","Kyrgyz","Lao","Latvian","Lebanese","Liberian","Libyan","Liechtensteiner","Lithuanian","Luxembourger","Macanese","Macedonian","Mahorais","Malagasy","Malawian","Malaysian","Maldivian","Malian","Maltese","Marshallese","Martinican","Mauritanian","Mauritian","Mexican","Micronesian","Moldovan","Monegasque","Mongolian","Montenegrian","Montserratian","Moroccan","Mozambican","Namibian","Nauruan","Nepalese","Netherlander","New Caledonian","New Zealander","Nicaraguan","Nigerian","Nigerien","Niuean","Norfolk Islander","North Korean","Northern Mariana Islander","Norwegian","Omani",
"Pakistani","Palauan","Panamanian","Papua New Guinean","Paraguayan","Peruvian","Pitcairner","Pole","Polynesian","Portuguese","Puerto Rican","Qatari","Reunionese","Romanian","Russian","Rwandan","Sahrawi","Saint Helenian","Saint Kitts and Nevis national","Saint Lucian","Saint Pierre and Miquelon national","Salvadorian","Samoan","San Marinese","Sao Tomean","Saudi Arabian","Senegalese","Serbian","Seychellois","Sierra Leonean","Singaporean","Slovak","Slovene","Solomon Islander","Somali",
"South African","South Georgia and the South Sandwich Islands","South Korean","Spaniard","Sri Lankan","Sudanese","Surinamer","Svalbard and Jan Mayen","Swazi","Swede","Swiss","Syrian","Taiwanese","Tajik","Tanzanian","Thai","Togolese","Tokelauan","Tongan","Trinidad and Tobago national","Tunisian","Turk","Turkmen","Turks and Caicos Islander","Tuvaluan","Ugandan","Ukrainian","United States Minor Outlying Islands","Uruguayan","US Virgin Islander","Uzbek","Vanuatuan","Vatican","Venezuelan","Vietnamese","Vincentian","Wallis and Futuna Islander","Yemeni","Zambian","Zimbabwean","Aland Islander");
		
		$selected = $this->getCustomer()->getNationality();
		echo "<option value='$selected'>$selected</option>\n";
		foreach($nationality as $option) {
			echo "<option value='$option'>$option</option>\n";
		}
	}

}
