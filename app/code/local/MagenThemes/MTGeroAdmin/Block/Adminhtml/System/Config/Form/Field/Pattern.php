<?php
class MagenThemes_MTGeroAdmin_Block_Adminhtml_System_Config_Form_Field_Pattern extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){ 
       	$html = parent::_getElementHtml($element);
		$html .= '<div id="bg_'.$element->getHtmlId().'" style="height: 100px; width: 280px;"></div>';
       	$html .= ' 
       		<script type="text/javascript"> 
				$mtkb(document).ready(function(){ 
					var valuepat = $mtkb("#'.$element->getHtmlId().'").val();
					activePattern'.$element->getHtmlId().'(valuepat); 
				});
				$mtkb("#'.$element->getHtmlId().'").bind("change", function() { 
					activePattern'.$element->getHtmlId().'($mtkb(this).val());
				});
				function activePattern'.$element->getHtmlId().'(patternactive){ 
					$mtkb("#bg_'.$element->getHtmlId().'").css("background","url('. Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/default/mt_gero/images/pattern/"+patternactive+".png) repeat");
				}
		    </script> 
       	'; 
        return $html;
    }
}
?>