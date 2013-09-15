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
class MagenThemes_Megamenu_Block_Adminhtml_Group_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('groupGrid');
      $this->setDefaultSort('megamenu_group_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('megamenu/group')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
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
	  
	  $this->addColumn('position', array(
          'header'    => Mage::helper('megamenu')->__('Position'),
          'align'     => 'left',
          'width'     => '120px',
		  'align'	  => 'center',
          'index'     => 'position',
          'type'      => 'options',
          'options'   => Mage::getModel('megamenu/resources_position')->toOptionArrayGrid()
      ));
	  
	  if (!Mage::app()->isSingleStoreMode()) {
		  $this->addColumn('stores', array(
			  'header'        => Mage::helper('megamenu')->__('Store View'),
			  'index'         => 'stores',
			  'type'          => 'store',
			  'width'     	  => '150px',
			  'align'		  => 'center',
			  'store_all'     => true,
			  'store_view'    => true,
			  'sortable'      => false,
			  'filter_condition_callback' => array($this, '_filterStoreCondition'),
		  ));
	  }

      $this->addColumn('sort_order', array(
          'header'    => Mage::helper('megamenu')->__('Sort Order'),
          'align'     =>'left',
		  'width'     => '80px',
          'index'     => 'sort_order',
      ));

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
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('megamenu')->__('Action'),
                'width'     => '60',
				'align'		=> 'center',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('megamenu')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('megamenu')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('megamenu')->__('XML'));
	  
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

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('megamenu_group_id');
        $this->getMassactionBlock()->setFormFieldName('megamenu_group');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('megamenu')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('megamenu')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('megamenu/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('megamenu')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('megamenu')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}