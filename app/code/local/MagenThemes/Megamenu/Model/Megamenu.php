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
class MagenThemes_Megamenu_Model_Megamenu extends MagenThemes_Megamenu_Model_Abtract
{
	const TREE_ROOT_ID = 1;
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('megamenu/megamenu');
    }
    
    public function isActive() {
        if($this->getStatus() == MagenThemes_Megamenu_Model_Status::STATUS_ENABLED) {
            return true;
        }
        return false;
    }
    
    public function hasChild($statusFilter = false, $storeId = null) {
        $collection = Mage::getModel('megamenu/megamenu')->getCollection()
			->setStoreFilter($storeId)
			->addFieldToFilter('parent_id', $this->getId());
	if($statusFilter) {
		$collection->addStatusFilter();
	}
        if(count($collection))
            return true;
        return false;
    }
    
    public function getChildItem($col=null, $storeId = null) {
        $collection = Mage::getModel('megamenu/megamenu')->getCollection()
			->setStoreFilter($storeId)
			->addFieldToFilter('parent_id', $this->getId())
			->setOrder('position', 'ASC');
        if($col != null) {
            $collection->addFieldToFilter('col', $col);
        }
        return $collection;
    }
    
    public function isGroup() {
        if($this->getIsGroup() == 1) {
            return true;
        }
        return false;
    }
    
    public function showTitle() {
        if($this->getShowTitle() == 1) {
            return true;
        }
        return false;
    }
    
    public function isContent() {
        if($this->getIsContent() == 1) {
            return true;
        }
        return false;
    }
    
    public function isRoot() {
	if($this->getParentId() == 0)
	    return true;
	return false;
    }
    
    public function showSub() {
	if($this->getShowSub() == 1) {
		return true;
	}
	return false;
    }
    
    public function getRootId($storeId = null) {
    	if($storeId == null)
    		$storeId = 0;
    	$collection = $this->getCollection()
    				->setStoreFilter($storeId)
    				->addFieldToFilter('parent_id', 0)
    				->addFieldToFilter('level', 0);
    	$data = array();
    	foreach ($collection as $megamenu) {
    		$data[] = $megamenu->getId();
    	}
    	return $data;
    }
    
    public function renderTree($menu=null, $level=0, $activeId, $storeId = null)
    {
    	$html = '';
    	
    	if(!$menu) {
		foreach($this->getRootId($storeId) as $rootId) {
			$menu = $this->load($rootId);
			$html .= $this->renderTree($menu, 0, $activeId, $storeId);
		}
		return $html;
    	}
    	
    	$html .= '<li id="'.$menu->getId().'" class="';
    	if($menu->isRoot() || $level==0) 
    	    $html .= 'root folder-open';
    	$html .= '"><span ';
	if($activeId == $menu->getId())
	    $html .= 'class="active"';
	$html .= '>'.$menu->getTitle().'</span>';
    	
    	if($menu->hasChild(false, $storeId)) {
	    $html .= '<ul>';
	    foreach ($menu->getChildItem(null, $storeId) as $child) {
		$html .= $this->renderTree($child, $level+1, $activeId, $storeId);
	    }
	    $html .= '</ul>';	
    	}
    	$html .= '</li>';
    	return $html;
    }
    
    public function loadByCategoryId($categoryId)
    {
	return $this->getCollection()->addFieldToFilter('article', $categoryId)->getFirstItem();
    }
}