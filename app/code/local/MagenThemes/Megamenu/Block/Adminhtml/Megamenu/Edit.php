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
class MagenThemes_Megamenu_Block_Adminhtml_Megamenu_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
	$this->_blockGroup  = 'megamenu';
        $this->_objectId    = 'megamenu_id';
        $this->_controller  = 'adminhtml_megamenu';
        $this->_mode        = 'edit';

        parent::__construct();
        $this->setTemplate('megamenu/megamenu/edit.phtml');
    }

    protected function _prepareLayout()
    {
        /*$category = Mage::registry('current_category');
        if (Mage::app()->getConfig()->getModuleConfig('Mage_GoogleOptimizer')->is('active', true)
            && Mage::helper('googleoptimizer')->isOptimizerActive($category->getStoreId())) {
            $this->setChild('googleoptimizer_js',
                $this->getLayout()->createBlock('googleoptimizer/js')->setTemplate('googleoptimizer/js.phtml')
            );
        }*/
        return parent::_prepareLayout();
    }
}