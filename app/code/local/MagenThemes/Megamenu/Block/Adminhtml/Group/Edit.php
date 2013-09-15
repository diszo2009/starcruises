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
class MagenThemes_Megamenu_Block_Adminhtml_Group_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'megamenu';
        $this->_controller = 'adminhtml_group';
        
        $this->_updateButton('save', 'label', Mage::helper('megamenu')->__('Save Menu'));
        $this->_updateButton('delete', 'label', Mage::helper('megamenu')->__('Delete Menu'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('megamenu_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'megamenu_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'megamenu_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('megamenu_group_data') && Mage::registry('megamenu_group_data')->getId() ) {
            return Mage::helper('megamenu')->__("Edit Menu '%s'", $this->htmlEscape(Mage::registry('megamenu_group_data')->getTitle()));
        } else {
            return Mage::helper('megamenu')->__('Add Menu');
        }
    }
}