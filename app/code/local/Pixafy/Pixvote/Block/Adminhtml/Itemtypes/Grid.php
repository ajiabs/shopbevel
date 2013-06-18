<?php
class Pixafy_Pixvote_Block_Adminhtml_Itemtypes_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('itemTypesGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}

	protected function _prepareCollection()
	{
		$helper = Mage::helper("pixvote");
		$collection = Mage::getModel($helper->ITEM_TYPES_MODEL_PATH)->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('id', array(
				'header'	=>	Mage::helper('adminhtml')->__('ID'),
				'align'		=>	'right',
				'width'		=>	'50px',
				'index'		=>	'id',
        ));
		
		$this->addColumn('name', array(
				'header'	=>	Mage::helper('adminhtml')->__('Name'),
				'align'		=>	'left',
				'index'		=>	'name',
        ));
		
		return parent::_prepareColumns();
	}

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

	public function getGridUrl()
	{
		return $this->getUrl('*/*/grid', array('_current' => true));
	}
}