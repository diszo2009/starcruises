<?php
/******************************************************
 * @package Megamenu module for Magento 1.4.x.x and Magento 1.5.x.x
 * @version 1.5.0.4
 * @author http://www.9magentothemes.com
 * @copyright (C) 2011- 9MagentoThemes.Com
 * @license PHP files are GNU/GPL
*******************************************************/
?>
<?php
class MagenThemes_Megamenu_Block_System_Config_Information extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {		
	$html = $this->_getHeaderHtml($element);		
	$html.= $this->_getFieldHtml($element);        
        $html .= $this->_getFooterHtml($element);
        return $html;
    }
    
    protected function _getFieldHtml($fieldset)
    {
	$content = 'Megamenu version : 2.0.2<br/>Author : <a href="http://www.9magentothemes.com" title="Magento Themes">9MagentoThemes.Com</a><br />Copyright &copy; 2011- 9MagentoThemes.Com';
	return $content;
    }
}