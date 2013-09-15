<?php 
class Movent_Starpay_Block_Form_Starccsave extends Mage_Payment_Block_Form_Cc
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/form/starccsave.phtml');
    }
	
	/**
     * Retrieve credit card expire Years
     *
     * @return array
     */
    public function getCcYears()
    {  
		$years = $this->getData('cc_years');
        if (is_null($years)) {
            $years = Mage::getModel('starpay/config')->getYears();
            $years = array(0=>$this->__('Year'))+$years;
            $this->setData('cc_years', $years);
        }
        return $years;
    }

}
