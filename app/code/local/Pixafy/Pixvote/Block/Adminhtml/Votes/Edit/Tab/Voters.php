<?php
class Pixafy_Pixvote_Block_Adminhtml_Votes_Edit_Tab_Voters extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('entryGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	
	protected function _prepareCollection()
	{
		$helper = Mage::helper("pixvote");
		$challenge = Mage::registry($helper->REGISTRY_CHALLENGE_DATA);
		
		if($challenge->getId())
		{
			$resource = Mage::getSingleton('core/resource');
			$designTable = $resource->getTableName($helper->DESIGNS_MODEL_PATH);
			$customerTable = 'customer_entity';
			$customerEntityVarChar = 'customer_entity_varchar';
			$firstname = 'firstnameEntity';
			$lastname = 'lastnameEntity';
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
			$votes->getSelect()->join($customerTable, $customerTable.'.entity_id = main_table.customer_id', array($customerTable.'.email'));
			$votes->getSelect()->join(array('firstnameEntity'=>$customerEntityVarChar),'firstnameEntity.entity_id = main_table.customer_id AND firstnameEntity.attribute_id = '.$helper->FIRST_NAME_ATRRIBUTE_ID ,
										array('firstname' => 'firstnameEntity.value'));
			$votes->getSelect()->join(array('lastnameEntity'=>$customerEntityVarChar), 'lastnameEntity.entity_id = main_table.customer_id AND lastnameEntity.attribute_id = '.$helper->LAST_NAME_ATRRIBUTE_ID , 
										array('lastname' => 'lastnameEntity.value'));
			
			$this->addExportType('*/*/exportCsv',$helper->__('CSV'));
		}
		$this->setCollection($votes);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$firstname = 'firstnameEntity';
		$lastname = 'lastnameEntity';
		
		$this->addColumn('customer_id', array(
				'header'	=>	Mage::helper('adminhtml')->__('Customer ID'),
				'align'		=>	'left',
				'index'		=>	'customer_id',
				'default'	=>	'---'
        ));
   
		$this->addColumn('firstname', array(
				'header'	=>	Mage::helper('adminhtml')->__('First Name'),
				'align'		=>	'left',
				'index'		=>	'firstname',
				'filter_index' => $firstname.'.value'
        ));
		
        $this->addColumn('lastname', array(
				'header'	=>	Mage::helper('adminhtml')->__('Last Name'),
				'align'		=>	'left',
				'index'		=>	'lastname',
				'filter_index' => $lastname.'.value'
        ));
		
		$this->addColumn('email', array(
				'header'	=>	Mage::helper('adminhtml')->__('Voter Emails'),
				'align'		=>	'left',
				'index'		=>	'email',
				'default'	=>	'---',
				//'filter' => false,
        ));
		
		$this->addColumn('created_on', array(
                'header'    => Mage::helper('adminhtml')->__('Day voted'),
                'align'     => 'left',
                'width'     => '180px',
                'type'      => 'datetime',
				'time'		=> true,
                'default'   => '--',
                'index'     => 'created_on',
				'renderer'  => 'Pixafy_Pixvote_Block_Adminhtml_Challenges_Grid_Renderer_Time',
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