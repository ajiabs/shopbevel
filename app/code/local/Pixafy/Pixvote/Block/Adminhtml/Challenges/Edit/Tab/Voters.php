<?php
class Pixafy_Pixvote_Block_Adminhtml_Challenges_Edit_Tab_Voters extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('entryGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		$this->setGridUrl("asdsadadsd");
	}
	
	protected function _prepareCollection()
	{
		$helper = Mage::helper("pixvote");
		$challenge = Mage::registry("challenge_data");
		
		if($challenge->getId())
		{
			$resource = Mage::getSingleton('core/resource');
			$designTable = $resource->getTableName($helper->DESIGNS_MODEL_PATH);
			$customerTable = 'customer_entity';
			$reader = Mage::getSingleton('core/resource')->getConnection('core_read');
			$designs = $reader->fetchAll('select id from '.$designTable.' where status_id not in('.$helper->ENTRY_STATUS_IN_PENDING.','.$helper->ENTRY_STATUS_IN_REJECTED.') and challenge_id = '.$challenge->getId());
			$designIds = array();
			$customerIds = array();
			foreach($designs as $design)
			{
				$designIds[] = $design['id'];
			}
			$votes = Mage::getModel($helper->DESIGN_VOTES_MODEL_PATH)->getCollection()
			->addFieldToFilter('design_id', array('in' => $designIds));
			foreach($votes as $vote)
			{
				if(!isset($customerIds[$vote->getCustomerId()]))
				{
					$customerIds[$vote->getCustomerId()] = $vote->getCustomerId();
				}
			}
			
			$voters = Mage::getResourceModel('customer/customer_collection')
			->addAttributeToFilter('entity_id',array("in" => $customerIds));
			//$votes->getSelect()->join($customerTable, $customerTable.'.entity_id = main_table.customer_id', array($customerTable.'.email'));
			//$votes->getSelect()->group('customer_id');
			$this->addExportType('*/*/exportCsv',$helper->__('CSV'));
		}
		$this->setCollection($voters);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
//		$this->addColumn('id', array(
//				'header'	=>	Mage::helper('adminhtml')->__('ID'),
//				'align'		=>	'right',
//				'width'		=>	'50px',
//				'index'		=>	'id',
//        ));
			
		$this->addColumn('email', array(
				'header'	=>	Mage::helper('adminhtml')->__('Voter Emails'),
				'align'		=>	'left',
				'index'		=>	'email',
				'default'	=>	'---',
				//'filter' => false,
        ));
		
		return parent::_prepareColumns();
	}

	public function getRowUrl($row)
	{
		return 'javascript:void(0)';
	}

	public function getGridUrl()
	{
		return $this->getUrl('*/*/voterGrid', array('_current' => true));
	}
}