<?php
class Pixafy_Pixvote_Block_Adminhtml_Designtips_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('designtipsGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	
	protected function _prepareCollection()
	{
		$helper = Mage::helper("pixvote");
		$collection = Mage::getModel($helper->DESIGN_TIPS_MODEL_PATH)->getCollection();
		$resource = Mage::getSingleton('core/resource');
		$challengesTable = $resource->getTableName($helper->CHALLENGES_MODEL_PATH);
		$statusTable = $resource->getTableName($helper->STATUS_MODEL_PATH);
		$customerTable = 'customer_entity';
		//$collection->getSelect()->join($customerTable, $customerTable.'.entity_id = main_table.customer_id');
		//$collection->joinAttribute('firstname', 'customer/firstname', 'customer_id');
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
		
		$this->addColumn('text', array(
				'header'	=>	Mage::helper('adminhtml')->__('Tip'),
				'align'		=>	'left',
				'index'		=>	'text',
        ));
		
		$this->addColumn('created_on', array(
                'header'    => Mage::helper('adminhtml')->__('Tip Date'),
                'align'     => 'left',
                'width'     => '180px',
                'type'      => 'date',
				'time'		=> true,
                'default'   => '--',
                'index'     => 'send_date',
		));
//		
//		$this->addColumn('description', array(
//				'header'	=>	Mage::helper('adminhtml')->__('Design Description'),
//				'align'		=>	'left',
//				'index'		=>	'description',
//        ));
//		
//		$this->addColumn('created_on', array(
//                'header'    => Mage::helper('adminhtml')->__('Date Submitted'),
//                'align'     => 'left',
//                'width'     => '180px',
//                'type'      => 'datetime',
//				'time'		=> true,
//                'default'   => '--',
//                'index'     => 'created_on',
//		));

		/*
        $this->addColumn('updated_at', array(
				'header'    => Mage::helper('adminhtml')->__('Update Time'),
				'align'     => 'left',
				'width'     => '180px',
				'type'      => 'datetime',
				'default'   => '--',
				'index'     => 'updated_at',
		));  
		*/
		
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