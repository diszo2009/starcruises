<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.4.0_2.0.3_467520
 * Purchase ID: JlNeBKBvSqsIsT7whc80ZpBA38zH86mwW38f4D7p5H
 * Generated:   2013-01-14 03:56:09
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminhtmlCatalogFormRendererFieldsetElement.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ ZsZhaSqghCMDkZar('3fb8b38f4c5e9a95462f1c7d826f82cc'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminhtmlCatalogFormRendererFieldsetElement
    extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    public function checkFieldDisable()
    {        
        $result = parent::checkFieldDisable();
        $superGlobalAttribute = array('sku', 'weight');
        $currentProduct = Mage::registry('current_product');
        $bAllow = !$currentProduct || !$currentProduct->getId() || !$currentProduct->getSku();

        if ($bAllow && 
            $this->getElement() &&
            $this->getElement()->getEntityAttribute() &&
            in_array($this->getElement()->getEntityAttribute()->getAttributeCode(), $superGlobalAttribute))
        {
            return $result;
        }
        
        if ($this->getElement() && 
            $this->getElement()->getEntityAttribute() &&
            $this->getElement()->getEntityAttribute()->isScopeGlobal())
        {
            $role = Mage::getSingleton('aitpermissions/role');

            if ($role->isPermissionsEnabled() && !$role->canEditGlobalAttributes())
            {                
                $this->getElement()->setDisabled(true);
                $this->getElement()->setReadonly(true);
                $afterHtml = $this->getElement()->getAfterElementHtml();
                if (false !== strpos($afterHtml, 'type="checkbox"'))
                {
                    $afterHtml = str_replace('type="checkbox"', 'type="checkbox" disabled="disabled"', $afterHtml);
                    $this->getElement()->setAfterElementHtml($afterHtml);
                }
            }            
        }        
        
        return $result;
    }
    
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = parent::render($element);
        if ($this->getElement() && 
            $this->getElement()->getEntityAttribute() &&
            $this->getElement()->getEntityAttribute()->isScopeGlobal())
        {
            $role = Mage::getSingleton('aitpermissions/role');

            if ($role->isPermissionsEnabled() &&
                !$role->canEditGlobalAttributes() &&
                ('msrp' == $this->getElement()->getHtmlId()))
            {
                 $html .= '
                    <script type="text/javascript">
                    //<![CDATA[
                    if (Prototype.Browser.IE)
                    {
                        if (window.addEventListener)
                        {
                            window.addEventListener("load", aitpermissions_disable_msrp, false);
                        }
                        else
                        {
                            window.attachEvent("onload", aitpermissions_disable_msrp);
                        }
                    }
                    else
                    {
                        document.observe("dom:loaded", aitpermissions_disable_msrp);
                    }

                    function aitpermissions_disable_msrp()
                    {
                        ["click", "focus", "change"].each(function(evt){
                            var msrp = $("msrp");
                            if (msrp && !msrp.disabled)
                            {
                                Event.observe(msrp, evt, function(el) {
                                    el.disabled = true;
                                }.curry(msrp));
                            }
                        });
                    }
                    //]]>
                    </script>';
            }
            
            if (!$role->canEditGlobalAttributes())
            {
                $html = str_replace(
                    '<script type="text/javascript">toggleValueElements(',
                    '<script type="text/javascript">//toggleValueElements(',
                    $html
                );
            }
        }
        
        return $html;
    }
} } 