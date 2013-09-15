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
class MagenThemes_Megamenu_Block_Adminhtml_Megamenu_New_Form extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    protected $_nodes = array();

    public function __construct()
    {
        parent::__construct();
        $this->addNode('external_link', 'External Link');
        $this->addNode('internal_link', 'Internal Link');
        $this->setTemplate('megamenu/menu/type.phtml');
    }
    
    public function addNode($name, $title, $model=null, $title_field=null, $parent=null) {
        if($this->_isExistsNode($name)) {
            return;
        }
        
        if($parent == 'external_link') {
            return;
        }
        
        $data = new Varien_Object();
        $data->setTitle($title)
            ->setName($name)
            ->setModel($model)
            ->setTitleField($title_field)
            ->setParent($parent);
        $this->_nodes[$name] = $data;
        if($model != null) {
            Mage::getModel('megamenu/type')->addTypeModel($name, $model,$title_field);
        }
        return $this;
    }
    
    public function getRootNodes() {
        $rootNode = array();
        foreach($this->_nodes as $name => $node) {
            if(!$node->getParent()) {
                $rootNode[$name] = $node;
            }
        }
        return $rootNode;
    }
    
    public function getChildNodes($name) {
        $childNodes = array();
        foreach($this->_nodes as $nodeName => $node) {
            if($node->getParent() == $name) {
                $childNodes[$nodeName] = $node;
            }
        }
        return $childNodes;
    }
    
    public function getNodeHtml($node, $select) {
        $html = '';
        if(!count($this->getChildNodes($node->getName()))) {
            $html .= '<option value="'.$node->getName().'" ';
            if($node->getName() == $select) {
                $html .= 'selected="selected"';
            }
            $html .= '>'.$node->getTitle().'</option>';
        } else {
            $html .= '<optgroup label="'.$node->getTitle().'">';
            foreach($this->getChildNodes($node->getName()) as $childNode) {
                $html .= $this->getNodeHtml($childNode, $select);
            }
            $html .= '</optgroup>';
        }
        return $html;
    }
    
    private function _isExistsNode($name) {
        foreach($this->_nodes as $nameNode => $node) {
            if($nameNode == $name) {
                return true;
            }
        }
        return false;
    }
    
    public function nodeToSelectHtml($name, $select = 0, $disabled = false) {
        $nodeData = null;
        $html = '';
        foreach($this->_nodes as $nodeName => $data) {
            if($nodeName == $name) {
                $nodeData = $data;
            }
        }
        
        $collection = Mage::getModel($nodeData->getModel())->getCollection();
        $html .= '<select name="megamenu[article]" id="megamenu[article]" class="input-text required-entry"';
        if($disabled)
            $html .= 'disabled="disabled"';
        $html .= '>';
        $html .= '<option value="">'.$this->__('-- Please Select Article --').'</option>';
        foreach($collection as $option) {
            $type = Mage::getModel($nodeData->getModel())->load($option->getId());
            if($type->getData($nodeData->getTitleField())) {
                if($select) {
                    $selectOption = '';
                    if($option->getId() == $select)
                        $selectOption = 'selected="selected"';
                    $html .= '<option value="'.$option->getId().'" '.$selectOption.'>'.$type->getData($nodeData->getTitleField()).'</option>';
                } else {
                    $html .= '<option value="'.$option->getId().'">'.$type->getData($nodeData->getTitleField()).'</option>';
                }
            }
        }
        $html .= '</select>';
        return $html;
    }
}