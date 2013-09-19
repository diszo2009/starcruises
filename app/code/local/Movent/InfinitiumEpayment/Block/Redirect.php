<?php 
/*
 * Redirect
 * 
 * @date: 2013-09-19
 * @author: diszo.sasil (diszo.sasil@movent.com) - Movent Inc.
 */
class Movent_InfinitiumEpayment_Block_Redirect extends Mage_Core_Block_Abstract
{
	
	const INFINITIUM_EPAYMENT_FORM = 'infinitium_epayment_form_checkout';
	
    protected function _toHtml()
    { 	 
		$config = Mage::getModel('infinitiumepayment/config')->getInfinitiumEpaymentConfig();

        $form = new Varien_Data_Form();
        $form->setAction($config->getInfinitiumEpaymentUrl())
            ->setId(self::INFINITIUM_EPAYMENT_FORM)
            ->setName(self::INFINITIUM_EPAYMENT_FORM)
            ->setMethod('POST')
            ->setUseContainer(true);
			
        foreach ($config->getMethodHandler()->getCheckoutFormFields() as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }
        $idSuffix = Mage::helper('core')->uniqHash();
        $submitButton = new Varien_Data_Form_Element_Submit(array(
            'value'    => $this->__('Click here if you are not redirected within 10 seconds...'),
        ));
        $id = "submit_to_infinitiumepayment_button_{$idSuffix}";
        $submitButton->setId($id);
        $form->addElement($submitButton);
        $html = '<html><body>';
        $html.= $this->__('You will be redirected to payment gateway in a few seconds.');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("'.self::INFINITIUM_EPAYMENT_FORM.'").submit();</script>';
        $html.= '</body></html>';

        return $html;
    }
}
