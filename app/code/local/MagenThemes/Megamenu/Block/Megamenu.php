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
class MagenThemes_Megamenu_Block_Megamenu extends Mage_Core_Block_Template
{
	protected $_types = array();
	
	protected function _construct()
	{
		parent::_construct();
		$this->addType('external_link', null,'megamenu/type_external');
	}
	
	public function addType($type, $model, $block) {
		$data = new Varien_Object();
		$data->setType($type)
			->setModel($model)
			->setBlock($block);
		$this->_types[$type] = $data;
		return $this;
	}
	
	public function getTypes() {
		return $this->_types;
	}
	
	private function _isExistsType($type) {
		foreach($this->_types as $typeName => $data) {
            if($typeName == $type) {
                return true;
            }
        }
        return false;
	}
	
	public function getType($type) {
		if(isset($this->_types[$type])) {
			return $this->_types[$type];
		}
		return null;
	}
}