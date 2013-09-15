<?php 

class Movent_Starpay_Block_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    { 	 
		$starpay = Mage::getModel('starpay/method_starccsave');

        $form = new Varien_Data_Form();
        $form->setAction($starpay->getConfig()->getStarPayUrl())
            ->setId('starcruise_standard_checkout')
            ->setName('starcruise_standard_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
        foreach ($starpay->getCheckoutFormFields() as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }
        $idSuffix = Mage::helper('core')->uniqHash();
        $submitButton = new Varien_Data_Form_Element_Submit(array(
            'value'    => $this->__('Click here if you are not redirected within 10 seconds...'),
        ));
        $id = "submit_to_starpay_button_{$idSuffix}";
        $submitButton->setId($id);
        $form->addElement($submitButton);
        $html = '<html><body>';
        $html.= $this->__('You will be redirected to payment gateway in a few seconds.');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("starcruise_standard_checkout").submit();</script>';
        $html.= '</body></html>';
		
		/*
		foreach($starpay->getCheckoutFormFields() as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			rtrim($fields_string, '&');

		echo $param = rtrim(($starpay->getConfig()->getStarPayUrl().'?'.$fields_string),'&');
		die('x');
		        $html = '<html><body>';
        $html.= $this->__('You will be redirected to payment gateway in a few seconds.'); 
        $html.= '<script type="text/javascript">window.location=\''.$param.'\';</script>';
        $html.= '</body></html>';*/
		

        return $html;
    }
}
