<?php

class MagenThemes_Megamenu_Block_Adminhtml_Group_Edit_Tab_Megamenu extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('group_edit_tab_megamenu');
      $this->setDefaultSort('megamenu_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
	  $this->setUseAjax(true);
	  if ($this->getRequest()->getParam('id')) {
		$this->setDefaultFilter(array('megamenus' => 1));
	  }
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('megamenu/megamenu')->getCollection()
					->addFieldToFilter('level',0);
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }
  
  protected function _addColumnFilterToCollection($column)
  {
	  // Set custom filter for in product flag
	  if ($column->getId() == 'megamenus') {
		  $megamenuIds = $this->_getSelectedMegamenus();
		  if (empty($megamenuIds)) {
			  $megamenuIds = 0;
		  }
		  if ($column->getFilter()->getValue()) {
			  $this->getCollection()->addFieldToFilter('megamenu_id', array('in'=>$megamenuIds));
		  } else {
			  if($megamenuIds) {
				  $this->getCollection()->addFieldToFilter('megamenu_id', array('nin'=>$megamenuIds));
			  }
		  }
	  } else {
		  parent::_addColumnFilterToCollection($column);
	  }
	  return $this;
  }

  protected function _prepareColumns()
  {
	  $this->addColumn('megamenus', array(
		  'header_css_class'  => 'a-center',
		  'type'              => 'checkbox',
		  'name'              => 'megamenus',
		  'values'            => $this->_getSelectedMegamenus(),
		  'align'             => 'center',
		  'index'             => 'megamenu_id'
	  ));
	  
      $this->addColumn('megamenu_id', array(
          'header'    => Mage::helper('megamenu')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'megamenu_id',
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
			  'width'         => 200,
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
	  
	  $this->addColumn('sort_order', array(
		  'header'            => Mage::helper('catalog')->__('Sort Order'),
		  'name'              => 'sort_order',
		  'type'              => 'number',
		  'validate_class'    => 'validate-number',
		  'index'             => 'sort_order',
		  'width'             => 60,
		  'editable'          => true,
		  'edit_only'         => $this->getRequest()->getParam('id')
	  ));
	  
      return parent::_prepareColumns();
  }
  
  public function getGridUrl()
  {
	  return $this->getData('grid_url')
		  ? $this->getData('grid_url')
		  : $this->getUrl('*/*/megamenusGrid', array('_current'=>true));
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
  
  private function _loadMegamenus() {
	return Mage::getModel('megamenu/group')->load($this->getRequest()->getParam('id'))->loadMegamenus()->getMegamenus();
  }
  
  private function _getSelectedMegamenus() {
	$megamenus = $this->_loadMegamenus();
	$megamenuArray = array();
	if(count($megamenus)) {
	  foreach($megamenus as $megamenu) {
		$megamenuArray[] = $megamenu['megamenu_id'];
	  }
	}
	return $megamenuArray;
  }
  
  public function getSelectedRelatedMegamenus()
  {
	$megamenus = array();
	$megamenuCollection = $this->_loadMegamenus();
	if(count($megamenuCollection)) {
	  foreach($megamenuCollection as $megamenu) {
		$megamenus[$megamenu['megamenu_id']] = array('sort_order' => $megamenu['sort_order']);
	  }
	}
	return $megamenus;
  }
}