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
class MagenThemes_Megamenu_Block_Adminhtml_Megamenu_Tree extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('megamenu/megamenu/tree.phtml');
        $this->setUseAjax(true);
    }
    
    public function getStore()
    {
    	if($storeId = (int) $this->getRequest()->getParam('storeId'))
    	    return $storeId;
	elseif(Mage::getSingleton('admin/session')->getLastViewedStore())
	    return Mage::getSingleton('admin/session')->getLastViewedStore();
    	return $this->_getDefaultStoreId();
    }
    
    public function getRoot()
    {
        $root = Mage::registry('root');
        if (is_null($root)) {
            $storeId = (int) $this->getRequest()->getParam('store');
	    $rootIds = null;
            if ($storeId) {
                $rootIds = Mage::getModel('megamenu/megamenu')->getRootId($storeId);
            } else {
		$rootIds = Mage::getModel('megamenu/megamenu')->getRootId(0);
	    }
	    $root = array();
	    foreach($rootIds as $rootId) {
		$root[] = Mage::getModel('megamenu/megamenu')->load($rootId);
	    }

            Mage::register('root', $root);
        }
        return $root;
    }
    
    protected function _prepareLayout()
    {
	$this->setChild('import_category_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Import Categories'),
                    'onclick'   => "importCategories('".$this->getUrl('*/*/importCategories')."')",
                    'id'        => 'add_submegamenu_button'
                ))
        );
	
        $this->setChild('add_sub_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Add Sub mega menu'),
                    'onclick'   => "addNew('".$this->getUrl('*/*/edit')."', false)",
                    'class'     => 'add',
                    'id'        => 'add_submegamenu_button'
                ))
        );
	
	$this->setChild('add_root_button',
	    $this->getLayout()->createBlock('adminhtml/widget_button')
		->setData(array(
		    'label'     => Mage::helper('catalog')->__('Add Root Megamenu'),
		    'onclick'   => "addNew('".$this->getUrl('*/*/edit')."', true)",
		    'class'     => 'add',
		    'id'        => 'add_root_megamenu_button'
		))
	);

        $this->setChild('store_switcher',
            $this->getLayout()->createBlock('adminhtml/store_switcher')
                ->setSwitchUrl($this->getUrl('*/*/*'))
                ->setTemplate('megamenu/store/switcher.phtml')
        );
        return parent::_prepareLayout();
    }
    
    protected function _getDefaultStoreId()
    {
        return MagenThemes_Megamenu_Model_Abtract::DEFAULT_STORE_ID;
    }

    public function getMegamenuCollection()
    {
        $storeId = $this->getRequest()->getParam('store', $this->_getDefaultStoreId());
        $collection = $this->getData('megamenu_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('megamenu/megamenu')->getCollection();

           	$collection->setStoreFilter($storeId);

            $this->setData('megamenu_collection', $collection);
        }
        return $collection;
    }
    
    public function getImportCategoriesButtonHtml()
    {
	return $this->getChildHtml('import_category_button');
    }

    public function getAddRootButtonHtml()
    {
        return $this->getChildHtml('add_root_button');
    }

    public function getAddSubButtonHtml()
    {
        return $this->getChildHtml('add_sub_button');
    }

    public function getExpandButtonHtml()
    {
        return $this->getChildHtml('expand_button');
    }

    public function getCollapseButtonHtml()
    {
        return $this->getChildHtml('collapse_button');
    }

    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }
    
    public function getTreeHtml() {
	return '<ul id="single-tree" class="simpleTree">'
	    .Mage::getModel('megamenu/megamenu')->renderTree(null, 0, $this->getRequest()->getParam('id'), $this->getStore())
	    .'</ul>'
	    .'<script type="text/javascript">'
		.'//<![CDATA['."\n"
		    .'var simpleTreeCollection = $jMega("#single-tree").simpleTree({'
			.'animate: true,'
			.'docToFolderConvert: true,'
			.'spaceImage: "'.$this->getSkinUrl('magenthemes/megamenu/images/spacer.gif').'",'
			.'minusImage: "'.$this->getSkinUrl('magenthemes/megamenu/images/minus.gif').'",'
			.'plusImage: "'.$this->getSkinUrl('magenthemes/megamenu/images/plus.gif').'",'
			.'afterClick: function(node) {'
			    .'var id = node.attr("id");'
			    .'var storeId = '.$this->getStore().';'
			    .'afterClick(id, storeId);'
			.'},'
			.'afterMove: function(destination, source, pos) {'
			    .'var parentId = destination.attr("id");'
			    .'var moveId = source.attr("id");'
			    .'afterMove(parentId, moveId);'
			.'},'
		    .'});'."\n"
		.'//]]>'
	    .'</script>';
    }

    /**
     * Retrieve currently edited product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getMegamenu()
    {
        return Mage::registry('current_megamenu');
    }
}