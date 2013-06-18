<?php
class Pixafy_Pixvote_Block_Adminhtml_Tracking_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('trackingGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	
	protected function _prepareCollection()
	{
		$pixvoteHelper = Mage::helper("pixvote");
		$collection = Mage::getModel($pixvoteHelper->TRACKING_MODEL_PATH)->getCollection();
		$this->setCollection($collection);
		$this->addExportType('*/*/exportCsv',$pixvoteHelper->__('CSV'));
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('customer_id', array(
				'header'	=>	Mage::helper('adminhtml')->__('Customer ID'),
				'align'		=>	'left',
				'index'		=>	'customer_id',
				'default'	=>	'---',
				'filter_index' => 'customer_id'
				//'filter'	=> false
        ));
		
		$this->addColumn('created_on', array(
                'header'    => Mage::helper('adminhtml')->__('Action Date'),
                'align'     => 'left',
                'width'     => '180px',
				'type'      => 'datetime',
				'index'		=> 'created_on',
				//'gmtoffset' => true
		));
		
		$this->addColumn('subject', array(
				'header'	=>	Mage::helper('adminhtml')->__('A/B'),
				'align'		=>	'left',
				'index'		=>	'subject',
				'default'	=>	'0',
        ));
		
		$this->addColumn('version', array(
				'header'	=>	Mage::helper('adminhtml')->__('Version'),
				'align'		=>	'left',
				'index'		=>	'version',
				'default'	=>	'0',
        ));
		
		$this->addColumn('outcome', array(
				'header'	=>	Mage::helper('adminhtml')->__('Outcome'),
				'align'		=>	'left',
				'index'		=>	'outcome',
				'default'	=>	'0',
        ));
		
		return parent::_prepareColumns();
	}

	public function getRowUrl($row)
	{
		return '#';
	}

	public function getGridUrl()
	{
		return $this->getUrl('*/*/grid', array('_current' => true));
	}
}