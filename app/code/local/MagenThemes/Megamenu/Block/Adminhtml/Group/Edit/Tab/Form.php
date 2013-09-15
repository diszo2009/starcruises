<?php
class MagenThemes_Megamenu_Block_Adminhtml_Group_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareLayout()
  {
      parent::_prepareLayout();
      if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
		$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
      }
  }
  
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('megamenu_group_form', array('legend'=>Mage::helper('megamenu')->__('Group information')));
	  
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('megamenu')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'group[title]',
      ));
	  
	  $fieldset->addField('position', 'select', array(
          'label'     => Mage::helper('megamenu')->__('Position'),
		  'required'  => true,
          'name'      => 'group[position]',
          'values'    => Mage::getModel('megamenu/resources_position')->toOptionArray()
      ));
	  
	  if (!Mage::app()->isSingleStoreMode()) {
		  $fieldset->addField('stores', 'multiselect', array(
			  'label'     => Mage::helper('megamenu')->__('Visible In'),
			  'required'  => true,
			  'name'      => 'group[stores][]',
			  'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
			  'value'     => 'stores'
		  ));
	  }
	  else {
		  $fieldset->addField('stores', 'hidden', array(
			  'name'      => 'group[stores][]',
			  'value'     => Mage::app()->getStore(true)->getId()
		  ));
	  }
	  	
      $fieldset->addField('description', 'editor', array(
          'name'      => 'group[description]',
          'label'     => Mage::helper('megamenu')->__('Description'),
          'title'     => Mage::helper('megamenu')->__('Description'),
          'style'     => 'width:500px; height:200px;',
          'required'  => false,
      ));
      
	  $fieldset->addField('sort_order', 'text', array(
          'label'     => Mage::helper('megamenu')->__('Sort Order'),
          'required'  => false,
          'name'      => 'group[sort_order]',
      ));
	    
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('megamenu')->__('Status'),
          'name'      => 'group[status]',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('megamenu')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('megamenu')->__('Disabled'),
              ),
          ),
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getMegamenuGroupData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getMegamenuGroupData());
          Mage::getSingleton('adminhtml/session')->setMegamenuGroupData(null);
      } elseif ( Mage::registry('megamenu_group_data') ) {
          $form->setValues(Mage::registry('megamenu_group_data')->getData());
      }
      return parent::_prepareForm();
  }
}