<?php
class Pixafy_Pixvote_Block_Adminhtml_Entry_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function _construct()
	{
		parent::_construct();
		$this->setId('entryGrid');
		//$this->setDefaultSort('id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}

	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('entries');
		$helper = Mage::helper("pixvote");
		//Get statuses
		$statuses = Mage::getModel($helper->STATUS_MODEL_PATH)->getCollection()->setOrder('value','ASC');
		
		foreach($statuses as $status)
		{
			$this->getMassactionBlock()->addItem('status-'.$status->getId(), array(
				 'label'    => Mage::helper('pixvote')->__('Change status to '.$status->getValue()),
				 'url'      => $this->getUrl('*/*/statusEdit/id/'.$status->getId()),
				// 'confirm'  => Mage::helper('pixvote')->__('Are you sure?')
			));
		}
        return $this;
    }
	
	
	protected function _prepareCollection()
	{
		$helper = Mage::helper("pixvote");
		$collection = Mage::getModel($helper->DESIGNS_MODEL_PATH)->getCollection();
		$fields = array(
			"id",
			array("design_name" => "name"),
			"price",
			"description",
			"bevel_price",
			"status_id",
		);
		//die((string)$collection->getSelect());
		$resource = Mage::getSingleton('core/resource');
		$challengesTable = $resource->getTableName($helper->CHALLENGES_MODEL_PATH);
		$statusTable = $resource->getTableName($helper->STATUS_MODEL_PATH);
		$sourcesTable = $resource->getTableName($helper->DESIGN_SOURCES_MODEL_PATH);
		$customerTable = 'customer_entity';
        $customerEntityVarChar = 'customer_entity_varchar';
		$firstname = 'firstnameEntity';
		$lastname = 'lastnameEntity';
		$collection->getSelect()->join($challengesTable, $challengesTable.'.id = main_table.challenge_id', array($challengesTable.'.name as challenge'));
		$collection->getSelect()->join($statusTable, $statusTable.'.id = main_table.status_id', array($statusTable.'.value as status'));
		$collection->getSelect()->joinLeft($sourcesTable, $sourcesTable.'.id = main_table.source_id', array($sourcesTable.'.value as source'));
		$collection->getSelect()->join($customerTable, $customerTable.'.entity_id = main_table.customer_id', array($customerTable.'.email'));
        $collection->getSelect()->join(array('firstnameEntity'=>$customerEntityVarChar),'firstnameEntity.entity_id = main_table.customer_id AND firstnameEntity.attribute_id = '.$helper->FIRST_NAME_ATRRIBUTE_ID ,
										array('firstname' => 'firstnameEntity.value'));
        $collection->getSelect()->join(array('lastnameEntity'=>$customerEntityVarChar), 'lastnameEntity.entity_id = main_table.customer_id AND lastnameEntity.attribute_id = '.$helper->LAST_NAME_ATRRIBUTE_ID , 
										array('lastname' => 'lastnameEntity.value'));
		//echo $collection->getSelect()->__toString();
		//exit;
		$this->setCollection($collection);
		$this->addExportType('*/*/exportCsv',$helper->__('CSV'));
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$helper = Mage::helper("pixvote");
		$resource = Mage::getSingleton('core/resource');
		$challengesTable = $resource->getTableName($helper->CHALLENGES_MODEL_PATH);
		$statusTable = $resource->getTableName($helper->STATUS_MODEL_PATH);
		$sourcesTable = $resource->getTableName($helper->DESIGN_SOURCES_MODEL_PATH);
		$customerTable = 'customer_entity';
		$firstname = 'firstnameEntity';
		$lastname = 'lastnameEntity';
//		$this->addColumn('id', array(
//				'header'	=>	Mage::helper('adminhtml')->__('ID'),
//				'align'		=>	'right',
//				'width'		=>	'50px',
//				'index'		=>	'id',
//        ));
		
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
				'header'	=>	Mage::helper('adminhtml')->__('Customer Email'),
				'align'		=>	'left',
				'index'		=>	'email',
				'default'	=>	'---',
				'filter_index' => $customerTable.'.email',
        ));
		
		$this->addColumn('name', array(
				'header'	=>	Mage::helper('adminhtml')->__('Design Name'),
				'align'		=>	'left',
				'index'		=>      'name',
				'filter_index' => 'main_table.name',
        ));
		
		$this->addColumn('price', array(
				'header'	=>	Mage::helper('adminhtml')->__('Designer price'),
				'align'		=>	'left',
				'index'		=>	'price',
        ));
		
		$this->addColumn('bevel_price', array(
				'header'	=>	Mage::helper('adminhtml')->__('Bevel price'),
				'align'		=>	'left',
				'index'		=>	'bevel_price',
        ));
		
		$this->addColumn('description', array(
				'header'	=>	Mage::helper('adminhtml')->__('Design description'),
				'align'		=>	'left',
				'index'		=>	'description',
        ));
		
		$this->addColumn('materials', array(
				'header'	=>	Mage::helper('adminhtml')->__('Design materials'),
				'align'		=>	'left',
				'index'		=>	'materials',
        ));
		
		$this->addColumn('school', array(
				'header'	=>	Mage::helper('adminhtml')->__('School'),
				'align'		=>	'left',
				'index'		=>	'school',
				'default'	=>	'N/A',
        ));
		
		$this->addColumn('source', array(
				'header'	=>	Mage::helper('adminhtml')->__('How did you find us'),
				'align'		=>	'left',
				'index'		=>	'source',
				'default'	=>	'N/A',
				'filter_index' => $sourcesTable.'.value'
        ));
		
		$this->addColumn('challenge', array(
				'header'	=>	Mage::helper('adminhtml')->__('Challenge'),
				'align'		=>	'left',
				'index'		=>	'challenge',
				'filter_index' => $challengesTable.'.name'
        ));
		
		$this->addColumn('status', array(
				'header'	=>	Mage::helper('adminhtml')->__('Entry status'),
				'align'		=>	'left',
				'index'		=>	'status',
				'filter_index' => $statusTable.'.value'
        ));
		
		$this->addColumn('created_on', array(
                'header'    => Mage::helper('adminhtml')->__('Date submitted'),
                'align'     => 'left',
                'width'     => '180px',
                'type'      => 'datetime',
				'time'		=> true,
                'default'   => '--',
                'index'     => 'created_on',
				'filter_index' => 'main_table.created_on',
		));

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