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
class MagenThemes_Megamenu_Block_Adminhtml_Megamenu_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('megamenu_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('megamenu')->__('Megamenu Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('megamenu')->__('Megamenu Information'),
          'title'     => Mage::helper('megamenu')->__('Megamenu Information'),
          'content'   => $this->getLayout()->createBlock('megamenu/adminhtml_megamenu_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}