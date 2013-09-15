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
class MagenThemes_Megamenu_Model_Type extends Varien_Object
{
    protected $_types = array();
    
    public function addTypeModel($name, $model, $title_field) {
        $this->_types[$name] = array('model' => $model, 'title_field' => $title_field);
        return $this;
    }
    
    public function getDropDownMenu($name) {
        $dataType = null;
        $optionArray = array();
        foreach($this->_types as $type => $data) {
            if($type == $name) {
                $dataType = $data;
            }
        }
        
        if($dataType) {
            $collection = Mage::getModel($dataType['model'])->getCollection();
            foreach($collection as $item) {
                $optionArray[] = array('value' => $item->getId(), 'label' => $item->getData[$dataType['title_field']]);
            }
        }
        return $optionArray;
    }
}