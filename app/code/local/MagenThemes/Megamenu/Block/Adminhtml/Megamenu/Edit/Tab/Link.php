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
class MagenThemes_Megamenu_Block_Adminhtml_Megamenu_Edit_Tab_Link extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $_model = Mage::registry('megamenu_data');
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('link_form', array('legend'=>Mage::helper('megamenu')->__('Megamenu Link')));
        $fieldset->addField('link', 'text', array(
          'label'     => Mage::helper('megamenu')->__('Link'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'megamenu[link]',
          'class'     => 'validate-url',
          'value'     => $_model->getLink()
        ));
        
        $fieldset->addField('target', 'select', array(
          'label'     => Mage::helper('megamenu')->__('Status'),
          'name'      => 'megamenu[target]',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('megamenu')->__('_self'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('megamenu')->__('_blank'),
              ),
              
              array(
                    'value'   => 3,
                    'label'   => Mage::helper('megamenu')->__('_parent')
              ),
              
              array(
                    'value'   => 4,
                    'label'   => Mage::helper('megamenu')->__('_top')
              ),
          ),
          'value'   => $_model->getTarget()
      ));
        
        return parent::_prepareForm();
    }
}