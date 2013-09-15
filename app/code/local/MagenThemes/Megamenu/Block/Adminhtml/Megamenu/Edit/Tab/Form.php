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
class MagenThemes_Megamenu_Block_Adminhtml_Megamenu_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('megamenu_form', array('legend'=>Mage::helper('megamenu')->__('Megamenu information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('megamenu')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'megamenu[title]',
      ));

      $fieldset->addField('image', 'image', array(
          'label'     => Mage::helper('megamenu')->__('Image'),
          'required'  => false,
          'name'      => 'image',
	  ));
	  
	  if (!Mage::app()->isSingleStoreMode()) {
		  $fieldset->addField('stores', 'multiselect', array(
			  'label'     => Mage::helper('megamenu')->__('Visible In'),
			  'required'  => true,
			  'name'      => 'megamenu[stores][]',
			  'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
			  'value'     => 'stores'
		  ));
	  }
	  else {
		  $fieldset->addField('stores', 'hidden', array(
			  'name'      => 'megamenu[stores][]',
			  'value'     => Mage::app()->getStore(true)->getId()
		  ));
	  }
	  
	  $fieldset->addField('parent_id', 'select', array(
          'label'     => Mage::helper('megamenu')->__('Parent Item'),
          'name'      => 'megamenu[parent_id]',
          'values'    => $this->itemToOptionArray(),
      ));
	  
	  $fieldset->addField('article', 'select', array(
          'label'     => Mage::helper('megamenu')->__('Select Article'),
          'name'      => 'megamenu[article]',
          'values'    => $this->acticleToOptionArray(),
      ));
	  
	  $fieldset->addField('is_group', 'select', array(
          'label'     => Mage::helper('megamenu')->__('Is Group'),
          'name'      => 'megamenu[is_group]',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('megamenu')->__('True'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('megamenu')->__('False'),
              ),
          ),
      ));
	  
	  $fieldset->addField('width', 'text', array(
		  'label'     => Mage::helper('megamenu')->__('Width'),
		  'required'  => false,
		  'name'      => 'megamenu[width]',
	  ));
	  
	  $fieldset->addField('subitem_width', 'textarea', array(
		  'label'     => Mage::helper('megamenu')->__('Submenu Item Width'),
		  'required'  => false,
		  'name'      => 'megamenu[subitem_width]',
	  ));
	  
	  $fieldset->addField('is_content', 'select', array(
		  'label'     => Mage::helper('megamenu')->__('Is Content'),
		  'name'      => 'megamenu[is_content]',
		  'values'    => array(
			  array(
				  'value'     => 1,
				  'label'     => Mage::helper('megamenu')->__('True'),
			  ),

			  array(
				  'value'     => 2,
				  'label'     => Mage::helper('megamenu')->__('False'),
			  ),
		  ),
	  ));
	  
	  $fieldset->addField('show_title', 'select', array(
		  'label'     => Mage::helper('megamenu')->__('Show Title'),
		  'name'      => 'megamenu[show_title]',
		  'values'    => array(
			  array(
				  'value'     => 1,
				  'label'     => Mage::helper('megamenu')->__('True'),
			  ),

			  array(
				  'value'     => 2,
				  'label'     => Mage::helper('megamenu')->__('False'),
			  ),
		  ),
	  ));
	  
	  $fieldset->addField('col', 'text', array(
          'label'     => Mage::helper('megamenu')->__('Col Position'),
          'required'  => false,
          'name'      => 'megamenu[col]',
      ));
	  
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('megamenu')->__('Status'),
          'name'      => 'megamenu[status]',
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
     
      if ( Mage::getSingleton('adminhtml/session')->getMegamenuData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getMegamenuData());
          Mage::getSingleton('adminhtml/session')->setMegamenuData(null);
      } elseif ( Mage::registry('megamenu_data') ) {
          $form->setValues(Mage::registry('megamenu_data')->getData());
      }
      return parent::_prepareForm();
  }
  
  public function itemToOptionArray() {
	$optionArray = array();
	$collection = Mage::getModel('megamenu/megamenu')->getCollection();
	if($this->getRequest()->getParam('id')) {
	  $collection->addFieldToFilter('megamenu_id', array('neq' => $this->getRequest()->getParam('id')));
	}
	$optionArray[] = array('value' => '', 'label' => '');
	foreach($collection as $item) {
	  $level = '...';
	  if($item->getLevel()) {
		for($i = 0;$i < $item->getLevel();$i++) {
		  $level .= '...';
		}
	  }
	  $optionArray[] = array('value' => $item->getId(), 'label' => $level.$item->getTitle());
	}
	return $optionArray;
  }
  
  public function acticleToOptionArray() {
	$option = null;
	if($this->getRequest()->getParam('type')) {
	  $option = $this->getLayout()->getBlock('megamenu.type')->nodeToOptionArray($this->getRequest()->getParam('type'));
	} else {
	  $megamenu = null;
	  if ( Mage::getSingleton('adminhtml/session')->getMegamenuData() ) {
          $megamenu = Mage::getSingleton('adminhtml/session')->getMegamenuData();
      } elseif ( Mage::registry('megamenu_data') ) {
          $megamenu = Mage::registry('megamenu_data')->getData();
      }
	  
	  if($megamenu) {
		$option = $this->getLayout()->getBlock('megamenu.type')->nodeToOptionArray($megamenu['type']);
	  }
	}
	return $option;
  }
}