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
class MagenThemes_Megamenu_Block_Adminhtml_Megamenu_Edit_Tab_Page extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $_model = Mage::registry('megamenu_data');
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('page_form', array('legend'=>Mage::helper('megamenu')->__('Megamenu Pages')));
        $fieldset->addField('block_id', 'select', array(
            'label'     => Mage::helper('megamenu')->__('Show Page'),
            'required'  => false,
            'name'      => 'megamenu[block_id]',
            'values'    => $this->_pageOptions(),
            'value'     => $_model->getBlockId()
        ));
        
        return parent::_prepareForm();
    }
    
    private function _pageOptions()
    {
        $_collection = Mage::getSingleton('cms/block')->getCollection()
                ->addFieldToFilter('is_active', 1);

        $_result = array();
        $_result[] = array('value' => '', 'label' => '');
        foreach ($_collection as $item) {
            $data = array(
                'value' => $item->getData('block_id'),
                'label' => $item->getData('title'));
            $_result[] = $data;
        }
        return $_result;
    }
}