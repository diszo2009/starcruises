<?php

class MagenThemes_Megamenu_Block_Adminhtml_Group_Edit_Tab_Group_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('group_parent_grid');
      $this->setDefaultSort('megamenu_group_id');
      $this->setDefaultDir('ASC');
      //$this->setSaveParametersInSession(true);
	  $this->setUseAjax(true);
	  if ($this->_getGroup() && $this->_getGroup()->getId()) {
		$this->setDefaultFilter(array('in_groups'=>1));
	  }
  }

  protected function _prepareCollection()
  {
	  $id = $this->getRequest()->getParam('id');
      $collection = Mage::getModel('megamenu/group')->getCollection();
	  if($id) {
		$collection->addFieldToFilter('megamenu_group_id', array('neq' => $id));
	  }
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }
  
  protected function _addColumnFilterToCollection($column)
  {
	  if ($column->getId() == 'in_groups') {
		  $groupIds = $this->getSelectedGroup();
		  if (empty($groupIds)) {
			  $groupIds = 0;
		  }
		  if ($column->getFilter()->getValue()) {
			  $this->getCollection()->addFieldToFilter('megamenu_group_id', array('in'=>$groupIds));
		  } else {
			  if($groupIds) {
				  $this->getCollection()->addFieldToFilter('megamenu_group_id', array('nin'=>$groupIds));
			  }
		  }
	  } else {
		  parent::_addColumnFilterToCollection($column);
	  }
	  return $this;
  }

  protected function _prepareColumns()
  {
	  $this->addColumn('in_groups', array(
		  'header'    		  => Mage::helper('megamenu')->__(' '),
		  'sortable'          => false,
		  'type'              => 'checkbox',
		  'name'              => 'in_groups',
		  'values'            => $this->getSelectedGroup(),
		  'align'             => 'center',
		  'index'             => 'megamenu_group_id'
	  ));
	  
      $this->addColumn('megamenu_group_id', array(
          'header'    => Mage::helper('megamenu')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'megamenu_group_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('megamenu')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));
	  
	  if (!Mage::app()->isSingleStoreMode()) {
		  $this->addColumn('stores', array(
			  'header'        => Mage::helper('megamenu')->__('Store View'),
			  'index'         => 'stores',
			  'type'          => 'store',
			  'store_all'     => true,
			  'store_view'    => true,
			  'sortable'      => false,
			  'filter_condition_callback' => array($this, '_filterStoreCondition'),
		  ));
	  }

      $this->addColumn('status', array(
          'header'    => Mage::helper('megamenu')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
      return parent::_prepareColumns();
  }
  
  protected function _afterLoadCollection()
  {
	  $this->getCollection()->walk('afterLoad');
	  parent::_afterLoadCollection();
  }

  protected function _filterStoreCondition($collection, $column)
  {
	  if (!$value = $column->getFilter()->getValue()) {
		  return;
	  }
	  
	  $this->getCollection()->addStoreFilter($value);
  }
  
  public function getGridUrl()
  {
	  return $this->getData('grid_url')
		  ? $this->getData('grid_url')
		  : $this->getUrl('*/*/parentsGrid', array('_current'=>true));
  }
  
  private function _getGroup() {
	return Mage::registry('megamenu_group_data');
  }
  
  public function getSelectedGroup() {
	if($this->_getGroup()) {
	  return array($this->_getGroup()->getParentId());
	} else {
	  return null;
	}
  }
  
  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
  
  public function getRowClickCallback() {
	return 'rowClicks';
  }
}