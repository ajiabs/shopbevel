<?php
class Pixafy_Pixvote_Block_Adminhtml_Designsources_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('designsourcesGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	
	protected function _prepareCollection()
	{
		$helper = Mage::helper("pixvote");
		$collection = Mage::getModel($helper->DESIGN_SOURCES_MODEL_PATH)->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{	
		
		$this->addColumn('id', array(
				'header'	=>	Mage::helper('adminhtml')->__('ID'),
				'align'		=>	'left',
				'index'		=>	'id',
        ));
		
		$this->addColumn('value', array(
				'header'	=>	Mage::helper('adminhtml')->__('Source'),
				'align'		=>	'left',
				'index'		=>	'value',
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