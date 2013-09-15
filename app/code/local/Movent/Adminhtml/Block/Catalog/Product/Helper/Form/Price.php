<?php

class Movent_Adminhtml_Block_Catalog_Product_Helper_Form_Price extends Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Price
{

 
    public function getAfterElementHtml()
    {
        $html = parent::getAfterElementHtml();
        /**
         * getEntityAttribute - use __call
         */
        $addJsObserver = false;
        if ($attribute = $this->getEntityAttribute()) {
            if (!($storeId = $attribute->getStoreId())) {
                $storeId = $this->getForm()->getDataObject()->getStoreId();
            }
            $store = Mage::app()->getStore($storeId);
            //$html.= '<strong>['.(string)$store->getBaseCurrencyCode().']</strong>';
            if (Mage::helper('tax')->priceIncludesTax($store)) {
                if ($attribute->getAttributeCode()!=='cost') {
                    $addJsObserver = true;
                    $html.= ' <strong>['.Mage::helper('tax')->__('Inc. Tax').'<span id="dynamic-tax-'.$attribute->getAttributeCode().'"></span>]</strong>';
                }
            }
        }
        if ($addJsObserver) {
            $html .= $this->_getTaxObservingCode($attribute);
        }

        return $html;
    }

   
}
